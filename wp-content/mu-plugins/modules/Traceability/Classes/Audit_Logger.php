<?php
namespace Improvia\Modules\Traceability\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Audit_Logger {

    public static function log( $event_type, $user_id = 0, $object_id = null, $old_value = null, $new_value = null ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'improvia_audit_log';

        // Evitar errores si la tabla aún no se ha creado
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            return false;
        }

        // Convertir valores complejos a JSON
        $old_value_json = ( is_array( $old_value ) || is_object( $old_value ) ) ? wp_json_encode( $old_value ) : $old_value;
        $new_value_json = ( is_array( $new_value ) || is_object( $new_value ) ) ? wp_json_encode( $new_value ) : $new_value;

        // Capturar IP
        $ip_address = self::get_user_ip();

        // Determinar ID del usuario realizador (si no se proporciona y hay sesion activa)
        if ( ! $user_id && is_user_logged_in() ) {
            $user_id = get_current_user_id();
        }

        $wpdb->insert(
            $table_name,
            [
                'event_type' => sanitize_text_field( $event_type ),
                'user_id'    => absint( $user_id ),
                'object_id'  => $object_id ? absint( $object_id ) : null,
                'old_value'  => $old_value_json,
                'new_value'  => $new_value_json,
                'ip_address' => sanitize_text_field( $ip_address ),
                'created_at' => current_time( 'mysql', 1 ) // Hora GMT
            ],
            [
                '%s', '%d', '%d', '%s', '%s', '%s', '%s'
            ]
        );

        return $wpdb->insert_id;
    }

    private static function get_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        }
    }
}
