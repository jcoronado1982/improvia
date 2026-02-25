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

        // Reemplazar Avatar de WP
        add_filter( 'get_avatar_url', array( $this, 'custom_avatar_url' ), 10, 3 );
        add_filter( 'get_avatar', array( $this, 'custom_avatar_html' ), 10, 5 );

        // Cargar Controladores MVC
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Controllers/class-shortcodes-controller.php';
        new SOP_Shortcodes_Controller();

        // Manejar actualización de perfil vía AJAX
        add_action( 'wp_ajax_sop_update_profile', array( $this, 'handle_profile_update' ) );
        add_action( 'wp_ajax_nopriv_sop_update_profile', array( $this, 'handle_profile_update' ) );

        // Manejar suscripción simulada
        add_action( 'wp_ajax_sop_simulate_subscription', array( $this, 'simulate_subscription' ) );
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
            'sop_pierna_id', 'sop_altura_id', 'sop_peso_id', 'sop_nivel_prof_id', 'sop_categoria_id',
            'sop_prof_description',
            'sop_ocupacion_id', 'sop_experiencia_id',
            'sop_precio_semanal', 'sop_precio_mensual', 'sop_precio_trimestral', 'sop_precio_anual',
            'sop_precio_sesiones', 'sop_cantidad_sesiones'
        );
        foreach ( $meta_fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                $new_val = sanitize_text_field( $_POST[$field] );
                $old_val = get_user_meta( $user_id, $field, true );

                if ( $new_val !== $old_val ) {
                    update_user_meta( $user_id, $field, $new_val );

                    if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                        \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                            'profile_meta_updated',
                            $user_id,
                            null,
                            [ 'field' => $field, 'value' => $old_val ],
                            [ 'field' => $field, 'value' => $new_val ]
                        );
                    }
                }
            }
        }

        // Guardar idiomas
        if ( isset( $_POST['sop_idiomas'] ) ) {
            $idiomas = json_decode( stripslashes( $_POST['sop_idiomas'] ), true );
            $old_idiomas = get_user_meta( $user_id, 'sop_idiomas_data', true );
            update_user_meta( $user_id, 'sop_idiomas_data', $idiomas );

            if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                    'profile_languages_updated',
                    $user_id,
                    null,
                    $old_idiomas,
                    $idiomas
                );
            }
        }

        // Guardar RRSS (Redes Sociales)
        if ( isset( $_POST['sop_rrss'] ) ) {
            $rrss = json_decode( stripslashes( $_POST['sop_rrss'] ), true );
            $old_rrss = get_user_meta( $user_id, 'sop_rrss_data', true );
            update_user_meta( $user_id, 'sop_rrss_data', $rrss );

            if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                    'profile_rrss_updated',
                    $user_id,
                    null,
                    $old_rrss,
                    $rrss
                );
            }
        }

        // --- SUBIDA DE IMAGEN DE PERFIL ---
        if ( ! empty( $_FILES['sop_profile_picture']['name'] ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            $attachment_id = media_handle_upload( 'sop_profile_picture', 0 ); 
            
            if ( is_wp_error( $attachment_id ) ) {
                if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                    \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                        'profile_image_upload_failed',
                        $user_id,
                        null,
                        null,
                        [ 'error' => $attachment_id->get_error_message(), 'filename' => $_FILES['sop_profile_picture']['name'] ]
                    );
                }
                wp_send_json_error( 'Error al subir la imagen: ' . $attachment_id->get_error_message() );
            } else {
                $old_id = get_user_meta( $user_id, 'sop_profile_image_id', true );
                
                // 1. Primero guardamos la nueva foto en el perfil del usuario de forma segura
                update_user_meta( $user_id, 'sop_profile_image_id', $attachment_id );
                
                // 2. Solo DESPUÉS de asegurar la nueva foto, borramos la anterior (si existía)
                if ( $old_id && $old_id != $attachment_id ) {
                    wp_delete_attachment( $old_id, true );
                }
                
                if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                    \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                        'profile_image_updated',
                        $user_id,
                        $attachment_id,
                        $old_id ? [ 'deleted_attachment' => $old_id ] : null,
                        [ 'filename' => $_FILES['sop_profile_picture']['name'] ]
                    );
                }
            }
        }

        // --- SUBIDA DE DOCUMENTO PROFESIONAL (ESTUDIO PRINCIPAL) ---
        if ( ! empty( $_FILES['sop_professional_document']['name'] ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            $doc_attachment_id = media_handle_upload( 'sop_professional_document', 0 ); 
            
            if ( is_wp_error( $doc_attachment_id ) ) {
                if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                    \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                        'professional_doc_upload_failed',
                        $user_id,
                        null,
                        null,
                        [ 'error' => $doc_attachment_id->get_error_message(), 'filename' => $_FILES['sop_professional_document']['name'] ]
                    );
                }
                wp_send_json_error( 'Error al subir el documento: ' . $doc_attachment_id->get_error_message() );
            } else {
                $old_doc_id = get_user_meta( $user_id, 'sop_professional_document_id', true );
                
                // Guardar la nueva ID
                update_user_meta( $user_id, 'sop_professional_document_id', $doc_attachment_id );
                
                // Borrar el archivo viejo si existía
                if ( $old_doc_id && $old_doc_id != $doc_attachment_id ) {
                    wp_delete_attachment( $old_doc_id, true );
                }
                
                if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                    \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                        'professional_doc_updated',
                        $user_id,
                        $doc_attachment_id,
                        $old_doc_id ? [ 'deleted_attachment' => $old_doc_id ] : null,
                        [ 'filename' => $_FILES['sop_professional_document']['name'] ]
                    );
                }
            }
        }

        // --- SUBIDA DE CERTIFICACIÓN DEL COACH ---
        if ( ! empty( $_FILES['sop_coach_certification_doc']['name'] ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            $coach_doc_id = media_handle_upload( 'sop_coach_certification_doc', 0 );
            
            if ( is_wp_error( $coach_doc_id ) ) {
                if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                    \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                        'coach_certification_upload_failed', $user_id, null, null,
                        [ 'error' => $coach_doc_id->get_error_message(), 'filename' => $_FILES['sop_coach_certification_doc']['name'] ]
                    );
                }
                wp_send_json_error( 'Error al subir certificación: ' . $coach_doc_id->get_error_message() );
            } else {
                $old_coach_doc = get_user_meta( $user_id, 'sop_coach_certification_doc_id', true );
                update_user_meta( $user_id, 'sop_coach_certification_doc_id', $coach_doc_id );
                if ( $old_coach_doc && $old_coach_doc != $coach_doc_id ) {
                    wp_delete_attachment( $old_coach_doc, true );
                }
                if ( class_exists( '\Improvia\Modules\Traceability\Classes\Audit_Logger' ) ) {
                    \Improvia\Modules\Traceability\Classes\Audit_Logger::log(
                        'coach_certification_updated', $user_id, $coach_doc_id,
                        $old_coach_doc ? [ 'deleted_attachment' => $old_coach_doc ] : null,
                        [ 'filename' => $_FILES['sop_coach_certification_doc']['name'] ]
                    );
                }
            }
        }

        // --- DATOS COACH: Formación Reglada (JSON) ---
        if ( isset( $_POST['sop_formacion_reglada'] ) ) {
            $formacion = json_decode( stripslashes( $_POST['sop_formacion_reglada'] ), true );
            update_user_meta( $user_id, 'sop_formacion_reglada_data', $formacion );
        }

        // --- DATOS COACH: Estudios Secundarios (JSON) ---
        if ( isset( $_POST['sop_estudios_secundarios'] ) ) {
            $estudios = json_decode( stripslashes( $_POST['sop_estudios_secundarios'] ), true );
            update_user_meta( $user_id, 'sop_estudios_secundarios_data', $estudios );
        }

        // --- DATOS COACH: Posiciones y Fases (Checkboxes) ---
        $form_type = isset($_POST['sop_form_type']) ? sanitize_text_field($_POST['sop_form_type']) : '';
        if ($form_type !== 'subscriptions') {
            $array_fields = array( 'sop_posiciones_ids', 'sop_fase_ofensiva_ids', 'sop_fase_defensiva_ids' );
            foreach ( $array_fields as $af ) {
                if ( isset( $_POST[$af] ) ) {
                    $values = array_map( 'intval', (array) $_POST[$af] );
                    update_user_meta( $user_id, $af, $values );
                } else {
                    // Si no se enviaron checkboxes, limpiar el campo
                    delete_user_meta( $user_id, $af );
                }
            }
        }

        wp_send_json_success( 'Perfil actualizado correctamente' );
    }

    /**
     * Maneja el flujo de suscripción simulada. 
     * Asocia al atleta con el entrenador y crea un registro de simulación financiera.
     */
    public function simulate_subscription() {
        if ( ! check_ajax_referer( 'sop_save_pref_nonce', 'nonce', false ) ) {
            wp_send_json_error( 'Error de seguridad (Nonce inválido)' );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( 'Usuario no identificado' );

        $trainer_id = intval( $_POST['trainer_id'] );
        $amount = floatval( $_POST['amount'] );

        if ( !$trainer_id || $amount <= 0 ) {
            wp_send_json_error( 'Datos inválidos para la suscripción.' );
        }

        // 1. "Atar" al atleta con el entrenador. Podríamos guardarlo en user_meta
        // Por la simplicidad de la simulación, lo guardamos como un array de entrenadores suscritos:
        $current_trainers = get_user_meta( $user_id, 'sop_subscribed_trainers', true );
        if( !is_array($current_trainers) ) $current_trainers = array();
        
        if ( !in_array($trainer_id, $current_trainers) ) {
             $current_trainers[] = $trainer_id;
             update_user_meta( $user_id, 'sop_subscribed_trainers', $current_trainers );
        }

        // 2. Registrar el pago simulado (Dinero de mentira)
        // Ejemplo de tabla logs (la guardaremos en wp_options por ser simulación rápida, o en CPT custom)
        // Usaremos get_option para apilar un array masivo de logs transaccionales 
        $transaction_logs = get_option( 'sop_mock_transactions_log', array() );
        
        // Simulación lógica Split (Improvia cobra 10% por ej.)
        $platform_fee = $amount * 0.10;
        $trainer_earning = $amount - $platform_fee;

        $transaction_logs[] = array(
             'date' => current_time('mysql'),
             'athlete_id' => $user_id,
             'trainer_id' => $trainer_id,
             'total_amount' => $amount,
             'platform_fee' => $platform_fee,
             'trainer_earning' => $trainer_earning,
             'status' => 'succeeded (mock)'
        );

        update_option( 'sop_mock_transactions_log', $transaction_logs );

        wp_send_json_success( 'Suscripción completada.' );
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
     * Reemplaza el Avatar de WordPress por la imagen subida en el perfil
     */
    public function custom_avatar_url( $url, $id_or_email, $args ) {
        $user_id = 0;
        
        if ( is_numeric( $id_or_email ) ) {
            $user_id = (int) $id_or_email;
        } elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) ) {
            $user_id = $user->ID;
        } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
            $user_id = (int) $id_or_email->user_id;
        }
        
        if ( $user_id ) {
            $profile_image_id = get_user_meta( $user_id, 'sop_profile_image_id', true );
            if ( $profile_image_id ) {
                $custom_url = wp_get_attachment_image_url( $profile_image_id, 'thumbnail' );
                if ( $custom_url ) {
                    return $custom_url;
                }
            }
        }
        
        return $url;
    }

    public function custom_avatar_html( $avatar, $id_or_email, $size, $default, $alt ) {
        $user_id = 0;
        
        if ( is_numeric( $id_or_email ) ) {
            $user_id = (int) $id_or_email;
        } elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) ) {
            $user_id = $user->ID;
        } elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
            $user_id = (int) $id_or_email->user_id;
        }
        
        if ( $user_id ) {
            $profile_image_id = get_user_meta( $user_id, 'sop_profile_image_id', true );
            if ( $profile_image_id ) {
                $custom_url = wp_get_attachment_image_url( $profile_image_id, array( $size, $size ) );
                if ( $custom_url ) {
                    $avatar = sprintf(
                        "<img alt='%s' src='%s' class='avatar avatar-%d photo' height='%d' width='%d' />",
                        esc_attr( $alt ),
                        esc_url( $custom_url ),
                        (int) $size,
                        (int) $size,
                        (int) $size
                    );
                }
            }
        }
        
        return $avatar;
    }

    /**
     * Agrega una clase CSS al body si el usuario es Entrenador o Especialista
     */
    public function add_provider_body_class( $classes ) {
        // Forzar tema oscuro de proveedor en el detalle público del entrenador
        if ( is_page('detalle-entrenador') ) {
            $classes[] = 'sop-is-provider';
            $classes[] = 'sop-preview-mode';
        }

        if ( is_user_logged_in() ) {
            if ( current_user_can( 'entrenador' ) || current_user_can( 'especialista' ) ) {
                if ( ! is_page('detalle-entrenador') ) {
                    $classes[] = 'sop-is-provider';
                    // Solo agregar el tema claro si NO estamos en previsualización de detalle público
                    // (el JS en perfil igual maneja lo suyo)
                    $classes[] = 'sop-provider-theme-light';
                }
            } elseif ( current_user_can( 'atleta' ) || current_user_can( 'deportista' ) ) {
                $classes[] = 'sop-is-atleta';
            }
        }
        return $classes;
    }
    /**
     * Mapa de nacionalidad → código ISO de país
     */
    public static function get_flag_map() {
        return array(
            'España' => 'ES', 'Alemania' => 'DE', 'Francia' => 'FR', 'Italia' => 'IT',
            'Portugal' => 'PT', 'Reino Unido' => 'GB', 'Países Bajos' => 'NL', 'Bélgica' => 'BE',
            'Suiza' => 'CH', 'Austria' => 'AT', 'Suecia' => 'SE', 'Noruega' => 'NO',
            'Dinamarca' => 'DK', 'Finlandia' => 'FI', 'Polonia' => 'PL', 'Croacia' => 'HR',
            'Serbia' => 'RS', 'Grecia' => 'GR', 'Turquía' => 'TR', 'Rumania' => 'RO',
            'Ucrania' => 'UA', 'República Checa' => 'CZ', 'Hungría' => 'HU', 'Irlanda' => 'IE',
            'Escocia' => 'GB', 'Argentina' => 'AR', 'Brasil' => 'BR', 'México' => 'MX',
            'Colombia' => 'CO', 'Chile' => 'CL', 'Uruguay' => 'UY', 'Perú' => 'PE',
            'Ecuador' => 'EC', 'Venezuela' => 'VE', 'Paraguay' => 'PY',
            'EE.UU.' => 'US', 'Canadá' => 'CA', 'Marruecos' => 'MA',
            'Senegal' => 'SN', 'Nigeria' => 'NG', 'Camerún' => 'CM', 'Costa de Marfil' => 'CI',
            'Japón' => 'JP', 'Corea del Sur' => 'KR', 'Australia' => 'AU',
            'Gales' => 'GB', 'Eslovaquia' => 'SK', 'Eslovenia' => 'SI', 'Bulgaria' => 'BG',
            'Montenegro' => 'ME', 'Bosnia y Herzegovina' => 'BA', 'Albania' => 'AL',
            'Macedonia del Norte' => 'MK', 'Islandia' => 'IS', 'Luxemburgo' => 'LU',
            'Malta' => 'MT', 'Chipre' => 'CY', 'Estonia' => 'EE', 'Letonia' => 'LV', 'Lituania' => 'LT',
            'Bolivia' => 'BO', 'Costa Rica' => 'CR', 'Panamá' => 'PA',
            'Qatar' => 'QA', 'Arabia Saudita' => 'SA', 'Emiratos Árabes Unidos' => 'AE', 'China' => 'CN',
        );
    }

    /**
     * Convierte un código ISO de 2 letras en emoji de bandera
     */
    public static function country_flag( $code ) {
        if ( empty( $code ) || strlen( $code ) < 2 ) return '🏳️';
        $code = strtoupper( substr( $code, 0, 2 ) );
        return mb_convert_encoding(
            '&#' . ( 127397 + ord( $code[0] ) ) . ';&#' . ( 127397 + ord( $code[1] ) ) . ';',
            'UTF-8', 'HTML-ENTITIES'
        );
    }

    /**
     * Obtiene el emoji de bandera a partir del nombre de la nacionalidad
     */
    public static function get_nationality_flag( $nationality_name ) {
        $map = self::get_flag_map();
        if ( isset( $map[ $nationality_name ] ) ) {
            return self::country_flag( $map[ $nationality_name ] );
        }
        return '🏳️';
    }
}
