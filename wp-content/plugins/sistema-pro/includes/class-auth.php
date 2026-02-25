<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_Auth {

    public function __construct() {
        add_shortcode( 'formulario_acceso', array( $this, 'render_form' ) );
        add_shortcode( 'formulario_registro', array( $this, 'render_registration_form' ) );
        add_action( 'init', array( $this, 'handle_login' ) );
        add_action( 'init', array( $this, 'handle_registration' ) );
        add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );
        add_action( 'after_setup_theme', array( $this, 'hide_admin_bar' ) );
    }

    /**
     * Oculta la barra de administración en el frontend (para todos los usuarios)
     */
    public function hide_admin_bar() {
        if ( ! is_admin() ) {
            show_admin_bar( false );
        }
    }

    /**
     * Redirige al usuario a la home tras cerrar sesión
     */
    public function redirect_after_logout() {
        wp_safe_redirect( home_url( '/home' ) );
        exit;
    }

    private $reg_errors = array();

    /**
     * Renderiza el formulario de registro
     */
    public function render_registration_form() {
        if ( is_user_logged_in() ) {
            return '<div class="sop-alert">Ya tienes una cuenta activa.</div>';
        }

        ob_start();
        $view_path = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Views/view-register.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Procesa el registro de usuario
     */
    public function handle_registration() {
        if ( ! isset( $_POST['sop_register_submit'] ) || ! isset( $_POST['sop_reg_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['sop_reg_nonce'], 'sop_register_action' ) ) {
            $this->reg_errors[] = 'Error de seguridad detectado. Por favor intenta de nuevo.';
            return;
        }

        $user_login = sanitize_user( $_POST['reg_user'] );
        $user_email = sanitize_email( $_POST['reg_email'] );
        $user_pass  = $_POST['reg_pass'];
        $user_role  = sanitize_text_field( $_POST['reg_role'] );
        $user_name  = sanitize_text_field( $_POST['reg_nombre'] );
        $user_tel   = sanitize_text_field( $_POST['reg_tel'] );

        // Validaciones
        if ( empty( $user_login ) || empty( $user_email ) || empty( $user_pass ) ) {
            $this->reg_errors[] = 'Todos los campos son obligatorios.';
            return;
        }

        if ( username_exists( $user_login ) ) {
            $this->reg_errors[] = 'Este nombre de usuario ya está registrado.';
        }

        if ( email_exists( $user_email ) ) {
            $this->reg_errors[] = 'Este correo electrónico ya está registrado.';
        }

        if ( strlen( $user_pass ) < 6 ) {
            $this->reg_errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        if ( ! empty( $this->reg_errors ) ) {
            return;
        }

        $user_id = wp_create_user( $user_login, $user_pass, $user_email );

        if ( is_wp_error( $user_id ) ) {
            $this->reg_errors[] = $user_id->get_error_message();
            return;
        }

        // Actualizar nombre y rol
        wp_update_user( array(
            'ID'           => $user_id,
            'display_name' => $user_name,
            'role'         => $user_role
        ) );

        // Guardar metadatos
        update_user_meta( $user_id, 'first_name', $user_name );
        update_user_meta( $user_id, 'sop_telefono', $user_tel );

        // Auto-login
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id );
        
        // Redirección basada en rol
        $user_obj = get_userdata( $user_id );
        if ( in_array( 'entrenador', (array) $user_obj->roles ) || in_array( 'especialista', (array) $user_obj->roles ) ) {
            wp_safe_redirect( home_url( '/mensajes' ) );
        } elseif ( in_array( 'deportista', (array) $user_obj->roles ) || in_array( 'atleta', (array) $user_obj->roles ) ) {
            wp_safe_redirect( home_url( '/entrenadores' ) );
        } else {
            wp_safe_redirect( home_url( '/suscripcion' ) );
        }
        exit;
    }

    /**
     * Renderiza el formulario de acceso
     */
    public function render_form() {
        if ( is_user_logged_in() ) {
            return '<div class="sop-alert">Ya has iniciado sesión. <a href="' . wp_logout_url( home_url( '/home' ) ) . '">Cerrar Sesión</a></div>';
        }

        ob_start();
        $view_path = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Views/view-login.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Procesa el login
     */
    public function handle_login() {
        if ( ! isset( $_POST['sop_login_submit'] ) || ! isset( $_POST['sop_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['sop_nonce'], 'sop_login_action' ) ) {
            wp_die( 'Error de seguridad detectado.' );
        }

        $creds = array(
            'user_login'    => sanitize_user( $_POST['sop_log'] ),
            'user_password' => $_POST['sop_pwd'],
            'remember'      => true
        );

        $user = wp_signon( $creds, false );

        if ( is_wp_error( $user ) ) {
            // Se podría añadir un error en el formulario aquí
            return;
        }

        // Redirección dinámica basada en el rol
        $user_obj = get_userdata( $user->ID );
        if ( in_array( 'entrenador', (array) $user_obj->roles ) || in_array( 'especialista', (array) $user_obj->roles ) ) {
            wp_safe_redirect( home_url( '/mensajes' ) );
        } elseif ( in_array( 'deportista', (array) $user_obj->roles ) || in_array( 'atleta', (array) $user_obj->roles ) ) {
            wp_safe_redirect( home_url( '/entrenadores' ) );
        } else {
            wp_safe_redirect( home_url( '/suscripcion' ) );
        }
        exit;
    }
}
