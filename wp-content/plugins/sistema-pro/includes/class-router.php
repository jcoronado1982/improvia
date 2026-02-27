<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_Router {

    public function __construct() {
        // Manejar redirecciones de frontend
        add_action( 'template_redirect', array( $this, 'handle_frontend_access' ) );
        
        // Bloquear wp-admin
        add_action( 'admin_init', array( $this, 'restrict_admin_access' ) );
    }

    /**
     * Reglas de redirección para el frontend
     */
    public function handle_frontend_access() {
        $private_pages = array( 'suscripcion', 'perfil', 'mensajes', 'entrenadores', 'especialistas', 'qa', 'checkout-simulado', 'detalle-entrenador', 'solicitudes' );
        
        // 1. Si intenta entrar a cualquier página privada sin estar logueado, rebotar a /home
        if ( is_page( $private_pages ) && ! is_user_logged_in() ) {
            wp_safe_redirect( home_url( '/home' ) );
            exit;
        }

        // 2. Si es un rol de negocio (entrenador, atleta, especialista) y está en /home o /identificacion, enviar a /suscripcion
        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $restricted_roles = array( 'entrenador', 'atleta', 'especialista', 'deportista' );
            
            if ( array_intersect( $restricted_roles, (array) $user->roles ) ) {
                if ( is_page( 'identificacion' ) || is_page( 'registro' ) || is_page( 'login' ) ) {
                    if ( in_array( 'entrenador', (array) $user->roles ) || in_array( 'especialista', (array) $user->roles ) ) {
                        wp_safe_redirect( home_url( '/mensajes' ) );
                    } elseif ( in_array( 'deportista', (array) $user->roles ) || in_array( 'atleta', (array) $user->roles ) ) {
                        wp_safe_redirect( home_url( '/entrenadores' ) );
                    } else {
                        wp_safe_redirect( home_url( '/suscripcion' ) );
                    }
                    exit;
                }
            }
        }
    }

    /**
     * Bloquea el acceso al panel /wp-admin para roles específicos
     */
    public function restrict_admin_access() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        $user = wp_get_current_user();
        if ( ! $user || ! $user->exists() ) {
            return;
        }

        $restricted_roles = array( 'entrenador', 'atleta', 'especialista', 'deportista' );
        
        if ( array_intersect( $restricted_roles, (array) $user->roles ) ) {
            wp_safe_redirect( home_url( '/home' ) );
            exit;
        }
    }
}
