<?php
namespace Improvia\Modules\Traceability\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Improvia\Modules\Traceability\Classes\Audit_Logger;

class Event_Listeners {

    public function __construct() {
        $this->register_auth_hooks();
        $this->register_crud_hooks();
    }

    private function register_auth_hooks() {
        add_action( 'wp_login', [ $this, 'log_login_success' ], 10, 2 );
        add_action( 'wp_login_failed', [ $this, 'log_login_failed' ] );
        add_action( 'clear_auth_cookie', [ $this, 'log_logout' ] );
    }

    private function register_crud_hooks() {
        add_action( 'post_updated', [ $this, 'log_post_updated' ], 10, 3 );
        add_action( 'deleted_post', [ $this, 'log_post_deleted' ], 10, 2 );
        add_action( 'profile_update', [ $this, 'log_user_update' ], 10, 2 );
        add_action( 'user_register', [ $this, 'log_user_register' ] );
        add_action( 'delete_user', [ $this, 'log_user_deleted' ], 10, 2 );
    }

    public function log_login_success( $user_login, $user ) {
        Audit_Logger::log( 'user_login', $user->ID, null, null, null );
    }

    public function log_login_failed( $username ) {
        Audit_Logger::log( 'user_login_failed', 0, null, null, [ 'attempted_username' => $username ] );
    }

    public function log_logout() {
        if ( is_user_logged_in() ) {
            Audit_Logger::log( 'user_logout', get_current_user_id(), null, null, null );
        }
    }

    public function log_post_updated( $post_ID, $post_after, $post_before ) {
        // Ignorar auto-guardados y revisiones
        if ( wp_is_post_revision( $post_ID ) || $post_after->post_status === 'auto-draft' ) {
            return;
        }

        // Solo guardar si existen diferencias notables (excluyendo fechas de modificación auto)
        $diff = array_diff_assoc( (array) $post_after, (array) $post_before );
        unset( $diff['post_modified'], $diff['post_modified_gmt'] );

        if ( empty( $diff ) ) {
            return; // Sin cambios reales
        }

        Audit_Logger::log( 'post_updated', 0, $post_ID, $post_before, $post_after );
    }

    public function log_post_deleted( $post_id, $post ) {
        if ( wp_is_post_revision( $post_id ) ) return;
        Audit_Logger::log( 'post_deleted', 0, $post_id, $post, null );
    }

    public function log_user_update( $user_id, $old_user_data ) {
        $new_user_data = get_userdata( $user_id );
        Audit_Logger::log( 'user_updated', 0, $user_id, $old_user_data, $new_user_data );
    }

    public function log_user_register( $user_id ) {
        $new_user_data = get_userdata( $user_id );
        Audit_Logger::log( 'user_registered', 0, $user_id, null, $new_user_data );
    }

    public function log_user_deleted( $user_id, $reassign ) {
        $deleted_user = get_userdata( $user_id );
        Audit_Logger::log( 'user_deleted', 0, $user_id, $deleted_user, [ 'reassigned_to' => $reassign ] );
    }
}
