<?php
/**
 * Plugin Name: Sistema Pro
 * Description: Sistema modular para gestión de entrenadores y deportistas.
 * Version: 1.0.0
 * Author: Desarrollador Senior
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes del sistema
define( 'SOP_PATH', plugin_dir_path( __FILE__ ) );
define( 'SOP_URL', plugin_dir_url( __FILE__ ) );

// Autocarga de módulos
require_once SOP_PATH . 'includes/class-db-setup.php';
require_once SOP_PATH . 'includes/class-auth.php';
require_once SOP_PATH . 'includes/class-router.php';
require_once SOP_PATH . 'includes/class-ui.php';
require_once SOP_PATH . 'includes/class-i18n.php';

/**
 * Inicialización del Sistema Operativo Pro
 */
function sop_init_system() {
    load_plugin_textdomain( 'sistema-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    
    new SOP_DB_Setup();
    new SOP_Auth();
    new SOP_Router();
    new SOP_UI();
    new SOP_I18n();
}

add_action( 'plugins_loaded', 'sop_init_system' );

// Registro de activación
register_activation_hook( __FILE__, array( 'SOP_DB_Setup', 'activate' ) );
