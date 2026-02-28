<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_DB_Setup {

    public function __construct() {
        // Asegurar consistencia en cada carga (init)
        add_action( 'init', array( $this, 'ensure_system_state' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

        // Columnas personalizadas en la lista de usuarios
        add_filter( 'manage_users_columns', array( $this, 'add_user_status_column' ) );
        add_action( 'manage_users_custom_column', array( $this, 'render_user_status_column' ), 10, 3 );
    }

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
     * Puebla las taxonomías con datos iniciales
     */
    private function seed_data() {
        // ===========================
        // PERSONAL INFO
        // ===========================

        // Idiomas (principales de Europa + internacionales)
        $idiomas = array('Español', 'Inglés', 'Francés', 'Alemán', 'Italiano', 'Portugués', 'Catalán', 'Euskera', 'Gallego', 'Holandés', 'Ruso', 'Árabe', 'Chino Mandarín', 'Japonés');
        foreach ($idiomas as $i) {
            if (!term_exists($i, 'sop_idioma')) wp_insert_term($i, 'sop_idioma');
        }

        // Niveles de Idioma (Marco Común Europeo)
        $niveles = array('Nativo', 'Bilingüe', 'Avanzado (C1/C2)', 'Intermedio (B1/B2)', 'Básico (A1/A2)');
        foreach ($niveles as $n) {
            if (!term_exists($n, 'sop_nivel')) wp_insert_term($n, 'sop_nivel');
        }

        // Nacionalidades (España + Europa + Latinoamérica + África futbolística)
        $nacionalidades = array(
            'España', 'Alemania', 'Francia', 'Italia', 'Portugal', 'Reino Unido', 'Países Bajos', 'Bélgica', 'Suiza',
            'Austria', 'Suecia', 'Noruega', 'Dinamarca', 'Finlandia', 'Polonia', 'Croacia', 'Serbia',
            'Grecia', 'Turquía', 'Rumania', 'Ucrania', 'República Checa', 'Hungría', 'Irlanda', 'Escocia',
            'Argentina', 'Brasil', 'México', 'Colombia', 'Chile', 'Uruguay', 'Perú', 'Ecuador', 'Venezuela', 'Paraguay',
            'EE.UU.', 'Canadá', 'Marruecos', 'Senegal', 'Nigeria', 'Camerún', 'Costa de Marfil', 'Japón', 'Corea del Sur', 'Australia'
        );
        foreach ($nacionalidades as $nac) {
            if (!term_exists($nac, 'sop_nacionalidad')) wp_insert_term($nac, 'sop_nacionalidad');
        }

        // Ubicaciones — Todas las capitales de provincia de España (50) + Ceuta/Melilla + Europa
        $ubicaciones = array(
            // España: 50 capitales de provincia + ciudades autónomas
            'A Coruña', 'Álava / Vitoria', 'Albacete', 'Alicante', 'Almería',
            'Ávila', 'Badajoz', 'Barcelona', 'Bilbao', 'Burgos',
            'Cáceres', 'Cádiz', 'Castellón de la Plana', 'Ceuta', 'Ciudad Real',
            'Córdoba', 'Cuenca', 'Girona', 'Granada', 'Guadalajara',
            'Huelva', 'Huesca', 'Jaén', 'Las Palmas de Gran Canaria', 'León',
            'Lleida', 'Logroño', 'Lugo', 'Madrid', 'Málaga',
            'Melilla', 'Murcia', 'Ourense', 'Oviedo', 'Palencia',
            'Palma de Mallorca', 'Pamplona', 'Pontevedra', 'Salamanca', 'San Sebastián',
            'Santa Cruz de Tenerife', 'Santander', 'Segovia', 'Sevilla', 'Soria',
            'Tarragona', 'Teruel', 'Toledo', 'Valencia', 'Valladolid',
            'Vigo', 'Zamora', 'Zaragoza',
            // Otras ciudades relevantes de España
            'Gijón', 'Elche', 'Cartagena', 'Jerez de la Frontera', 'Marbella',
            'Getafe', 'Leganés', 'Alcorcón', 'Cornellà', 'Hospitalet de Llobregat',
            // Europa: principales capitales y ciudades deportivas
            'Londres', 'Mánchester', 'Liverpool', 'París', 'Lyon', 'Marsella',
            'Berlín', 'Múnich', 'Dortmund', 'Roma', 'Milán', 'Turín', 'Nápoles',
            'Lisboa', 'Oporto', 'Ámsterdam', 'Róterdam', 'Bruselas',
            'Viena', 'Zúrich', 'Estocolmo', 'Copenhague', 'Oslo',
            'Varsovia', 'Praga', 'Budapest', 'Atenas', 'Estambul', 'Zagreb', 'Belgrado',
            // Latinoamérica
            'Buenos Aires', 'Ciudad de México', 'Bogotá', 'Santiago', 'Lima', 'Montevideo', 'São Paulo',
            // Otros
            'Online / Remoto'
        );
        foreach ($ubicaciones as $ub) {
            if (!term_exists($ub, 'sop_ubicacion')) wp_insert_term($ub, 'sop_ubicacion');
        }

        // Pierna Dominante
        $piernas = array('Derecha', 'Izquierda', 'Ambidiestro');
        foreach ($piernas as $p) {
            if (!term_exists($p, 'sop_pierna')) wp_insert_term($p, 'sop_pierna');
        }

        // Alturas (150–230 cm, cada 2 cm — incluye basquetbolistas)
        for ($h = 150; $h <= 230; $h += 2) {
            $v = (string) $h;
            if (!term_exists($v, 'sop_altura')) wp_insert_term($v, 'sop_altura');
        }

        // Pesos (45–160 kg — incluye boxeadores de peso pesado)
        for ($w = 45; $w <= 160; $w++) {
            $v = (string) $w;
            if (!term_exists($v, 'sop_peso')) wp_insert_term($v, 'sop_peso');
        }

        // Niveles Profesionales (España)
        $niveles_prof = array('Profesional', 'Semiprofesional', 'Amateur', 'Formativo / Cantera');
        foreach ($niveles_prof as $np) {
            if (!term_exists($np, 'sop_nivel_prof')) wp_insert_term($np, 'sop_nivel_prof');
        }

        // Categorías de Fútbol (Prioridad España Pro → Cantera → Europa)
        $categorias = array(
            // 1. Niveles de Competición Profesional y Semiprofesional (España)
            'Primera División (LaLiga EA Sports)', 
            'Segunda División (LaLiga Hypermotion)', 
            'Primera Federación (1ª RFEF)', 
            'Segunda Federación (2ª RFEF)', 
            'Tercera Federación (3ª RFEF)', 
            'Regional Preferente', 
            'Primera Regional',
            
            // 2. Por Edad / Cantera (España)
            'Sénior', 'Sub-23', 'Juvenil A (División de Honor)', 'Juvenil B (Liga Nacional)', 'Juvenil C', 'Cadete', 'Infantil', 'Alevín', 'Benjamín', 'Pre-benjamín', 'Veteranos',
            
            // 3. Ligas Europa (Principales)
            'Premier League (Inglaterra)', 'Bundesliga (Alemania)', 'Serie A (Italia)', 'Ligue 1 (Francia)', 'Primeira Liga (Portugal)', 'Eredivisie (Países Bajos)', 'Scottish Premiership', 'Süper Lig (Turquía)'
        );
        foreach ($categorias as $cat) {
            if (!term_exists($cat, 'sop_categoria')) wp_insert_term($cat, 'sop_categoria');
        }

        // Redes Sociales
        $rrss = array('Instagram', 'Facebook', 'X (Twitter)', 'LinkedIn', 'YouTube', 'TikTok', 'Twitch', 'Threads');
        foreach ($rrss as $red) {
            if (!term_exists($red, 'sop_red_social')) wp_insert_term($red, 'sop_red_social');
        }

        // ===========================
        // COACH / SPECIALIST
        // ===========================

        // Ocupaciones
        $ocupaciones = array(
            'Entrenador Principal', 'Segundo Entrenador', 'Entrenador de Porteros',
            'Preparador Físico', 'Director Técnico', 'Director Deportivo',
            'Analista Táctico', 'Analista de Vídeo', 'Ojeador / Scout',
            'Fisioterapeuta Deportivo', 'Médico Deportivo', 'Nutricionista Deportivo',
            'Psicólogo Deportivo', 'Readaptador de Lesiones', 'Entrenador Personal',
            'Coordinador de Cantera', 'Director de Metodología', 'Delegado de Campo'
        );
        foreach ($ocupaciones as $oc) {
            if (!term_exists($oc, 'sop_ocupacion')) wp_insert_term($oc, 'sop_ocupacion');
        }

        // Experiencia
        $experiencias = array('Menos de 1 año', '1-2 años', '3-5 años', '5-10 años', '10-15 años', '15-20 años', 'Más de 20 años');
        foreach ($experiencias as $exp) {
            if (!term_exists($exp, 'sop_experiencia')) wp_insert_term($exp, 'sop_experiencia');
        }

        // Títulos de Estudio (grados oficiales)
        $titulos = array(
            'Grado en Ciencias de la Actividad Física y del Deporte (CAFYD/INEF)',
            'Grado en Fisioterapia', 'Grado en Nutrición Humana y Dietética',
            'Grado en Psicología', 'Grado en Medicina',
            'Máster en Alto Rendimiento Deportivo', 'Máster en Preparación Física',
            'Máster en Dirección y Gestión del Deporte', 'Máster en Readaptación de Lesiones',
            'Máster en Nutrición Deportiva',
            'Técnico Superior en Enseñanza y Animación Sociodeportiva (TSEAS)',
            'Técnico Deportivo en Fútbol (Nivel I)', 'Técnico Deportivo en Fútbol (Nivel II)',
            'Técnico Deportivo Superior en Fútbol (Nivel III)',
            'Doctorado en Ciencias del Deporte'
        );
        foreach ($titulos as $t) {
            if (!term_exists($t, 'sop_titulo')) wp_insert_term($t, 'sop_titulo');
        }

        // Institutos (España + Europa + organismos internacionales)
        $institutos = array(
            'INEF Madrid', 'INEF Barcelona', 'INEF Granada', 'INEF Galicia',
            'Universidad Europea de Madrid', 'Universidad Camilo José Cela',
            'Universidad Autónoma de Madrid', 'Universidad de Barcelona',
            'Universidad de Valencia', 'Universidad del País Vasco',
            'Universidad de Sevilla', 'Universidad de Málaga',
            'Universidad Católica San Antonio (UCAM)', 'Universidad de Vic',
            'Real Federación Española de Fútbol (RFEF)',
            'Escuela Nacional de Entrenadores (RFEF)',
            'Federación Catalana de Fútbol', 'Federación Madrileña de Fútbol',
            'UEFA', 'FIFA', 'Johan Cruyff Institute', 'LaLiga Business School',
            'KNVB (Países Bajos)', 'DFB (Alemania)', 'The FA (Inglaterra)',
            'FFF (Francia)', 'FIGC (Italia)', 'FPF (Portugal)',
            'Coverciano (Centro Técnico FIGC)',
            'NSCA', 'ACSM', 'NASM', 'ISSA'
        );
        foreach ($institutos as $inst) {
            if (!term_exists($inst, 'sop_instituto')) wp_insert_term($inst, 'sop_instituto');
        }

        // Lugares de Estudio — Ciudades españolas con universidades/centros deportivos + Europa
        $lugares = array(
            // España (ciudades con universidades o centros de formación deportiva)
            'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao', 'San Sebastián',
            'Málaga', 'Granada', 'Zaragoza', 'Murcia', 'Alicante', 'Córdoba',
            'Valladolid', 'Salamanca', 'Oviedo', 'Pamplona', 'Vitoria',
            'A Coruña', 'Vigo', 'Santiago de Compostela', 'León', 'Cádiz',
            'Huelva', 'Jaén', 'Almería', 'Castellón', 'Lleida', 'Girona',
            'Tarragona', 'Logroño', 'Santander', 'Las Palmas', 'Tenerife',
            'Palma de Mallorca', 'Cáceres', 'Badajoz', 'Albacete', 'Ciudad Real',
            'Toledo', 'Cuenca', 'Guadalajara', 'Ávila', 'Segovia', 'Soria',
            'Teruel', 'Huesca', 'Pontevedra', 'Lugo', 'Ourense', 'Burgos', 'Palencia', 'Zamora',
            // Europa
            'Londres', 'Mánchester', 'Liverpool', 'París', 'Lyon', 'Marsella',
            'Berlín', 'Múnich', 'Colonia', 'Roma', 'Milán', 'Turín', 'Bolonia',
            'Lisboa', 'Oporto', 'Ámsterdam', 'Róterdam', 'Bruselas',
            'Viena', 'Zúrich', 'Estocolmo', 'Copenhague', 'Oslo',
            'Varsovia', 'Praga', 'Budapest', 'Atenas', 'Zagreb', 'Belgrado',
            // Latinoamérica
            'Buenos Aires', 'São Paulo', 'Ciudad de México', 'Bogotá', 'Santiago', 'Lima', 'Montevideo',
            // Otros
            'Online / A distancia'
        );
        foreach ($lugares as $lg) {
            if (!term_exists($lg, 'sop_lugar_estudio')) wp_insert_term($lg, 'sop_lugar_estudio');
        }

        // Tipos de Título / Licencia (UEFA + federaciones)
        $tipos_titulo = array(
            'UEFA PRO', 'UEFA A', 'UEFA B', 'UEFA C', 'UEFA Futsal B',
            'UEFA Goalkeeper A', 'UEFA Goalkeeper B', 'UEFA Youth A', 'UEFA Youth B',
            'UEFA Fitness Coach', 'UEFA Elite Youth A',
            'Licencia Nacional Nivel I', 'Licencia Nacional Nivel II', 'Licencia Nacional Nivel III',
            'Monitor Deportivo', 'Grado Universitario', 'Máster Universitario',
            'Certificación Internacional', 'Diploma de Postgrado'
        );
        foreach ($tipos_titulo as $tt) {
            if (!term_exists($tt, 'sop_tipo_titulo')) wp_insert_term($tt, 'sop_tipo_titulo');
        }

        // Países (toda Europa + Latinoamérica + otros relevantes)
        $paises = array(
            'España', 'Alemania', 'Francia', 'Italia', 'Portugal', 'Reino Unido',
            'Países Bajos', 'Bélgica', 'Suiza', 'Austria', 'Suecia', 'Noruega',
            'Dinamarca', 'Finlandia', 'Polonia', 'Croacia', 'Serbia', 'Grecia',
            'Turquía', 'Rumania', 'Ucrania', 'República Checa', 'Hungría',
            'Irlanda', 'Escocia', 'Gales', 'Eslovaquia', 'Eslovenia', 'Bulgaria',
            'Montenegro', 'Bosnia y Herzegovina', 'Albania', 'Macedonia del Norte',
            'Islandia', 'Luxemburgo', 'Malta', 'Chipre', 'Estonia', 'Letonia', 'Lituania',
            'Argentina', 'Brasil', 'México', 'Colombia', 'Chile', 'Uruguay', 'Perú',
            'Ecuador', 'Venezuela', 'Paraguay', 'Bolivia', 'Costa Rica', 'Panamá',
            'EE.UU.', 'Canadá', 'Marruecos', 'Australia', 'Japón', 'Corea del Sur',
            'Qatar', 'Arabia Saudita', 'Emiratos Árabes Unidos', 'China'
        );
        foreach ($paises as $pa) {
            if (!term_exists($pa, 'sop_pais')) wp_insert_term($pa, 'sop_pais');
        }

        // Certificaciones (cursos complementarios)
        $certificaciones = array(
            'Certificación FIFA en Dirección Técnica',
            'Certificación NSCA-CSCS (Fuerza y Acondicionamiento)',
            'Certificación NSCA-CPT (Entrenador Personal)',
            'Certificación ACSM', 'Certificación NASM-PES (Rendimiento Deportivo)',
            'Diploma en Análisis Táctico', 'Diploma en Scouting y Análisis de Rendimiento',
            'Curso de Psicología Deportiva', 'Curso de Nutrición Deportiva Avanzada',
            'Curso de Readaptación de Lesiones Deportivas',
            'Certificación en Entrenamiento Funcional',
            'Certificación en Pilates / Yoga Deportivo',
            'Curso de Primeros Auxilios Deportivos',
            'Diploma en Gestión Deportiva', 'Curso de Videoanálisis Deportivo'
        );
        foreach ($certificaciones as $cert) {
            if (!term_exists($cert, 'sop_certificacion')) wp_insert_term($cert, 'sop_certificacion');
        }

        // Posiciones de fútbol (demarcaciones oficiales)
        $posiciones = array(
            'Portero', 'Lateral Derecho', 'Lateral Izquierdo',
            'Carrilero Derecho', 'Carrilero Izquierdo',
            'Central', 'Líbero',
            'Pivote / Mediocentro Defensivo', 'Mediocentro',
            'Interior Derecho', 'Interior Izquierdo',
            'Mediapunta / Enganche', 'Mediocentro Ofensivo',
            'Extremo Derecho', 'Extremo Izquierdo',
            'Segundo Delantero', 'Delantero Centro', 'Falso 9'
        );
        foreach ($posiciones as $pos) {
            if (!term_exists($pos, 'sop_posicion')) wp_insert_term($pos, 'sop_posicion');
        }

        // Fases Ofensivas (modelo de juego)
        $fases_of = array(
            'Salida de balón desde portero', 'Construcción / Primera fase ofensiva',
            'Progresión / Segunda fase ofensiva', 'Finalización / Tercera fase ofensiva',
            'Transición defensa-ataque', 'Ataque organizado', 'Ataque posicional',
            'Contraataque', 'Juego combinativo', 'Juego directo',
            'Acciones a balón parado ofensivas'
        );
        foreach ($fases_of as $fo) {
            if (!term_exists($fo, 'sop_fase_ofensiva')) wp_insert_term($fo, 'sop_fase_ofensiva');
        }

        // Fases Defensivas (modelo de juego)
        $fases_def = array(
            'Presión alta / Pressing', 'Bloque medio / Repliegue medio',
            'Bloque bajo / Repliegue intensivo', 'Transición ataque-defensa',
            'Defensa organizada', 'Defensa zonal', 'Defensa individual', 'Defensa mixta',
            'Pressing tras pérdida (6 segundos)',
            'Acciones a balón parado defensivas'
        );
        foreach ($fases_def as $fd) {
            if (!term_exists($fd, 'sop_fase_defensiva')) wp_insert_term($fd, 'sop_fase_defensiva');
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
