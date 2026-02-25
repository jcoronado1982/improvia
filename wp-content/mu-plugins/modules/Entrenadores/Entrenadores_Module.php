<?php
namespace Improvia\Modules\Entrenadores;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Entrenadores_Module {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
		add_action( 'acf/init', [ $this, 'register_fields' ] );
		add_action( 'graphql_register_types', [ $this, 'register_graphql_fields' ] );
	}

	public function register_cpt() {
		$labels = [
			'name'               => _x( 'Entrenadores', 'post type general name', 'improvia' ),
			'singular_name'      => _x( 'Entrenador', 'post type singular name', 'improvia' ),
			'menu_name'          => _x( 'Entrenadores', 'admin menu', 'improvia' ),
			'name_admin_bar'     => _x( 'Entrenador', 'add new on admin bar', 'improvia' ),
			'add_new'            => _x( 'Add New', 'entrenador', 'improvia' ),
			'add_new_item'       => __( 'Add New Entrenador', 'improvia' ),
			'new_item'           => __( 'New Entrenador', 'improvia' ),
			'edit_item'          => __( 'Edit Entrenador', 'improvia' ),
			'view_item'          => __( 'View Entrenador', 'improvia' ),
			'all_items'          => __( 'All Entrenadores', 'improvia' ),
			'search_items'       => __( 'Search Entrenadores', 'improvia' ),
			'not_found'          => __( 'No entrenadores found.', 'improvia' ),
			'not_found_in_trash' => __( 'No entrenadores found in Trash.', 'improvia' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'entrenador' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 7,
			'supports'           => [ 'title' ],
			'show_in_graphql'    => true,
			'graphql_single_name' => 'entrenador',
			'graphql_plural_name' => 'entrenadores',
		];

		register_post_type( 'entrenador', $args );
	}

	public function register_fields() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group( [
				'key' => 'group_entrenador_fields',
				'title' => 'Datos del Entrenador',
				'fields' => [
					[
						'key' => 'field_entrenador_nombre_completo',
						'label' => 'Nombre Completo',
						'name' => 'nombre_completo',
						'type' => 'text',
						'required' => 1,
						'show_in_graphql' => 1,
					],
				],
				'location' => [
					[
						[
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'entrenador',
						],
					],
				],
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'show_in_graphql' => 1,
				'graphql_field_name' => 'entrenadorFields',
			] );
		}
	}

	public function register_graphql_fields() {
		// Register the 'EntrenadorFields' type
		register_graphql_object_type( 'EntrenadorFields', [
			'description' => __( 'Campos del Entrenador', 'improvia' ),
			'fields'      => [
				'nombreCompleto' => [
					'type'        => 'String',
					'description' => __( 'Nombre Completo del Entrenador', 'improvia' ),
					'resolve'     => function( $post ) {
						return get_field( 'nombre_completo', $post->ID );
					},
				],
			],
		] );

		// Add the 'entrenadorFields' field to the 'Entrenador' post type
		register_graphql_field( 'Entrenador', 'entrenadorFields', [
			'type'        => 'EntrenadorFields',
			'description' => __( 'Datos adicionales del entrenador', 'improvia' ),
			'resolve'     => function( $post ) {
				return $post; // Pass the post object to the resolved fields
			},
		] );
	}
}
