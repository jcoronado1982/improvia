<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_UI {

    public function __construct() {
        // Inyectar CSS
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // Inyectar Header
        add_action( 'wp_body_open', array( $this, 'render_header' ) );
        
        // Inyectar Footer
        add_action( 'wp_footer', array( $this, 'render_footer' ) );

        // Agregar clase dinámica al body para el Tema Claro de Proveedores
        add_filter( 'body_class', array( $this, 'add_provider_body_class' ) );

        // Cargar Controladores MVC
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Controllers/class-shortcodes-controller.php';
        new SOP_Shortcodes_Controller();

        // Manejar actualización de perfil vía AJAX
        add_action( 'wp_ajax_sop_update_profile', array( $this, 'handle_profile_update' ) );
        add_action( 'wp_ajax_nopriv_sop_update_profile', array( $this, 'handle_profile_update' ) );
    }

    /**
     * Procesa la actualización del perfil del usuario
     */
    public function handle_profile_update() {
        // WordPress por defecto usa '_wpnonce' si no se especifica nombre en wp_nonce_field
        if ( ! check_ajax_referer( 'sop_profile_nonce', 'nonce', false ) ) {
            wp_send_json_error( 'Error de seguridad (Nonce inválido)' );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( 'Usuario no identificado' );

        // Actualizar nombre visible
        if ( isset( $_POST['display_name'] ) ) {
            wp_update_user( array( 'ID' => $user_id, 'display_name' => sanitize_text_field( $_POST['display_name'] ) ) );
        }

        // Guardar metadatos individuales
        $meta_fields = array( 
            'sop_ubicacion_id', 'sop_nacionalidad_id', 'sop_fecha_nacimiento',
            'sop_pierna_id', 'sop_altura_id', 'sop_peso_id', 'sop_nivel_prof_id',
            'sop_prof_description'
        );
        foreach ( $meta_fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                update_user_meta( $user_id, $field, sanitize_text_field( $_POST[$field] ) );
            }
        }

        // Guardar idiomas
        if ( isset( $_POST['sop_idiomas'] ) ) {
            $idiomas = json_decode( stripslashes( $_POST['sop_idiomas'] ), true );
            update_user_meta( $user_id, 'sop_idiomas_data', $idiomas );
        }

        // Guardar RRSS (Redes Sociales)
        if ( isset( $_POST['sop_rrss'] ) ) {
            $rrss = json_decode( stripslashes( $_POST['sop_rrss'] ), true );
            update_user_meta( $user_id, 'sop_rrss_data', $rrss );
        }

        wp_send_json_success( 'Perfil actualizado correctamente' );
    }

    /**
     * Encola estilos básicos del sistema de forma modular
     */
    public function enqueue_assets() {
        // Encolar CSS base
        wp_enqueue_style( 'sop-base-style', SOP_URL . 'assets/css/base.css', array(), '1.0.0' );

        // Cargar automáticamente TODOS los archivos CSS modulares
        $css_dir = SOP_PATH . 'assets/css/';
        $css_files = array_merge(
            glob( $css_dir . 'layout/*.css' ),
            glob( $css_dir . 'components/*.css' )
        );

        foreach ( $css_files as $file ) {
            $filename = basename( $file, '.css' );
            $url = SOP_URL . str_replace( SOP_PATH, '', $file );
            wp_enqueue_style( 'sop-' . $filename, $url, array('sop-base-style'), '1.0.0' );
        }
        
        // Encolar JS
        wp_enqueue_script( 'sop-settings-js', SOP_URL . 'assets/js/settings.js', array('jquery'), '1.0.0', true );
        wp_localize_script( 'sop-settings-js', 'sop_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'sop_save_pref_nonce' )
        ) );
    }

    /**
     * Los métodos de shortcode y layout han sido migrados a SOP_Shortcodes_Controller.
     */

    /**
     * Renderiza el header global dinámico
     */
    public function render_header() {
        $view_path = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Views/view-global-header.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
    }

    /**
     * Renderiza el footer global
     */
    public function render_footer() {
        $view_path = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Views/view-global-footer.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
    }

    /**
     * Agrega una clase CSS al body si el usuario es Entrenador o Especialista
     */
    public function add_provider_body_class( $classes ) {
        if ( is_user_logged_in() ) {
            if ( current_user_can( 'entrenador' ) || current_user_can( 'especialista' ) ) {
                $classes[] = 'sop-is-provider';
                // Solo agregar el tema claro si NO estamos en previsualización
                $classes[] = 'sop-provider-theme-light';
            } elseif ( current_user_can( 'atleta' ) || current_user_can( 'deportista' ) ) {
                $classes[] = 'sop-is-atleta';
            }
        }
        return $classes;
    }
}
