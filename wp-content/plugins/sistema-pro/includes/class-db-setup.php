<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_DB_Setup {

    public function __construct() {
        // Registrar taxonomías en cada carga (init) para que estén disponibles en frontend y admin
        add_action( 'init', array( $this, 'create_taxonomies' ) );
        
        // Ejecutar check de sistema (siembra, roles, páginas) solo en el admin
        add_action( 'admin_init', array( $this, 'ensure_system_state' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

        // Columnas personalizadas en la lista de usuarios
        add_filter( 'manage_users_columns', array( $this, 'add_user_status_column' ) );
        add_action( 'manage_users_custom_column', array( $this, 'render_user_status_column' ), 10, 3 );
    }

    /**
     * Versión de los datos del sistema. Incrementar este número para forzar 
     * una nueva siembra de datos (ej. si añadimos nuevos países).
     */
    const SOP_DATA_VERSION = '1.0.1';

    /**
     * Agrega la columna "Estado" a la lista de usuarios
     */
    public function add_user_status_column( $columns ) {
        $columns['sop_status'] = 'Estado';
        return $columns;
    }

    /**
     * Renderiza el contenido de la columna "Estado"
     */
    public function render_user_status_column( $output, $column_name, $user_id ) {
        if ( 'sop_status' !== $column_name ) {
            return $output;
        }

        $status = intval( get_user_meta( $user_id, 'sop_user_status', true ) );
        
        switch ( $status ) {
            case 1:
                return '<span style="color: #6b7280; font-weight: bold;">Inscrito</span>';
            case 2:
                return '<span style="color: #f59e0b; font-weight: bold;">Aprobado en proceso</span>';
            case 3:
                $label = ( user_can( $user_id, 'deportista' ) || user_can( $user_id, 'atleta' ) ) ? 'Suscrito' : 'Aprobado';
                return '<span style="color: #10b981; font-weight: bold;">' . $label . '</span>';
            default:
                return '<span style="color: #9ca3af; font-style: italic;">Sin estado</span>';
        }
    }

    public function register_admin_menu() {
        // (Contenido del menú omitido por brevedad)
        // El menú principal ahora apunta a los logs de simulación como página inicial por defecto
        add_menu_page(
            'Sistema PRO',
            'Sistema PRO',
            'manage_options',
            'sop_mock_logs',
            array( $this, 'render_mock_logs_page' ),
            'dashicons-admin-generic',
            30
        );

        // SECCIÓN: GESTIÓN
        add_submenu_page( 'sop_mock_logs', '--- GESTIÓN ---', '<span style="color:#aaa;font-weight:bold;text-transform:uppercase;font-size:10px;">—— GESTIÓN ——</span>', 'manage_options', '#' );
        add_submenu_page( 'sop_mock_logs', 'Suscripciones', 'Suscripciones (Real)', 'manage_options', 'edit.php?post_type=subscription' );
        add_submenu_page( 'sop_mock_logs', 'Suscripciones Sim.', 'Suscripciones Sim.', 'manage_options', 'sop_mock_logs' );
        
        // SECCIÓN: DATOS GLOBALES
        add_submenu_page( 'sop_mock_logs', '--- GLOBALES ---', '<span style="color:#aaa;font-weight:bold;text-transform:uppercase;font-size:10px;">—— GLOBALES ——</span>', 'manage_options', '#' );
        add_submenu_page( 'sop_mock_logs', 'Idiomas', 'Idiomas', 'manage_options', 'edit-tags.php?taxonomy=sop_idioma' );
        add_submenu_page( 'sop_mock_logs', 'Niveles Idioma', 'Niveles Idioma', 'manage_options', 'edit-tags.php?taxonomy=sop_nivel' );
        add_submenu_page( 'sop_mock_logs', 'Nacionalidades', 'Nacionalidades', 'manage_options', 'edit-tags.php?taxonomy=sop_nacionalidad' );
        add_submenu_page( 'sop_mock_logs', 'Ubicaciones', 'Ubicaciones', 'manage_options', 'edit-tags.php?taxonomy=sop_ubicacion' );
        add_submenu_page( 'sop_mock_logs', 'Países', 'Países', 'manage_options', 'edit-tags.php?taxonomy=sop_pais' );
        add_submenu_page( 'sop_mock_logs', 'Redes Sociales', 'Redes Sociales', 'manage_options', 'edit-tags.php?taxonomy=sop_red_social' );

        // SECCIÓN: DATOS ATLETA
        add_submenu_page( 'sop_mock_logs', '--- ATLETA ---', '<span style="color:#aaa;font-weight:bold;text-transform:uppercase;font-size:10px;">—— ATLETA ——</span>', 'manage_options', '#' );
        add_submenu_page( 'sop_mock_logs', 'Pierna', 'Pierna Dominante', 'manage_options', 'edit-tags.php?taxonomy=sop_pierna' );
        add_submenu_page( 'sop_mock_logs', 'Altura', 'Altura', 'manage_options', 'edit-tags.php?taxonomy=sop_altura' );
        add_submenu_page( 'sop_mock_logs', 'Peso', 'Peso', 'manage_options', 'edit-tags.php?taxonomy=sop_peso' );
        add_submenu_page( 'sop_mock_logs', 'Nivel Prof.', 'Niveles Prof.', 'manage_options', 'edit-tags.php?taxonomy=sop_nivel_prof' );
        add_submenu_page( 'sop_mock_logs', 'Categorías', 'Categorías', 'manage_options', 'edit-tags.php?taxonomy=sop_categoria' );

        // SECCIÓN: DATOS STAFF (COACH/ESPECIALISTA)
        add_submenu_page( 'sop_mock_logs', '--- STAFF ---', '<span style="color:#aaa;font-weight:bold;text-transform:uppercase;font-size:10px;">—— STAFF ——</span>', 'manage_options', '#' );
        add_submenu_page( 'sop_mock_logs', 'Ocupación', 'Ocupaciones', 'manage_options', 'edit-tags.php?taxonomy=sop_ocupacion' );
        add_submenu_page( 'sop_mock_logs', 'Experiencia', 'Experiencia', 'manage_options', 'edit-tags.php?taxonomy=sop_experiencia' );
        add_submenu_page( 'sop_mock_logs', 'Títulos', 'Títulos de Estudio', 'manage_options', 'edit-tags.php?taxonomy=sop_titulo' );
        add_submenu_page( 'sop_mock_logs', 'Institutos', 'Institutos', 'manage_options', 'edit-tags.php?taxonomy=sop_instituto' );
        add_submenu_page( 'sop_mock_logs', 'Lugares', 'Lugares de Estudio', 'manage_options', 'edit-tags.php?taxonomy=sop_lugar_estudio' );
        add_submenu_page( 'sop_mock_logs', 'Tipo Título', 'Tipos de Título', 'manage_options', 'edit-tags.php?taxonomy=sop_tipo_titulo' );
        add_submenu_page( 'sop_mock_logs', 'Certificaciones', 'Certificaciones', 'manage_options', 'edit-tags.php?taxonomy=sop_certificacion' );
        add_submenu_page( 'sop_mock_logs', 'Posiciones', 'Posiciones', 'manage_options', 'edit-tags.php?taxonomy=sop_posicion' );
        add_submenu_page( 'sop_mock_logs', 'Fase Ofensiva', 'Fases Ofensivas', 'manage_options', 'edit-tags.php?taxonomy=sop_fase_ofensiva' );
        add_submenu_page( 'sop_mock_logs', 'Fase Defensiva', 'Fases Defensivas', 'manage_options', 'edit-tags.php?taxonomy=sop_fase_defensiva' );
    }

    /**
     * Renderiza la página de logs de pago sumulados
     */
    public function render_mock_logs_page() {
        $view_path = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Views/view-admin-mock-logs.php';
        if ( file_exists( $view_path ) ) {
            require $view_path;
        } else {
            echo '<div class="wrap"><p>No se encontró la vista de logs.</p></div>';
        }
    }

    /**
     * Lógica disparada al activar el plugin
     */
    public static function activate() {
        $setup = new self();
        $setup->create_taxonomies(); // Registrar para poder sembrar
        $setup->ensure_system_state( true ); // Forzar ejecución al activar
        flush_rewrite_rules();
    }

    /**
     * Verifica y recrea el estado si es necesario
     * 
     * @param bool $force_seed Si es true, ignora el check de versión
     */
    public function ensure_system_state( $force_seed = false ) {
        // Solo ejecutar si estamos en el admin o se fuerza
        if ( ! is_admin() && ! $force_seed ) {
            return;
        }

        $installed_version = get_option( 'sop_data_version', '0.0.0' );

        // Si la versión ha cambiado o se fuerza, ejecutar siembra
        if ( version_compare( $installed_version, self::SOP_DATA_VERSION, '<' ) || $force_seed ) {
            $this->create_roles();
            $this->create_pages();
            // create_taxonomies() ya se llama en 'init', no es necesario aquí
            $this->seed_data();
            
            update_option( 'sop_data_version', self::SOP_DATA_VERSION );
            SOP_Debug::log( 'DB_SETUP', 'System state ensured and seeded', [ 'version' => self::SOP_DATA_VERSION ] );
        }
    }

    /**
     * Registra taxonomías para organizar datos de perfil
     */
    public function create_taxonomies() {
        $taxonomies = array(
            // Personal Info
            'sop_idioma'         => 'Idiomas',
            'sop_nivel'          => 'Niveles de Idioma',
            'sop_nacionalidad'   => 'Nacionalidades',
            'sop_ubicacion'      => 'Ubicaciones',
            // Athlete Professional Info
            'sop_pierna'         => 'Pierna Dominante',
            'sop_altura'         => 'Alturas (cm)',
            'sop_peso'           => 'Pesos (kg)',
            'sop_nivel_prof'     => 'Niveles Profesionales',
            'sop_red_social'     => 'Redes Sociales',
            // Coach/Specialist Professional Info
            'sop_ocupacion'      => 'Ocupaciones',
            'sop_experiencia'    => 'Experiencia',
            'sop_titulo'         => 'Títulos de Estudio',
            'sop_instituto'      => 'Institutos',
            'sop_lugar_estudio'  => 'Lugares de Estudio',
            'sop_tipo_titulo'    => 'Tipos de Título',
            'sop_pais'           => 'Países',
            'sop_certificacion'  => 'Certificaciones',
            'sop_posicion'       => 'Posiciones',
            'sop_fase_ofensiva'  => 'Fases Ofensivas',
            'sop_fase_defensiva' => 'Fases Defensivas',
            'sop_categoria'      => 'Categorías'
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
     * Puebla las taxonomías con datos iniciales desde un archivo JSON
     */
    private function seed_data() {
        $json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'assets/data/seed-data.json';
        
        if ( ! file_exists( $json_path ) ) {
            SOP_Debug::log( 'DB_SETUP', 'Seed data JSON not found', [ 'path' => $json_path ] );
            return;
        }

        $json_content = file_get_contents( $json_path );
        $data = json_decode( $json_content, true );

        if ( ! is_array( $data ) ) {
            SOP_Debug::log( 'DB_SETUP', 'Invalid seed data JSON content' );
            return;
        }

        foreach ( $data as $taxonomy => $terms ) {
            if ( ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }

            foreach ( $terms as $term ) {
                if ( ! term_exists( $term, $taxonomy ) ) {
                    wp_insert_term( $term, $taxonomy );
                }
            }
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
            'Solicitudes' => array(
                'slug'    => 'solicitudes',
                'content' => '[sop_layout][sop_solicitudes][/sop_layout]'
            ),
            'Perfil' => array(
                'slug'    => 'perfil',
                'content' => '[sop_layout][/sop_layout]'
            ),
            'Mensajes' => array(
                'slug'    => 'mensajes',
                'content' => '[sop_layout][sop_mensajes][/sop_layout]'
            ),
            'Entrenadores' => array(
                'slug'    => 'entrenadores',
                'content' => '[sop_layout][sop_lista_entrenadores][/sop_layout]'
            ),
            'Especialistas' => array(
                'slug'    => 'especialistas',
                'content' => '[sop_layout][sop_lista_entrenadores role="especialista"][/sop_layout]'
            ),
            'QA' => array(
                'slug'    => 'qa',
                'content' => '[sop_layout]<h2>Q&A</h2><p>Preguntas frecuentes y soporte.</p>[/sop_layout]'
            ),
            'Checkout Simulado' => array(
                'slug'    => 'checkout-simulado',
                'content' => '[sop_mock_checkout]'
            ),
            'Detalle Entrenador' => array(
                'slug'    => 'detalle-entrenador',
                'content' => '[sop_layout][sop_detalle_entrenador][/sop_layout]'
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
