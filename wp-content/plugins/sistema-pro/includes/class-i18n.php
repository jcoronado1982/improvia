<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SOP_I18n {

    public function __construct() {
        // Hook location for loading translations
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 5 );
        
        // Filter the locale to load user's preference
        add_filter( 'locale', array( $this, 'set_user_locale' ) );
        
        // Filter gettext to provide instant translations using our PHP array
        add_filter( 'gettext', array( $this, 'translate_strings' ), 10, 3 );
        
        // Ajax endpoint to save preference
        add_action( 'wp_ajax_sop_save_language_preference', array( $this, 'save_language_preference' ) );
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'sistema-pro', false, dirname( plugin_basename( dirname( __FILE__ ) ) ) . '/languages' );
    }

    public function set_user_locale( $locale ) {
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $user_locale = get_user_meta( $user_id, 'sop_user_language', true );
            
            if ( ! empty( $user_locale ) ) {
                return $user_locale;
            }
        }
        return $locale;
    }

    public function translate_strings( $translated_text, $text, $domain ) {
        if ( 'sistema-pro' !== $domain ) {
            return $translated_text;
        }

        // Only translate if the current locale is English
        if ( determine_locale() !== 'en_US' ) {
            return $translated_text;
        }

        $en_dict = array(
            'NOTIFICACIONES' => 'NOTIFICATIONS',
            'Nuevos Mensajes' => 'New Messages',
            'Recibe notificaciones cuando obtengas nuevos mensajes' => 'Receive notifications when you get new messages',
            'Nuevos Comentarios' => 'New Comments',
            'Recibe notificaciones cuando obtengas nuevos comentarios' => 'Receive notifications when you get new comments',
            'Nuevos Seguidores' => 'New Followers',
            'Recibe notificaciones cuando obtengas nuevos seguidores' => 'Receive notifications when you get new followers',
            'Promociones' => 'Promotions',
            'Recibe notificaciones acerca de promociones' => 'Receive notifications about promotions',
            'IDIOMA' => 'LANGUAGE',
            'Español' => 'Spanish',
            'English' => 'English',
            'Cambiar' => 'Change',
            'CANCELACION' => 'ACCOUNT CANCELLATION',
            '¿Qué ocurre cuando elimino mi cuenta?' => 'What happens when I delete my account?',
            'Tu perfil y tus trabajos ya no sere mostraran en Improvia' => 'Your profile and work will no longer be shown on Improvia',
            'Las solicitudes o contrataciones seran canceladas' => 'Requests or hires will be cancelled',
            'Si tu entrenador o especialista ya esta trabajando el dinero no sera reembolsable' => 'If your coach or specialist is already working, the money will not be refunded',
            'Me retiro porque...' => 'I am leaving because...',
            'Elegir una razon' => 'Choose a reason',
            'No entiendo la plataforma' => 'I don\'t understand the platform',
            'Es muy caro' => 'It\'s too expensive',
            'Otro motivo' => 'Another reason',
            'Eliminar cuenta' => 'Delete account',
            'CORREO ELECTRÓNICO' => 'EMAIL ADDRESS',
            'Correo actual' => 'Current email',
            'Cambiar correo' => 'Change email',
            'CONTRASEÑA' => 'PASSWORD',
            'Contraseña actual' => 'Current password',
            'Cambiar contraseña' => 'Change password',
            'Ya has iniciado sesión.' => 'You are already logged in.',
            'Cerrar Sesión' => 'Log Out',
            'Bienvenido de nuevo' => 'Welcome back',
            'Ingresa tus credenciales para continuar' => 'Enter your credentials to continue',
            'Usuario o Correo' => 'Username or Email',
            'Entrar al Sistema' => 'Log In',
            '¿Olvidaste tu contraseña?' => 'Forgot your password?',
            'Crear tu cuenta' => 'Create your account',
            'Únete a la plataforma IMPROVIA' => 'Join the IMPROVIA platform',
            'Nombre Completo' => 'Full Name',
            'Nombre de Usuario' => 'Username',
            'Teléfono' => 'Phone',
            '¿Cuál es tu rol?' => 'What is your role?',
            'Soy Atleta' => 'I am an Athlete',
            'Soy Entrenador' => 'I am a Coach',
            'Soy Especialista' => 'I am a Specialist',
            'Registrarme ahora' => 'Register now',
            'Ya tienes una cuenta activa.' => 'You already have an active account.',
            'Error:' => 'Error:',
            // Sidebar Menu
            'Perfil' => 'Profile',
            'Mensajes' => 'Messages',
            'Entrenadores' => 'Coaches',
            'Especialistas' => 'Specialists',
            'Q&A' => 'Q&A',
            'Salir' => 'Log Out',
            // Profile Tabs
            'Información personal' => 'Personal info',
            'Información profesional' => 'Professional info',
            'Seguridad y acceso' => 'Security and access',
            'Sesiones' => 'Sessions',
            'Previsualizar' => 'Preview',
            'Ajustes' => 'Settings',
            // Profile Form Labels
            'ABOUT ME' => 'ABOUT ME',
            'Gestiona tu información personal y de contacto.' => 'Manage your personal and contact info.',
            'Subir imagen' => 'Upload image',
            'Nombre completo' => 'Full Name',
            'Ubicación' => 'Location',
            'Seleccionar' => 'Select',
            'Nacionalidad' => 'Nationality',
            'Nacimiento' => 'Birthdate',
            'Idiomas que manejo' => 'Languages I speak',
            'Lenguaje' => 'Language',
            'Nivel' => 'Level',
            'Añadir' => 'Add',
            'Guardar Cambios' => 'Save Changes',
            // Professional Info
            'INFORMACIÓN PARA MI ESPECIALISTA' => 'INFORMATION FOR MY SPECIALIST',
            'Pierna dominante' => 'Dominant leg',
            'Altura (cm)' => 'Height (cm)',
            'Peso' => 'Weight',
            'DESCRIPCIÓN PROFESIONAL' => 'PROFESSIONAL DESCRIPTION',
            'ESTUDIO PRINCIPAL' => 'MAIN EDUCATION',
            'Adjunta tu título o certificación para su verificación, de no adjuntarlo tu título no aparecerá para los Entrenadores.' => 'Attach your degree or certification for verification. If you don\'t attach it, your title won\'t appear for Coaches.',
            'REDES SOCIALES PROFESIONALES' => 'PROFESSIONAL SOCIAL NETWORKS',
            // Preview Overview (Provider)
            'BACKGROUND' => 'BACKGROUND',
            'Experiencia' => 'Experience',
            'Licencias' => 'Licenses',
            'Certificaciones' => 'Certifications',
            'POSICIONES ESPECIALIZADAS' => 'SPECIALIZED POSITIONS',
            'Posiciones' => 'Positions',
            'Paises donde puedo entrenar' => 'Countries where I can train',
            'QUIEN SOY' => 'WHO I AM',
            'MI COMPOSICION' => 'MY COMPOSITION',
            'REPORTE MEDICO / ESPECIALISTA' => 'MEDICAL / SPECIALIST REPORT',
            'Periodo' => 'Period',
            'Semanal' => 'Weekly',
            'Mensual' => 'Monthly',
            'Trimestral' => 'Quarterly',
            'Anual' => 'Yearly',
            'Cupos' => 'Slots',
            'SUSCRIBIRSE' => 'SUBSCRIBE',
            'REVIEWS' => 'REVIEWS',
            'Reseñas y valoraciones de clientes' => 'Customer reviews and ratings',
            '(%s sobre %s)' => '(%s out of %s)',
            'Basado en %s reseñas' => 'Based on %s reviews',
            'Todas las opiniones (%s)' => 'All opinions (%s)',
            'Positivas (%s) ✕' => 'Positive (%s) ✕',
            'Neutras (%s)' => 'Neutral (%s)',
            'Negativas (%s)' => 'Negative (%s)',
            'RRSS' => 'SOCIAL MEDIA'
        );

        if ( isset( $en_dict[ $text ] ) ) {
            return $en_dict[ $text ];
        }

        return $translated_text;
    }

    public function save_language_preference() {
        check_ajax_referer( 'sop_save_pref_nonce', 'nonce' );
        
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'No autorizado' );
        }

        $lang = isset( $_POST['lang'] ) ? sanitize_text_field( $_POST['lang'] ) : '';
        
        if ( ! empty( $lang ) ) {
            $user_id = get_current_user_id();
            update_user_meta( $user_id, 'sop_user_language', $lang );
            wp_send_json_success( 'Idioma guardado' );
        } else {
            wp_send_json_error( 'Idioma inválido' );
        }
    }
}
