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
        add_shortcode( 'sop_mock_checkout', array( $this, 'render_mock_checkout' ) );
        add_shortcode( 'sop_detalle_entrenador', array( $this, 'render_trainer_detail' ) );
        add_shortcode( 'sop_suscripciones', array( $this, 'render_subscriptions' ) );
        
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
        $view_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/Views/view-sidebar-menu.php';
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
        
        $view_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/Views/view-profile-tabs.php';
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
        
        $html = '<div class="sop-cols-container">';
        $html .= '<div class="sop-col-left">' . $menu . '</div>';
        $html .= '<div class="sop-col-right">' . do_shortcode( $content ) . '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Renderiza la lista de entrenadores
     */
    public function render_trainer_list() {
        // Pagination logic
        $current_page = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
        $per_page     = 8;
        $offset       = ($current_page - 1) * $per_page;

        // Get total count for trainers
        $user_counts = count_users();
        $total_trainers = isset($user_counts['avail_roles']['entrenador']) ? $user_counts['avail_roles']['entrenador'] : 0;
        $total_pages    = ceil($total_trainers / $per_page);

        // Fetch limited set of trainers
        $trainers = get_users( array( 
            'role'   => 'entrenador',
            'number' => $per_page,
            'offset' => $offset
        ) );
        
        ob_start();
        
        // Pass the $trainers variable to the view
        $view_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/Views/view-trainer-directory.php';
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
        $view_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/Views/view-hero-landing.php';
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
        $view_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/Views/view-mock-checkout.php';
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
        $view_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/Views/view-trainer-detail.php';
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
        $view_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/Views/view-subscriptions.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<p>Error: View file not found.</p>';
        }
        return ob_get_clean();
    }

}
