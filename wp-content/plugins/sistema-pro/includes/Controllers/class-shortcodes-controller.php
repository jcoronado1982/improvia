<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_Shortcodes_Controller {

    public function __construct() {
        // Registrar Shortcodes
        add_shortcode( 'sop_menu_lateral', array( $this, 'render_sidebar_menu' ) );
        add_shortcode( 'sop_hero_landing', array( $this, 'render_hero_landing' ) );
        add_shortcode( 'sop_lista_entrenadores', array( $this, 'render_trainer_list' ) );
        add_shortcode( 'sop_lista_especialistas', array( $this, 'render_specialist_list' ) );
        add_shortcode( 'sop_mock_checkout', array( $this, 'render_mock_checkout' ) );
        add_shortcode( 'sop_detalle_entrenador', array( $this, 'render_trainer_detail' ) );
        add_shortcode( 'sop_suscripciones', array( $this, 'render_subscriptions' ) );
        add_shortcode( 'sop_solicitudes', array( $this, 'render_solicitudes' ) );
        add_shortcode( 'sop_mensajes', array( $this, 'render_messaging' ) );
        add_shortcode( 'sop_qa', array( $this, 'render_qa' ) );
        
        // Registrar Shortcode de Layout Envolvente
        add_shortcode( 'sop_layout', array( $this, 'render_generic_layout' ) );

        // Registrar Shortcode de Tabs de Perfil
        add_shortcode( 'sop_perfil_tabs', array( $this, 'render_profile_tabs' ) );
    }

    /**
     * Renderiza el menú lateral mediante shortcode
     */
    public function render_sidebar_menu() {
        $current_slug = get_post_field( 'post_name', get_the_ID() );
        
        $items = array(
            'perfil'        => array( 'label' => __( 'Perfil', 'sistema-pro' ), 'icon' => 'user_white.png' ),
            'mensajes'      => array( 'label' => __( 'Mensajes', 'sistema-pro' ), 'icon' => 'mail.png', 'dot' => true ),
            'entrenadores'  => array( 'label' => __( 'Entrenadores', 'sistema-pro' ), 'icon' => 'zap.png' ),
            'especialistas' => array( 'label' => __( 'Especialistas', 'sistema-pro' ), 'icon' => 'award.png' ),
            'qa'            => array( 'label' => __( 'Q&A', 'sistema-pro' ), 'icon' => 'help-circle.png' ),
        );

        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-sidebar-menu.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza las pestañas de navegación en el perfil
     */
    public function render_profile_tabs() {
        $user = wp_get_current_user();
        
        ob_start();
        
        $view_path = SOP_PATH . 'includes/Views/view-profile-tabs.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file no encontrada para las pestañas de perfil.</p>';
        }

        return ob_get_clean();
    }

    /**
     * Renderiza un layout genérico 25/75 con menú lateral
     * Uso: [sop_layout] Contenido [/sop_layout]
     */
    public function render_generic_layout( $atts, $content = null ) {
        $menu = $this->render_sidebar_menu();
        
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-generic-layout.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>' . esc_html__( 'Error: Layout view not found.', 'sistema-pro' ) . '</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza la lista de entrenadores
     */
    public function render_trainer_list( $atts ) {
        $atts = shortcode_atts( array(
            'role' => 'entrenador',
        ), $atts, 'sop_lista_entrenadores' );

        $role = sanitize_text_field( $atts['role'] );

        // Pagination logic
        $current_page = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
        $per_page     = 8;
        $offset       = ($current_page - 1) * $per_page;

        // Meta Query: Debe tener al menos un precio configurado
        $meta_query = array(
            'relation' => 'OR'
        );

        $price_fields = array(
            'sop_precio_semanal', 'sop_precio_mensual', 'sop_precio_trimestral', 'sop_precio_anual',
            'sop_precio_sesiones_1', 'sop_precio_sesiones_2', 'sop_precio_sesiones_3',
            'sop_precio_sesiones_4', 'sop_precio_sesiones_5', 'sop_precio_sesiones_6'
        );

        foreach ( $price_fields as $field ) {
            $meta_query[] = array(
                'key'     => $field,
                'value'   => 0,
                'compare' => '>',
                'type'    => 'NUMERIC'
            );
        }

        // Get total count for filtered users
        $count_args = array(
            'role'       => $role,
            'meta_query' => $meta_query,
            'count_total' => true,
            'fields'     => 'ID',
        );
        $user_query = new WP_User_Query( $count_args );
        $total_trainers = $user_query->get_total();
        $total_pages    = ceil($total_trainers / $per_page);

        // Fetch limited set of trainees/specialists
        $trainers = get_users( array( 
            'role'       => $role,
            'number'     => $per_page,
            'offset'     => $offset,
            'meta_query' => $meta_query
        ) );
        
        ob_start();
        
        // Pass the $trainers variable to the view
        $view_path = SOP_PATH . 'includes/Views/view-trainer-directory.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }

        return ob_get_clean();
    }

    /**
     * Renderiza la lista de especialistas (copia de la lógica de entrenadores)
     */
    public function render_specialist_list( $atts ) {
        $atts = shortcode_atts( array(
            'role' => 'entrenador', // Placeholder: Use trainers until specialists are registered
        ), $atts, 'sop_lista_especialistas' );

        $role = sanitize_text_field( $atts['role'] );

        // Paginación
        $current_page = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
        $per_page     = 8;
        $offset       = ($current_page - 1) * $per_page;

        // Meta Query: Debe tener al menos un precio configurado
        $meta_query = array( 'relation' => 'OR' );
        $price_fields = array(
            'sop_precio_semanal', 'sop_precio_mensual', 'sop_precio_trimestral', 'sop_precio_anual',
            'sop_precio_sesiones_1', 'sop_precio_sesiones_2', 'sop_precio_sesiones_3',
            'sop_precio_sesiones_4', 'sop_precio_sesiones_5', 'sop_precio_sesiones_6'
        );
        foreach ( $price_fields as $field ) {
            $meta_query[] = array( 'key' => $field, 'value' => 0, 'compare' => '>', 'type' => 'NUMERIC' );
        }

        // Conteo total
        $count_args = array( 'role' => $role, 'meta_query' => $meta_query, 'count_total' => true, 'fields' => 'ID' );
        $user_query = new WP_User_Query( $count_args );
        $total_trainers = $user_query->get_total();
        $total_pages    = ceil($total_trainers / $per_page);

        // Fetch de usuarios
        $trainers = get_users( array( 
            'role'       => $role,
            'number'     => $per_page,
            'offset'     => $offset,
            'meta_query' => $meta_query
        ) );
        
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-specialist-directory.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza el contenido dinámico del Hero en la Landing
     */
    public function render_hero_landing() {
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-hero-landing.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza la vista del checkout simulado
     */
    public function render_mock_checkout() {
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-mock-checkout.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza la vista del detalle público del entrenador
     */
    public function render_trainer_detail() {
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-trainer-detail.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza la vista del panel de Suscripciones
     */
    public function render_subscriptions() {
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-subscriptions.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza la vista del panel de Solicitudes
     */
    public function render_solicitudes() {
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-solicitudes.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza la vista del panel de Mensajería
     */
    public function render_messaging() {
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-messaging.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Renderiza la vista del panel de QA
     */
    public function render_qa() {
        ob_start();
        $view_path = SOP_PATH . 'includes/Views/view-qa.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

}
