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
        add_action( 'wp_ajax_sop_prepare_checkout', array( $this, 'handle_prepare_checkout' ) );
        add_action( 'wp_ajax_sop_simulate_subscription', array( $this, 'simulate_subscription' ) );

        // Manejar aceptación/rechazo de solicitudes (Simulación Capture/Void)
        add_action( 'wp_ajax_sop_accept_solicitude', array( $this, 'handle_solicitude_approval' ) );
        add_action( 'wp_ajax_sop_reject_solicitude', array( $this, 'handle_solicitude_approval' ) );

        // Manejar actualización de seguridad (Correo y Contraseña)
        add_action( 'wp_ajax_sop_update_email', array( $this, 'handle_update_email' ) );
        add_action( 'wp_ajax_sop_update_password', array( $this, 'handle_update_password' ) );

        // Admin Columns for Subscriptions
        add_filter( 'manage_subscription_posts_columns', array( $this, 'subscription_columns' ) );
        add_action( 'manage_subscription_posts_custom_column', array( $this, 'subscription_column_content' ), 10, 2 );
    }

    /**
     * Procesa la actualización del perfil del usuario
     */
    public function handle_profile_update() {
        if (!isset($_POST['nonce'])) {
            wp_send_json_error('NONCE NO RECIBIDO. POST Keys=' . implode(', ', array_keys($_POST)));
        }
        
        // WordPress por defecto usa '_wpnonce' si no se especifica nombre en wp_nonce_field
        if ( ! check_ajax_referer( 'sop_profile_nonce', 'nonce', false ) ) {
            wp_send_json_error( 'Error de seguridad (Nonce inválido). Valor enviado=' . $_POST['nonce'] );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            SOP_Debug::log( 'UI', 'Profile update failed: user not identified' );
            wp_send_json_error( 'Usuario no identificado' );
        }

        SOP_Debug::log( 'UI', 'Profile update started', [ 'user_id' => $user_id, 'fields' => array_keys($_POST) ] );

        // --- VALIDACIÓN DE CAMPOS OBLIGATORIOS ---
        $form_section = isset( $_POST['sop_form_section'] ) ? sanitize_text_field( $_POST['sop_form_section'] ) : '';

        if ( isset( $_POST['display_name'] ) && empty( trim( $_POST['display_name'] ) ) ) {
            wp_send_json_error( __( 'El nombre completo es obligatorio.', 'sistema-pro' ) );
        }
        if ( isset( $_POST['sop_ubicacion_id'] ) && empty( $_POST['sop_ubicacion_id'] ) ) {
            wp_send_json_error( __( 'La ubicación es obligatoria.', 'sistema-pro' ) );
        }
        if ( isset( $_POST['sop_nacionalidad_id'] ) && empty( $_POST['sop_nacionalidad_id'] ) ) {
            wp_send_json_error( __( 'La nacionalidad es obligatoria.', 'sistema-pro' ) );
        }
        if ( isset( $_POST['sop_fecha_nacimiento'] ) && empty( $_POST['sop_fecha_nacimiento'] ) ) {
            wp_send_json_error( __( 'La fecha de nacimiento es obligatoria.', 'sistema-pro' ) );
        }

        // Validación de idiomas (JSON)
        if ( isset( $_POST['sop_idiomas'] ) ) {
            $idiomas_val = json_decode( stripslashes( $_POST['sop_idiomas'] ), true );
            if ( empty( $idiomas_val ) ) {
                wp_send_json_error( __( 'Debes agregar al menos un idioma.', 'sistema-pro' ) );
            }
        }

        // Validación específica para proveedores (Coaches/Especialistas)
        $is_provider = user_can( $user_id, 'entrenador' ) || user_can( $user_id, 'especialista' );
        if ( $is_provider && $form_section === 'professional' ) {
            if ( isset( $_POST['sop_ocupacion_id'] ) && empty( $_POST['sop_ocupacion_id'] ) ) {
                wp_send_json_error( __( 'La ocupación actual es obligatoria para proveedores.', 'sistema-pro' ) );
            }
            if ( isset( $_POST['sop_experiencia_id'] ) && empty( $_POST['sop_experiencia_id'] ) ) {
                wp_send_json_error( __( 'La experiencia es obligatoria para proveedores.', 'sistema-pro' ) );
            }

            // Validación de Descripción Profesional
            if ( isset( $_POST['sop_prof_description'] ) && empty( trim( $_POST['sop_prof_description'] ) ) ) {
                wp_send_json_error( __( 'La descripción profesional es obligatoria.', 'sistema-pro' ) );
            }

            if ( user_can( $user_id, 'entrenador' ) ) {
                // Validación de Posiciones
                if ( ! isset( $_POST['sop_posiciones_ids'] ) || empty( $_POST['sop_posiciones_ids'] ) ) {
                    wp_send_json_error( __( 'Debes seleccionar al menos una posición especializada.', 'sistema-pro' ) );
                }
                
                // Validación de Fases de Juego (Ofensiva o Defensiva)
                $has_offense = isset( $_POST['sop_fase_ofensiva_ids'] ) && ! empty( $_POST['sop_fase_ofensiva_ids'] );
                $has_defense = isset( $_POST['sop_fase_defensiva_ids'] ) && ! empty( $_POST['sop_fase_defensiva_ids'] );
                
                if ( ! $has_offense && ! $has_defense ) {
                    wp_send_json_error( __( 'Debes seleccionar al menos una fase de juego.', 'sistema-pro' ) );
                }
            }
        }

        // Validación específica para atletas (Deportistas)
        $is_athlete = user_can( $user_id, 'atleta' ) || user_can( $user_id, 'deportista' );
        if ( $is_athlete && $form_section === 'professional' ) {
            $athlete_fields = array(
                'sop_pierna_id'      => __( 'La pierna dominante es obligatoria.', 'sistema-pro' ),
                'sop_altura_id'      => __( 'La altura es obligatoria.', 'sistema-pro' ),
                'sop_peso_id'        => __( 'El peso es obligatorio.', 'sistema-pro' ),
                'sop_categoria_id'   => __( 'La categoría es obligatoria.', 'sistema-pro' ),
                'sop_nivel_prof_id'  => __( 'El nivel es obligatorio.', 'sistema-pro' ),
            );
            foreach ( $athlete_fields as $field => $msg ) {
                if ( isset( $_POST[$field] ) && empty( $_POST[$field] ) ) {
                    wp_send_json_error( $msg );
                }
            }

            // También descripción para atletas
            if ( isset( $_POST['sop_prof_description'] ) && empty( trim( strip_tags( $_POST['sop_prof_description'] ) ) ) ) {
                wp_send_json_error( __( 'La descripción profesional es obligatoria.', 'sistema-pro' ) );
            }
        }
        // ------------------------------------------

        // Actualizar nombre visible
        if ( isset( $_POST['display_name'] ) ) {
            wp_update_user( array( 'ID' => $user_id, 'display_name' => sanitize_text_field( $_POST['display_name'] ) ) );
        }

        // Manejar explícitamente el checkbox (si no viene en POST, está unchecked)
        $consulta_gratis = isset( $_POST['sop_consulta_gratis'] ) ? 'yes' : 'no';
        update_user_meta( $user_id, 'sop_consulta_gratis', $consulta_gratis );

        // Guardar metadatos individuales
        $meta_fields = array( 
            'sop_ubicacion_id', 'sop_nacionalidad_id', 'sop_fecha_nacimiento',
            'sop_pierna_id', 'sop_altura_id', 'sop_peso_id', 'sop_nivel_prof_id', 'sop_categoria_id',
            'sop_prof_description',
            'sop_ocupacion_id', 'sop_experiencia_id',
            'sop_precio_semanal', 'sop_precio_mensual', 'sop_precio_trimestral', 'sop_precio_anual',
            'sop_consulta_gratis',
            'sop_cantidad_sesiones_1', 'sop_precio_sesiones_1',
            'sop_cantidad_sesiones_2', 'sop_precio_sesiones_2',
            'sop_cantidad_sesiones_3', 'sop_precio_sesiones_3',
            'sop_cantidad_sesiones_4', 'sop_precio_sesiones_4',
            'sop_cantidad_sesiones_5', 'sop_precio_sesiones_5',
            'sop_cantidad_sesiones_6', 'sop_precio_sesiones_6'
        );
        foreach ( $meta_fields as $field ) {
            if ( isset( $_POST[$field] ) ) {
                // sop_prof_description contiene HTML de Quill.js (<p>, <br>, <strong>, etc.)
                // wp_kses_post() permite HTML seguro; sanitize_text_field() lo eliminaría
                $new_val = ( $field === 'sop_prof_description' )
                    ? wp_kses_post( stripslashes( $_POST[$field] ) )
                    : sanitize_text_field( $_POST[$field] );
                $old_val = get_user_meta( $user_id, $field, true );

                if ( $new_val !== $old_val ) {
                    if ( empty( $new_val ) ) {
                        delete_user_meta( $user_id, $field );
                    } else {
                        update_user_meta( $user_id, $field, $new_val );
                    }

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

        // --- GESTIÓN DE SUSCRIPCIONES ---
        if ($form_type === 'subscriptions') {
            // Guardar precios de suscripción estándar
            $price_fields = array('sop_precio_semanal', 'sop_precio_mensual', 'sop_precio_trimestral', 'sop_precio_anual');
            foreach ($price_fields as $field) {
                if (isset($_POST[$field])) {
                    update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
                }
            }

            // Guardar paquetes de sesiones
            for ($i = 1; $i <= 6; $i++) {
                $qty_key = 'sop_cantidad_sesiones_' . $i;
                $prc_key = 'sop_precio_sesiones_' . $i;
                
                if (isset($_POST[$qty_key])) {
                    update_user_meta($user_id, $qty_key, sanitize_text_field($_POST[$qty_key]));
                }
                if (isset($_POST[$prc_key])) {
                    update_user_meta($user_id, $prc_key, sanitize_text_field($_POST[$prc_key]));
                }
            }

            // Guardar consulta gratis (checkbox)
            $consulta_gratis = isset($_POST['sop_consulta_gratis']) ? 'yes' : 'no';
            update_user_meta($user_id, 'sop_consulta_gratis', $consulta_gratis);
        }

        // --- ACTUALIZACIÓN DE ESTATUS ---
        $current_status = intval( get_user_meta( $user_id, 'sop_user_status', true ) );

        // 1 -> 2: Paso a Aprobado en proceso (Perfil completo)
        if ( $current_status === 1 && self::check_profile_completion( $user_id ) ) {
            update_user_meta( $user_id, 'sop_user_status', 2 ); // 2: Aprobado en proceso
            $current_status = 2;
            SOP_Debug::log( 'UI', 'User status upgraded to 2 (Aprobado en proceso)', [ 'user_id' => $user_id ] );
        }

        // Manejo de etapa 2 <-> 3
        if ( $current_status >= 2 ) {
            $has_active = false;
            if ( user_can($user_id, 'entrenador') || user_can($user_id, 'especialista') ) {
                $has_active = SOP_Auth::has_active_plans( $user_id );
            } elseif ( user_can($user_id, 'deportista') || user_can($user_id, 'atleta') ) {
                $has_active = SOP_Auth::has_active_subscriptions( $user_id );
            }

            if ( $has_active ) {
                if ( $current_status !== 3 ) {
                    update_user_meta( $user_id, 'sop_user_status', 3 ); // 3: Aprobado / Suscrito
                    SOP_Debug::log( 'UI', 'User status changed to 3', [ 'user_id' => $user_id ] );
                }
            } else {
                if ( $current_status !== 2 ) {
                    update_user_meta( $user_id, 'sop_user_status', 2 ); // Downgrade to Aprobado en proceso
                    SOP_Debug::log( 'UI', 'User status changed to 2', [ 'user_id' => $user_id ] );
                }
            }
        }

        wp_send_json_success( 'Perfil actualizado correctamente' );
    }

    /**
     * Verifica si el usuario ha completado los campos obligatorios para su rol
     */
    public static function check_profile_completion( $user_id ) {
        $user = get_userdata( $user_id );
        if ( ! $user ) return false;

        // Campos Básicos (Para todos)
        $basic_fields = array(
            'display_name'         => $user->display_name,
            'sop_ubicacion_id'     => get_user_meta( $user_id, 'sop_ubicacion_id', true ),
            'sop_nacionalidad_id'  => get_user_meta( $user_id, 'sop_nacionalidad_id', true ),
            'sop_fecha_nacimiento' => get_user_meta( $user_id, 'sop_fecha_nacimiento', true ),
            'sop_idiomas_data'     => get_user_meta( $user_id, 'sop_idiomas_data', true ),
        );

        foreach ( $basic_fields as $field => $value ) {
            if ( empty( $value ) ) return false;
        }

        // Campos Específicos por Rol
        $is_provider = user_can( $user_id, 'entrenador' ) || user_can( $user_id, 'especialista' );
        $is_athlete  = user_can( $user_id, 'atleta' ) || user_can( $user_id, 'deportista' );

        if ( $is_provider ) {
            $prof_desc = get_user_meta( $user_id, 'sop_prof_description', true );
            if ( empty( trim( strip_tags( $prof_desc ) ) ) ) return false;

            if ( empty( get_user_meta( $user_id, 'sop_ocupacion_id', true ) ) ) return false;
            if ( empty( get_user_meta( $user_id, 'sop_experiencia_id', true ) ) ) return false;

            if ( user_can( $user_id, 'entrenador' ) ) {
                if ( empty( get_user_meta( $user_id, 'sop_posiciones_ids', true ) ) ) return false;
                
                $has_offense = get_user_meta( $user_id, 'sop_fase_ofensiva_ids', true );
                $has_defense = get_user_meta( $user_id, 'sop_fase_defensiva_ids', true );
                if ( empty( $has_offense ) && empty( $has_defense ) ) return false;
            }
        }

        if ( $is_athlete ) {
            $athlete_meta = array(
                'sop_pierna_id',
                'sop_altura_id',
                'sop_peso_id',
                'sop_categoria_id',
                'sop_nivel_prof_id',
            );
            foreach ( $athlete_meta as $key ) {
                if ( empty( get_user_meta( $user_id, $key, true ) ) ) return false;
            }

            // También descripción para atletas
            $prof_desc = get_user_meta( $user_id, 'sop_prof_description', true );
            if ( empty( trim( strip_tags( $prof_desc ) ) ) ) return false;
        }

        return true;
    }

    /**
     * Prepara los datos del checkout guardándolos en el servidor.
     * Evita pasar datos sensibles por la URL.
     */
    public function handle_prepare_checkout() {
        if ( ! check_ajax_referer( 'sop_save_pref_nonce', 'nonce', false ) ) {
            wp_send_json_error( 'Error de seguridad' );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            SOP_Debug::log( 'UI', 'Prepare checkout failed: user not identified' );
            wp_send_json_error( 'Usuario no identificado' );
        }

        SOP_Debug::log( 'UI', 'Checkout preparation started', $_POST );

        $trainer_id = intval( $_POST['trainer_id'] );
        $amount = floatval( $_POST['amount'] );
        $plan_name = isset( $_POST['plan'] ) ? sanitize_text_field( $_POST['plan'] ) : 'Plan';

        if ( ! $trainer_id || $amount <= 0 ) {
            wp_send_json_error( 'Datos insuficientes para el checkout.' );
        }

        // Guardar estado temporal del checkout en el meta del usuario
        $checkout_data = array(
            'trainer_id' => $trainer_id,
            'amount'     => $amount,
            'plan_name'  => $plan_name,
            'timestamp'  => time()
        );

        update_user_meta( $user_id, 'sop_pending_checkout_data', $checkout_data );

        wp_send_json_success( 'Checkout preparado.' );
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
        if ( ! $user_id ) {
            SOP_Debug::log( 'UI', 'Simulation failed: user not identified' );
            wp_send_json_error( 'Usuario no identificado' );
        }

        SOP_Debug::log( 'UI', 'Simulating subscription', $_POST );

        $trainer_id = intval( $_POST['trainer_id'] );
        $amount = floatval( $_POST['amount'] );
        $plan_name = isset( $_POST['plan'] ) ? sanitize_text_field( $_POST['plan'] ) : 'Plan';

        if ( !$trainer_id || $amount <= 0 ) {
            wp_send_json_error( 'Datos inválidos para la suscripción.' );
        }

        // Procesa el registro de la transacción simulada en los logs del sistema.
        $transaction_logs = get_option( 'sop_mock_transactions_log', array() );
        
        // Simulación lógica Split (Improvia cobra 10% por ej.)
        $platform_fee = $amount * 0.10;
        $trainer_earning = $amount - $platform_fee;

        $transaction_logs[] = array(
             'id' => uniqid('txn_'),
             'date' => current_time('mysql'),
             'athlete_id' => $user_id,
             'trainer_id' => $trainer_id,
             'total_amount' => $amount,
             'platform_fee' => $platform_fee,
             'trainer_earning' => $trainer_earning,
             'plan_name' => $plan_name,
             'status' => 'FONDOS_RESERVADOS_SIMULADOS'
        );

        update_option( 'sop_mock_transactions_log', $transaction_logs );

        // 3. Crear Post de Suscripción real (para visibilidad en Admin)
        $athlete = get_userdata( $user_id );
        $trainer = get_userdata( $trainer_id );
        
        $post_title = sprintf( 'Sub: %s -> %s (%s)', 
            $athlete ? $athlete->display_name : '#' . $user_id,
            $trainer ? $trainer->display_name : '#' . $trainer_id,
            $plan_name
        );

        $subscription_post_id = wp_insert_post( array(
            'post_title'   => $post_title,
            'post_status'  => 'publish',
            'post_type'    => 'subscription',
        ) );

        if ( $subscription_post_id ) {
            update_post_meta( $subscription_post_id, 'sop_txn_id', $transaction_logs[count($transaction_logs)-1]['id'] );
            update_post_meta( $subscription_post_id, 'athlete_id', $user_id );
            update_post_meta( $subscription_post_id, 'trainer_id', $trainer_id );
            update_post_meta( $subscription_post_id, 'amount', $amount );
            update_post_meta( $subscription_post_id, 'plan_name', $plan_name );
            update_post_meta( $subscription_post_id, 'status', 'FONDOS_RESERVADOS_SIMULADOS' );
        }

        wp_send_json_success( 'Fondos reservados en garantía.' );
    }

    /**
     * Procesa la aceptación o rechazo de una solicitud técnica
     */
    public function handle_solicitude_approval() {
        if ( ! check_ajax_referer( 'sop_save_pref_nonce', 'nonce', false ) ) {
            wp_send_json_error( 'Error de seguridad' );
        }

        $user_id = get_current_user_id(); // El entrenador
        if ( ! $user_id ) {
            SOP_Debug::log( 'UI', 'Solicitude approval failed: user not identified' );
            wp_send_json_error( 'No tienes permisos' );
        }

        SOP_Debug::log( 'UI', 'Handling solicitude action', [ 'txn_id' => $txn_id ] );
        $txn_id = sanitize_text_field( $_POST['txn_id'] );
        $action = sanitize_text_field( $_POST['action_type'] ); // 'accept' o 'reject'

        $logs = get_option( 'sop_mock_transactions_log', array() );
        $found = false;

        foreach ( $logs as &$log ) {
            if ( $log['id'] === $txn_id && intval($log['trainer_id']) === $user_id ) {
                if ( $action === 'accept' ) {
                    $log['status'] = 'SUSCRIPCION_ACTIVA';
                    
                    // Asegurar que el atleta tenga al entrenador en su lista
                    $athlete_trainers = get_user_meta( $log['athlete_id'], 'sop_subscribed_trainers', true );
                    if ( !is_array($athlete_trainers) ) $athlete_trainers = array();
                    if ( !in_array($user_id, $athlete_trainers) ) {
                        $athlete_trainers[] = $user_id;
                        update_user_meta( $log['athlete_id'], 'sop_subscribed_trainers', $athlete_trainers );
                        
                        // Actualizar estatus del atleta a 3 (Suscrito)
                        $athlete_status = intval( get_user_meta( $log['athlete_id'], 'sop_user_status', true ) );
                        if ( $athlete_status === 2 ) {
                            update_user_meta( $log['athlete_id'], 'sop_user_status', 3 );
                            SOP_Debug::log( 'UI', 'Athlete status upgraded to 3 after subscription approval', [ 'athlete_id' => $log['athlete_id'] ] );
                        }
                    }
                } else {
                    $log['status'] = 'SOLICITUD_RECHAZADA_LIBERADA';
                }
                $found = true;
                break;
            }
        }

        if ( $found ) {
            update_option( 'sop_mock_transactions_log', $logs );

            // Sincronizar con el post de suscripción
            $sub_posts = get_posts( array(
                'post_type'  => 'subscription',
                'meta_key'   => 'sop_txn_id',
                'meta_value' => $txn_id,
                'posts_per_page' => 1
            ) );

            if ( !empty($sub_posts) ) {
                update_post_meta( $sub_posts[0]->ID, 'status', $action === 'accept' ? 'SUSCRIPCION_ACTIVA' : 'SOLICITUD_RECHAZADA_LIBERADA' );
            }

            wp_send_json_success( $action === 'accept' ? __( 'Suscripción activada con éxito.', 'sistema-pro' ) : __( 'Solicitud rechazada y fondos liberados.', 'sistema-pro' ) );
        } else {
            wp_send_json_error( __( 'Transacción no encontrada o permiso denegado.', 'sistema-pro' ) );
        }
    }

    /**
     * Actualiza el correo electrónico del usuario
     */
    public function handle_update_email() {
        if ( ! check_ajax_referer( 'sop_save_pref_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Error de seguridad.', 'sistema-pro' ) );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( __( 'Usuario no identificado.', 'sistema-pro' ) );

        $new_email = sanitize_email( $_POST['new_email'] );

        if ( ! is_email( $new_email ) ) {
            wp_send_json_error( __( 'El correo electrónico no es válido.', 'sistema-pro' ) );
        }

        if ( email_exists( $new_email ) ) {
            wp_send_json_error( __( 'Este correo ya está en uso por otro usuario.', 'sistema-pro' ) );
        }

        $result = wp_update_user( array( 'ID' => $user_id, 'user_email' => $new_email ) );

        if ( is_wp_error( $result ) ) {
            SOP_Debug::log( 'UI', 'Email update failed', [ 'user_id' => $user_id, 'error' => $result->get_error_message() ] );
            wp_send_json_error( $result->get_error_message() );
        }

        SOP_Debug::log( 'UI', 'Email updated successfully', [ 'user_id' => $user_id, 'new_email' => $new_email ] );

        wp_send_json_success( __( 'Correo electrónico actualizado correctamente.', 'sistema-pro' ) );
    }

    /**
     * Actualiza la contraseña del usuario
     */
    public function handle_update_password() {
        if ( ! check_ajax_referer( 'sop_save_pref_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Error de seguridad.', 'sistema-pro' ) );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( __( 'Usuario no identificado.', 'sistema-pro' ) );

        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        $user = get_userdata( $user_id );

        if ( ! wp_check_password( $current_pass, $user->user_pass, $user->ID ) ) {
            wp_send_json_error( __( 'La contraseña actual es incorrecta.', 'sistema-pro' ) );
        }

        if ( $new_pass !== $confirm_pass ) {
            wp_send_json_error( __( 'Las nuevas contraseñas no coinciden.', 'sistema-pro' ) );
        }

        if ( strlen( $new_pass ) < 6 ) {
            wp_send_json_error( __( 'La nueva contraseña debe tener al menos 6 caracteres.', 'sistema-pro' ) );
        }

        wp_set_password( $new_pass, $user_id );
        SOP_Debug::log( 'UI', 'Password updated successfully', [ 'user_id' => $user_id ] );

        // Iniciar sesión de nuevo automáticamente porque wp_set_password cierra la sesión
        $creds = array(
            'user_login'    => $user->user_login,
            'user_password' => $new_pass,
            'remember'      => true
        );
        wp_signon( $creds, false );

        wp_send_json_success( __( 'Contraseña actualizada correctamente.', 'sistema-pro' ) );
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
            wp_enqueue_style( 'sop-' . $filename, $url, array('sop-base-style'), '1.1.7' );
        }
        
        // Tom Select
        wp_enqueue_style( 'tom-select-css', 'https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css', array(), '2.2.2' );
        wp_enqueue_style( 'tom-select-custom-css', SOP_URL . 'assets/css/components/tom-select-custom.css', array('tom-select-css'), '1.0.0' );
        wp_enqueue_script( 'tom-select-js', 'https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js', array(), '2.2.2', true );

        // Flatpickr
        wp_enqueue_style( 'flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13' );
        wp_enqueue_script( 'flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true );
        wp_enqueue_script( 'flatpickr-es', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', array('flatpickr-js'), '4.6.13', true );

        // Quill.js (WYSIWYG Editor)
        wp_enqueue_style( 'quill-snow-css', 'https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css', array(), '2.0.2' );
        wp_enqueue_script( 'quill-js', 'https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js', array(), '2.0.2', true );

        // Encolar JS
        wp_enqueue_script( 'sop-settings-js', SOP_URL . 'assets/js/settings.js', array('jquery', 'flatpickr-js'), '1.0.0', true );
        wp_localize_script( 'sop-settings-js', 'sop_ajax', array(
            'ajax_url'     => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'sop_save_pref_nonce' ),
            'required_msg' => __( 'Este campo es obligatorio', 'sistema-pro' ),
            'user_lang'    => get_user_meta( get_current_user_id(), 'sop_user_language', true ) ?: 'es_ES'
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

        if ( is_page('login') ) {
            $classes[] = 'sop-login-page';
        }

        if ( is_page('registro') ) {
            $classes[] = 'sop-register-page';
        }

        if ( is_page('perfil') ) {
            $classes[] = 'sop-profile-page';
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
        if ( empty( $code ) || strlen( $code ) < 2 ) return '';
        $code = strtolower( substr( $code, 0, 2 ) );
        return "https://flagcdn.com/" . $code . ".svg";
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

    /**
     * Define las columnas personalizadas para el CPT Subscription
     */
    public function subscription_columns( $columns ) {
        $new_columns = array(
            'cb'         => $columns['cb'],
            'title'      => $columns['title'],
            'athlete'    => 'Atleta',
            'trainer'    => 'Entrenador',
            'amount'     => 'Importe',
            'plan'       => 'Plan',
            'status'     => 'Estado',
            'date'       => $columns['date'],
        );
        return $new_columns;
    }

    /**
     * Renderiza el contenido de las columnas personalizadas
     */
    public function subscription_column_content( $column, $post_id ) {
        switch ( $column ) {
            case 'athlete':
                $id = get_post_meta( $post_id, 'athlete_id', true );
                $u = get_userdata( $id );
                echo $u ? esc_html( $u->display_name ) : '#' . $id;
                break;
            case 'trainer':
                $id = get_post_meta( $post_id, 'trainer_id', true );
                $u = get_userdata( $id );
                echo $u ? esc_html( $u->display_name ) : '#' . $id;
                break;
            case 'amount':
                echo esc_html( get_post_meta( $post_id, 'amount', true ) ) . '€';
                break;
            case 'plan':
                echo esc_html( get_post_meta( $post_id, 'plan_name', true ) );
                break;
            case 'status':
                $status = get_post_meta( $post_id, 'status', true );
                $status_labels = [
                    'SUSCRIPCION_ACTIVA'           => [ 'text' => __( 'ACTIVA', 'sistema-pro' ), 'color' => '#10b981' ],
                    'FONDOS_RESERVADOS_SIMULADOS' => [ 'text' => __( 'RESERVADO', 'sistema-pro' ), 'color' => '#f59e0b' ],
                    'SOLICITUD_RECHAZADA_LIBERADA' => [ 'text' => __( 'LIBERADO', 'sistema-pro' ), 'color' => '#ef4444' ],
                ];

                if ( isset( $status_labels[$status] ) ) {
                    $label = $status_labels[$status];
                    printf( 
                        '<span style="color: %s; font-weight: bold;">%s</span>', 
                        esc_attr( $label['color'] ), 
                        esc_html( $label['text'] ) 
                    );
                } else {
                    echo esc_html( $status );
                }
                break;
        }
    }
}
