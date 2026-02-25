<?php
namespace Improvia\Modules\Subscriptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Subscriptions_Module {

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
			'name'               => _x( 'Subscriptions', 'post type general name', 'improvia' ),
			'singular_name'      => _x( 'Subscription', 'post type singular name', 'improvia' ),
			'menu_name'          => _x( 'Subscriptions', 'admin menu', 'improvia' ),
			'name_admin_bar'     => _x( 'Subscription', 'add new on admin bar', 'improvia' ),
			'add_new'            => _x( 'Add New', 'subscription', 'improvia' ),
			'add_new_item'       => __( 'Add New Subscription', 'improvia' ),
			'new_item'           => __( 'New Subscription', 'improvia' ),
			'edit_item'          => __( 'Edit Subscription', 'improvia' ),
			'view_item'          => __( 'View Subscription', 'improvia' ),
			'all_items'          => __( 'All Subscriptions', 'improvia' ),
			'search_items'       => __( 'Search Subscriptions', 'improvia' ),
			'not_found'          => __( 'No subscriptions found.', 'improvia' ),
			'not_found_in_trash' => __( 'No subscriptions found in Trash.', 'improvia' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => false, // Subscriptions are internal records, not public pages
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'subscription' ],
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 6,
			'supports'           => [ 'title', 'custom-fields' ], 
			'show_in_graphql'    => true, // Exposed to API for authenticated users/admins
			'graphql_single_name' => 'Subscription',
			'graphql_plural_name' => 'Subscriptions',
		];

		register_post_type( 'subscription', $args );
	}
}
