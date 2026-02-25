<?php
namespace Improvia\Modules\Plans;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plans_Module {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
	}

	public function register_cpt() {
		$labels = [
			'name'               => _x( 'Plans', 'post type general name', 'improvia' ),
			'singular_name'      => _x( 'Plan', 'post type singular name', 'improvia' ),
			'menu_name'          => _x( 'Plans', 'admin menu', 'improvia' ),
			'name_admin_bar'     => _x( 'Plan', 'add new on admin bar', 'improvia' ),
			'add_new'            => _x( 'Add New', 'plan', 'improvia' ),
			'add_new_item'       => __( 'Add New Plan', 'improvia' ),
			'new_item'           => __( 'New Plan', 'improvia' ),
			'edit_item'          => __( 'Edit Plan', 'improvia' ),
			'view_item'          => __( 'View Plan', 'improvia' ),
			'all_items'          => __( 'All Plans', 'improvia' ),
			'search_items'       => __( 'Search Plans', 'improvia' ),
			'not_found'          => __( 'No plans found.', 'improvia' ),
			'not_found_in_trash' => __( 'No plans found in Trash.', 'improvia' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true, // Accessible via frontend/API
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'plan' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'supports'           => [ 'title' ], // We'll use ACF for other fields
			'show_in_graphql'    => true, // Expose to WPGraphQL
			'graphql_single_name' => 'Plan',
			'graphql_plural_name' => 'Plans',
		];

		register_post_type( 'plan', $args );
	}
}
