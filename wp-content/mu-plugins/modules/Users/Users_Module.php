<?php
namespace Improvia\Modules\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Users_Module {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'register_roles' ] );
		add_action( 'init', [ $this, 'register_creator_cpt' ] );
		add_action( 'init', [ $this, 'register_user_taxonomy' ] );
		add_action( 'show_user_profile', [ $this, 'add_custom_user_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'add_custom_user_fields' ] );
		add_action( 'personal_options_update', [ $this, 'save_custom_user_fields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'save_custom_user_fields' ] );
		add_action( 'user_register', [ $this, 'generate_unique_user_id' ] );
		add_action( 'graphql_register_types', [ $this, 'register_graphql_user_fields' ] );
        
        // Add custom column to Users table
        add_filter( 'manage_users_columns', [ $this, 'add_user_type_column' ] );
        add_filter( 'manage_users_custom_column', [ $this, 'show_user_type_column_content' ], 10, 3 );
	}

    public function add_user_type_column( $columns ) {
        $columns['user_type'] = __( 'Tipo de Usuario', 'improvia' );
        return $columns;
    }

    public function show_user_type_column_content( $val, $column_name, $user_id ) {
        if ( 'user_type' === $column_name ) {
            $terms = wp_get_object_terms( $user_id, 'user_type' );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $term_names = array_map( function( $term ) {
                    return $term->name;
                }, $terms );
                return implode( ', ', $term_names );
            }
            return '—';
        }
        return $val;
    }

	public function register_roles() {
		add_role( 'creator', __( 'Creator', 'improvia' ), [ 'read' => true, 'edit_posts' => true, 'upload_files' => true ] );
		add_role( 'specialist', __( 'Specialist', 'improvia' ), [ 'read' => true ] );
		
		$subscriber = get_role( 'subscriber' );
		if ( $subscriber ) {
			$subscriber->add_cap( 'view_premium_content' );
		}
	}

	public function register_user_taxonomy() {
		register_taxonomy( 'user_type', 'user', [
			'public' => true,
			'labels' => [
				'name' => __( 'User Types', 'improvia' ),
				'singular_name' => __( 'User Type', 'improvia' ),
			],
			'show_ui' => true,
			'show_in_graphql' => true,
			'graphql_single_name' => 'userType',
			'graphql_plural_name' => 'userTypes',
		] );

		// Seed terms if they don't exist
		$terms = [ 'Entrenador', 'Especialista', 'Deportista' ];
		foreach ( $terms as $term ) {
			if ( ! term_exists( $term, 'user_type' ) ) {
				wp_insert_term( $term, 'user_type' );
			}
		}
	}

	public function add_custom_user_fields( $user ) {
		?>
		<h3><?php _e( 'Improvia Platform Info', 'improvia' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="gender"><?php _e( 'Gender', 'improvia' ); ?></label></th>
				<td>
					<select name="gender" id="gender">
						<option value="male" <?php selected( get_user_meta( $user->ID, 'gender', true ), 'male' ); ?>>Masculino</option>
						<option value="female" <?php selected( get_user_meta( $user->ID, 'gender', true ), 'female' ); ?>>Femenino</option>
						<option value="other" <?php selected( get_user_meta( $user->ID, 'gender', true ), 'other' ); ?>>Otro</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label><?php _e( 'Unique ID', 'improvia' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( get_user_meta( $user->ID, 'unique_id', true ) ); ?>" disabled />
					<p class="description">Generated automatically on registration.</p>
				</td>
			</tr>
		</table>
		<?php
	}

	public function save_custom_user_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'gender', $_POST['gender'] );
	}

	public function generate_unique_user_id( $user_id ) {
		$unique_id = 'IMP-' . strtoupper( wp_generate_password( 8, false ) );
		update_user_meta( $user_id, 'unique_id', $unique_id );
	}

	public function register_graphql_user_fields() {
		register_graphql_field( 'User', 'gender', [
			'type' => 'String',
			'description' => __( 'User gender', 'improvia' ),
			'resolve' => function( $user ) {
				return get_user_meta( $user->ID, 'gender', true );
			}
		] );

		register_graphql_field( 'User', 'uniqueId', [
			'type' => 'String',
			'description' => __( 'Platform unique ID', 'improvia' ),
			'resolve' => function( $user ) {
				return get_user_meta( $user->ID, 'unique_id', true );
			}
		] );

		// Extend RegisterUserInput
		add_filter( 'graphql_input_fields', function( $fields, $type_name ) {
			if ( 'RegisterUserInput' === $type_name ) {
				$fields['gender'] = [
					'type' => 'String',
					'description' => __( 'User gender', 'improvia' ),
				];
				$fields['userType'] = [
					'type' => 'Int',
					'description' => __( 'User type category ID', 'improvia' ),
				];
			}
			return $fields;
		}, 10, 2 );

		// Handle data during registration
		add_action( 'graphql_user_object_mutation_update_additional_data', function( $user_id, $input, $mutation_name, $context, $info ) {
			if ( isset( $input['gender'] ) ) {
				update_user_meta( $user_id, 'gender', $input['gender'] );
			}
            if ( isset( $input['userType'] ) ) {
                wp_set_object_terms( $user_id, (int) $input['userType'], 'user_type', false );
            }
        }, 10, 5 );
	}

	public function register_creator_cpt() {
		$labels = [
			'name'               => _x( 'Creators', 'post type general name', 'improvia' ),
			'singular_name'      => _x( 'Creator', 'post type singular name', 'improvia' ),
			'menu_name'          => _x( 'Creators', 'admin menu', 'improvia' ),
			'name_admin_bar'     => _x( 'Creator', 'add new on admin bar', 'improvia' ),
			'add_new'            => _x( 'Add New', 'creator', 'improvia' ),
			'add_new_item'       => __( 'Add New Creator', 'improvia' ),
			'new_item'           => __( 'New Creator', 'improvia' ),
			'edit_item'          => __( 'Edit Creator', 'improvia' ),
			'view_item'          => __( 'View Creator', 'improvia' ),
			'all_items'          => __( 'All Creators', 'improvia' ),
			'search_items'       => __( 'Search Creators', 'improvia' ),
			'not_found'          => __( 'No creators found.', 'improvia' ),
			'not_found_in_trash' => __( 'No creators found in Trash.', 'improvia' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'creator' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'supports'           => [ 'title', 'editor', 'thumbnail' ], // Basic fields
			'show_in_graphql'    => true,
			'graphql_single_name' => 'Creator',
			'graphql_plural_name' => 'Creators',
		];

		register_post_type( 'creator', $args );
	}
}
