<?php
/**
 * Clase: SOP_Debug
 * Utilidad centralizada para trazabilidad y depuración.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_Debug {

    /**
     * Escribe un mensaje de log en wp-content/debug.log
     * 
     * @param string $context El contexto del log (ej: AUTH, UI, DB)
     * @param string $message El mensaje a registrar
     * @param mixed  $data    Datos opcionales para serializar en JSON
     */
    public static function log( $context, $message, $data = null ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        $user_id = get_current_user_id();
        $user_tag = $user_id ? "[User #$user_id]" : "[Guest]";
        $timestamp = date( 'Y-m-d H:i:s' );
        
        $formatted_msg = sprintf(
            "[SOP] [%s] [%s] %s %s",
            $timestamp,
            strtoupper( $context ),
            $user_tag,
            $message
        );

        if ( $data !== null ) {
            $formatted_msg .= " | DATA: " . json_encode( $data, JSON_UNESCAPED_UNICODE );
        }

        error_log( $formatted_msg );
    }
}
