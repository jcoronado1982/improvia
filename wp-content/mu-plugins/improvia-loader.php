<?php
/**
 * Plugin Name: Improvia Platform Core
 * Description: Core functionality for the Improvia platform (Users, Plans, Subscriptions).
 * Version: 1.0.0
 * Author: Improvia Dev Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define platform constants
define( 'IMPROVIA_PATH', plugin_dir_path( __FILE__ ) );
define( 'IMPROVIA_URL', plugin_dir_url( __FILE__ ) );

// Load the Core Module which handles autoloading and initialization
require_once IMPROVIA_PATH . 'modules/Core/Core_Module.php';

// Initialize the platform
\Improvia\Modules\Core\Core_Module::instance();
