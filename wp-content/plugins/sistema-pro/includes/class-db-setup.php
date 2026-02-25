<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_DB_Setup {

    public function __construct() {
        // Asegurar consistencia en cada carga (init)
        add_action( 'init', array( $this, 'ensure_system_state' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
    }

    /**
     * Crea un menú dedicado en el administrador
     */
    public function register_admin_menu() {
        // El menú principal ahora apunta directamente a Idiomas para evitar el 404
        add_menu_page(
            'Sistema PRO',
            'Sistema PRO',
            'manage_options',
            'edit-tags.php?taxonomy=sop_idioma',
            '',
            'dashicons-admin-generic',
            30
        );

        // Submenús (el primero suele repetirse para que aparezca en el desplegable)
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Idiomas', 'Idiomas', 'manage_options', 'edit-tags.php?taxonomy=sop_idioma' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Niveles', 'Niveles de Idioma', 'manage_options', 'edit-tags.php?taxonomy=sop_nivel' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Nacionalidades', 'Nacionalidades', 'manage_options', 'edit-tags.php?taxonomy=sop_nacionalidad' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Ubicaciones', 'Ubicaciones', 'manage_options', 'edit-tags.php?taxonomy=sop_ubicacion' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Pierna', 'Pierna Dominante', 'manage_options', 'edit-tags.php?taxonomy=sop_pierna' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Altura', 'Altura', 'manage_options', 'edit-tags.php?taxonomy=sop_altura' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Peso', 'Peso', 'manage_options', 'edit-tags.php?taxonomy=sop_peso' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'Nivel Prof.', 'Niveles Prof.', 'manage_options', 'edit-tags.php?taxonomy=sop_nivel_prof' );
        add_submenu_page( 'edit-tags.php?taxonomy=sop_idioma', 'RRSS', 'Redes Sociales', 'manage_options', 'edit-tags.php?taxonomy=sop_red_social' );
    }

    /**
     * Lógica disparada al activar el plugin
     */
    public static function activate() {
        $setup = new self();
        $setup->ensure_system_state();
        flush_rewrite_rules();
    }

    /**
     * Verifica y recrea el estado si es necesario
     */
    public function ensure_system_state() {
        $this->create_roles();
        $this->create_pages();
        $this->create_taxonomies();
        $this->seed_data();
    }

    /**
     * Registra taxonomías para organizar datos de perfil
     */
    private function create_taxonomies() {
        $taxonomies = array(
            'sop_idioma'       => 'Idiomas',
            'sop_nivel'        => 'Niveles de Idioma',
            'sop_nacionalidad' => 'Nacionalidades',
            'sop_ubicacion'    => 'Ubicaciones',
            'sop_pierna'       => 'Pierna Dominante',
            'sop_altura'       => 'Alturas (cm)',
            'sop_peso'         => 'Pesos (kg)',
            'sop_nivel_prof'   => 'Niveles Profesionales',
            'sop_red_social'   => 'Redes Sociales'
        );

        foreach ( $taxonomies as $slug => $label ) {
            if ( ! taxonomy_exists( $slug ) ) {
                register_taxonomy( $slug, null, array(
                    'label'        => $label,
                    'rewrite'      => array( 'slug' => $slug ),
                    'hierarchical' => true,
                    'show_ui'      => true,
                    'show_in_rest' => true,
                    'show_in_menu' => false,
                ) );
            }
        }
    }

    /**
     * Puebla las taxonomías con datos iniciales
     */
    private function seed_data() {
        // Idiomas
        $idiomas = array('Inglés', 'Español', 'Francés', 'Alemán', 'Italiano', 'Portugués');
        foreach ($idiomas as $i) {
            if (!term_exists($i, 'sop_idioma')) wp_insert_term($i, 'sop_idioma');
        }

        // Niveles
        $niveles = array('Nativo', 'Bilingüe', 'Avanzado (C1/C2)', 'Intermedio (B1/B2)', 'Básico (A1/A2)');
        foreach ($niveles as $n) {
            if (!term_exists($n, 'sop_nivel')) wp_insert_term($n, 'sop_nivel');
        }

        // Nacionalidades
        $nacionalidades = array('España', 'México', 'Colombia', 'Argentina', 'Chile', 'Ecuador', 'Perú', 'EE.UU.', 'Reino Unido');
        foreach ($nacionalidades as $nac) {
            if (!term_exists($nac, 'sop_nacionalidad')) wp_insert_term($nac, 'sop_nacionalidad');
        }

        // Ubicaciones
        $ubicaciones = array('Madrid', 'Barcelona', 'Ciudad de México', 'Bogotá', 'Buenos Aires', 'Santiago');
        foreach ($ubicaciones as $ub) {
            if (!term_exists($ub, 'sop_ubicacion')) wp_insert_term($ub, 'sop_ubicacion');
        }

        // Pierna Dominante
        $piernas = array('Derecha', 'Izquierda', 'Ambidiestro');
        foreach ($piernas as $p) {
            if (!term_exists($p, 'sop_pierna')) wp_insert_term($p, 'sop_pierna');
        }

        // Alturas (ejemplos)
        $alturas = array('160', '165', '170', '175', '180', '185', '190');
        foreach ($alturas as $a) {
            if (!term_exists($a, 'sop_altura')) wp_insert_term($a, 'sop_altura');
        }

        // Pesos (ejemplos)
        $pesos = array('60', '65', '70', '75', '80', '85', '90');
        foreach ($pesos as $pes) {
            if (!term_exists($pes, 'sop_peso')) wp_insert_term($pes, 'sop_peso');
        }

        // Niveles Profesionales
        $niveles_prof = array('Master', 'Senior', 'Junior', 'Amateur');
        foreach ($niveles_prof as $np) {
            if (!term_exists($np, 'sop_nivel_prof')) wp_insert_term($np, 'sop_nivel_prof');
        }

        // Redes Sociales
        $rrss = array('Instagram', 'Facebook', 'Twitter', 'LinkedIn', 'YouTube', 'TikTok');
        foreach ($rrss as $red) {
            if (!term_exists($red, 'sop_red_social')) wp_insert_term($red, 'sop_red_social');
        }
    }

    /**
     * Crea los roles de negocio
     */
    private function create_roles() {
        if ( ! get_role( 'entrenador' ) ) {
            add_role( 'entrenador', 'Entrenador', array( 'read' => true ) );
        }
        if ( ! get_role( 'atleta' ) ) {
            add_role( 'atleta', 'Atleta', array( 'read' => true ) );
        }
        if ( ! get_role( 'especialista' ) ) {
            add_role( 'especialista', 'Especialista', array( 'read' => true ) );
        }
        // Mantener deportista por compatibilidad si fuera necesario, o ignorarlo
    }

    /**
     * Crea las páginas programáticamente
     */
    private function create_pages() {
        $pages = array(
            'Home' => array(
                'slug'    => 'home',
                'content' => '<!-- wp:heading {"level":1} --><h1>Bienvenido a nuestra Landing Page</h1><!-- /wp:heading --><!-- wp:paragraph --><p>Esta es la página pública del sistema. Aquí puedes conocer nuestros servicios.</p><!-- /wp:paragraph --><!-- wp:shortcode -->[formulario_acceso]<!-- /wp:shortcode -->'
            ),
            'Login' => array(
                'slug'    => 'login',
                'content' => '<!-- wp:paragraph --><p>Por favor, identifícate para continuar.</p><!-- /wp:paragraph --><!-- wp:shortcode -->[formulario_acceso]<!-- /wp:shortcode -->'
            ),
            'Registro' => array(
                'slug'    => 'registro',
                'content' => '<!-- wp:heading {"level":1} --><h1>Crea tu cuenta</h1><!-- /wp:heading --><!-- wp:shortcode -->[formulario_registro]<!-- /wp:shortcode -->'
            ),
            'Suscripcion' => array(
                'slug'    => 'suscripcion',
                'content' => '[sop_layout]<h2>Suscripción</h2><p>Estado de tu suscripción actual.</p>[/sop_layout]'
            ),
            'Perfil' => array(
                'slug'    => 'perfil',
                'content' => '[sop_layout][/sop_layout]'
            ),
            'Mensajes' => array(
                'slug'    => 'mensajes',
                'content' => '[sop_layout]<h2>Mis Mensajes</h2><p>Bandeja de entrada y notificaciones.</p>[/sop_layout]'
            ),
            'Entrenadores' => array(
                'slug'    => 'entrenadores',
                'content' => '[sop_layout][sop_lista_entrenadores][/sop_layout]'
            ),
            'Especialistas' => array(
                'slug'    => 'especialistas',
                'content' => '[sop_layout]<h2>Especialistas</h2><p>Directorio de especialistas técnicos.</p>[/sop_layout]'
            ),
            'QA' => array(
                'slug'    => 'qa',
                'content' => '[sop_layout]<h2>Q&A</h2><p>Preguntas frecuentes y soporte.</p>[/sop_layout]'
            ),
        );

        foreach ( $pages as $title => $data ) {
            if ( ! get_page_by_path( $data['slug'] ) ) {
                wp_insert_post( array(
                    'post_title'   => $title,
                    'post_content' => $data['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $data['slug']
                ) );
            }
        }
    }
}
