<?php
namespace Improvia\Modules\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Core_Module {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load_dependencies();
		$this->init_modules();
		$this->enable_graphql_introspection();
	}

	private function enable_graphql_introspection() {
		add_filter( 'graphql_is_introspection_allowed', function( $is_allowed, $request_context ) {
			return true;
		}, 99, 2 );
	}

	private function load_dependencies() {
		// Autoload other modules here if needed or use a PSR-4 autoloader
		// For simplicity, we'll manually require module files for now as we build them out.
		
		// Load Traceability Module
		if ( file_exists( IMPROVIA_PATH . 'modules/Traceability/Traceability_Module.php' ) ) {
			require_once IMPROVIA_PATH . 'modules/Traceability/Traceability_Module.php';
		}

		// Load Users Module
		if ( file_exists( IMPROVIA_PATH . 'modules/Users/Users_Module.php' ) ) {
			require_once IMPROVIA_PATH . 'modules/Users/Users_Module.php';
		}

		// Load Plans Module
		if ( file_exists( IMPROVIA_PATH . 'modules/Plans/Plans_Module.php' ) ) {
			require_once IMPROVIA_PATH . 'modules/Plans/Plans_Module.php';
		}

		// Load Subscriptions Module
		if ( file_exists( IMPROVIA_PATH . 'modules/Subscriptions/Subscriptions_Module.php' ) ) {
			require_once IMPROVIA_PATH . 'modules/Subscriptions/Subscriptions_Module.php';
		}

		// Load Content Module
		if ( file_exists( IMPROVIA_PATH . 'modules/Content/Content_Module.php' ) ) {
			require_once IMPROVIA_PATH . 'modules/Content/Content_Module.php';
		}

		// Load Entrenadores Module
		if ( file_exists( IMPROVIA_PATH . 'modules/Entrenadores/Entrenadores_Module.php' ) ) {
			require_once IMPROVIA_PATH . 'modules/Entrenadores/Entrenadores_Module.php';
		}
	}

	private function init_modules() {
		// Initialize the modules
        if ( class_exists( '\Improvia\Modules\Traceability\Traceability_Module' ) ) {
			\Improvia\Modules\Traceability\Traceability_Module::instance();
		}

		if ( class_exists( '\Improvia\Modules\Users\Users_Module' ) ) {
			\Improvia\Modules\Users\Users_Module::instance();
		}
		
		if ( class_exists( '\Improvia\Modules\Plans\Plans_Module' ) ) {
			\Improvia\Modules\Plans\Plans_Module::instance();
		}

		if ( class_exists( '\Improvia\Modules\Subscriptions\Subscriptions_Module' ) ) {
			\Improvia\Modules\Subscriptions\Subscriptions_Module::instance();
		}

		if ( class_exists( '\Improvia\Modules\Content\Content_Module' ) ) {
			\Improvia\Modules\Content\Content_Module::instance();
		}

		if ( class_exists( '\Improvia\Modules\Entrenadores\Entrenadores_Module' ) ) {
			\Improvia\Modules\Entrenadores\Entrenadores_Module::instance();
		}
	}
}
