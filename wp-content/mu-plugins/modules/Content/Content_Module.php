<?php
namespace Improvia\Modules\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Content_Module {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'register_cpts' ] );
	}

	public function register_cpts() {
		$this->register_session_cpt();
		$this->register_program_cpt();
		$this->register_resource_cpt();
	}

	private function register_session_cpt() {
		$labels = [
			'name'          => _x( 'Sessions', 'post type general name', 'improvia' ),
			'singular_name' => _x( 'Session', 'post type singular name', 'improvia' ),
			'menu_name'     => _x( 'Sessions', 'admin menu', 'improvia' ),
			'all_items'     => __( 'All Sessions', 'improvia' ),
			'add_new'       => _x( 'Add New', 'session', 'improvia' ),
		];

		register_post_type( 'session', [
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'show_in_graphql'     => true,
			'graphql_single_name' => 'Session',
			'graphql_plural_name' => 'Sessions',
			'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
			'menu_icon'           => 'dashicons-video-alt3',
			'menu_position'       => 20,
		] );
	}

	private function register_program_cpt() {
		$labels = [
			'name'          => _x( 'Programs', 'post type general name', 'improvia' ),
			'singular_name' => _x( 'Program', 'post type singular name', 'improvia' ),
			'menu_name'     => _x( 'Programs', 'admin menu', 'improvia' ),
			'all_items'     => __( 'All Programs', 'improvia' ),
			'add_new'       => _x( 'Add New', 'program', 'improvia' ),
		];

		register_post_type( 'program', [
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'show_in_graphql'     => true,
			'graphql_single_name' => 'Program',
			'graphql_plural_name' => 'Programs',
			'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
			'menu_icon'           => 'dashicons-playlist-video',
			'menu_position'       => 21,
		] );
	}

	private function register_resource_cpt() {
		$labels = [
			'name'          => _x( 'Resources', 'post type general name', 'improvia' ),
			'singular_name' => _x( 'Resource', 'post type singular name', 'improvia' ),
			'menu_name'     => _x( 'Resources', 'admin menu', 'improvia' ),
			'all_items'     => __( 'All Resources', 'improvia' ),
			'add_new'       => _x( 'Add New', 'resource', 'improvia' ),
		];

		register_post_type( 'resource', [
			'labels'              => $labels,
			'public'              => true, // Accessible via API/Frontend
			'show_in_rest'        => true,
			'show_in_graphql'     => true,
			'graphql_single_name' => 'Resource',
			'graphql_plural_name' => 'Resources',
			'supports'            => [ 'title', 'editor' ], // File download managed via ACF
			'menu_icon'           => 'dashicons-download',
			'menu_position'       => 22,
		] );
	}
}
