<?php
namespace Improvia\Modules\Traceability;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'Classes/Audit_Logger.php';
require_once plugin_dir_path( __FILE__ ) . 'Classes/Event_Listeners.php';

use Improvia\Modules\Traceability\Classes\Audit_Logger;
use Improvia\Modules\Traceability\Classes\Event_Listeners;

class Traceability_Module {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->init_hooks();
        $this->create_audit_table();
	}

	private function init_hooks() {
        // Inicializar listeners de eventos de auditoria
        new Event_Listeners();
	}

    private function create_audit_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'improvia_audit_log';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            user_id bigint(20) NOT NULL,
            object_id bigint(20) DEFAULT NULL,
            old_value longtext DEFAULT NULL,
            new_value longtext DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY event_type (event_type),
            KEY user_id (user_id),
            KEY object_id (object_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
