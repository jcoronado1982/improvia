<?php
/**
 * Template para la pestaña de Ajustes
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="sop-settings-container">
    <div class="sop-settings-grid">
        
        <!-- MODULE: NOTIFICACIONES -->
        <div class="sop-settings-panel">
            <h4 class="sop-settings-title"><?php esc_html_e( 'NOTIFICACIONES', 'sistema-pro' ); ?></h4>
            <div class="sop-settings-list">
                
                <!-- Toggle 1 -->
                <div class="sop-settings-item">
                    <div class="sop-settings-item-text">
                        <strong><?php esc_html_e( 'Nuevos Mensajes', 'sistema-pro' ); ?></strong>
                        <span><?php esc_html_e( 'Recibe notificaciones cuando obtengas nuevos mensajes', 'sistema-pro' ); ?></span>
                    </div>
                    <label class="sop-toggle">
                        <input type="checkbox" checked>
                        <span class="sop-slider"></span>
                    </label>
                </div>

                <!-- Toggle 2 -->
                <div class="sop-settings-item">
                    <div class="sop-settings-item-text">
                        <strong><?php esc_html_e( 'Nuevos Comentarios', 'sistema-pro' ); ?></strong>
                        <span><?php esc_html_e( 'Recibe notificaciones cuando obtengas nuevos comentarios', 'sistema-pro' ); ?></span>
                    </div>
                    <label class="sop-toggle">
                        <input type="checkbox" checked>
                        <span class="sop-slider"></span>
                    </label>
                </div>

                <!-- Toggle 3 -->
                <div class="sop-settings-item">
                    <div class="sop-settings-item-text">
                        <strong><?php esc_html_e( 'Nuevos Seguidores', 'sistema-pro' ); ?></strong>
                        <span><?php esc_html_e( 'Recibe notificaciones cuando obtengas nuevos seguidores', 'sistema-pro' ); ?></span>
                    </div>
                    <label class="sop-toggle">
                        <input type="checkbox" checked>
                        <span class="sop-slider"></span>
                    </label>
                </div>

                <!-- Toggle 4 -->
                <div class="sop-settings-item">
                    <div class="sop-settings-item-text">
                        <strong><?php esc_html_e( 'Promociones', 'sistema-pro' ); ?></strong>
                        <span><?php esc_html_e( 'Recibe notificaciones acerca de promociones', 'sistema-pro' ); ?></span>
                    </div>
                    <label class="sop-toggle">
                        <input type="checkbox" checked>
                        <span class="sop-slider"></span>
                    </label>
                </div>

            </div>
        </div>

        <!-- MODULE: IDIOMA -->
        <div class="sop-settings-panel">
            <h4 class="sop-settings-title"><?php esc_html_e( 'IDIOMA', 'sistema-pro' ); ?></h4>
            <?php 
                $user_id = get_current_user_id();
                $current_lang = get_user_meta( $user_id, 'sop_user_language', true );
                if ( empty( $current_lang ) ) {
                    $current_lang = get_locale();
                }
                
                $display_lang = ( $current_lang === 'en_US' ) ? 'English' : 'Español';
            ?>
            <div class="sop-lang-box">
                <span class="sop-lang-badge" id="sop_current_lang_badge"><?php echo esc_html( $display_lang ); ?></span>
                <select class="sop-lang-select sop-user-lang-preference sop-tom-select" id="sop_lang_pref" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <option value="es" <?php selected( $current_lang, 'es_ES' ); ?>><?php esc_html_e( 'Español', 'sistema-pro' ); ?></option>
                    <option value="en_US" <?php selected( $current_lang, 'en_US' ); ?>><?php esc_html_e( 'English', 'sistema-pro' ); ?></option>
                </select>
            </div>
        </div>

        <!-- MODULE: CANCELACION -->
        <div class="sop-settings-panel full-width">
            <h4 class="sop-settings-title"><?php esc_html_e( 'CANCELACION', 'sistema-pro' ); ?></h4>
            
            <div class="sop-cancellation-box">
                <h4><?php esc_html_e( '¿Qué ocurre cuando elimino mi cuenta?', 'sistema-pro' ); ?></h4>
                <ul class="sop-cancellation-list">
                    <li><?php esc_html_e( 'Tu perfil y tus trabajos ya no sere mostraran en Improvia', 'sistema-pro' ); ?></li>
                    <li><?php esc_html_e( 'Las solicitudes o contrataciones seran canceladas', 'sistema-pro' ); ?></li>
                    <li><?php esc_html_e( 'Si tu entrenador o especialista ya esta trabajando el dinero no sera reembolsable', 'sistema-pro' ); ?></li>
                </ul>

                <div class="sop-cancellation-actions">
                    <div>
                        <span class="sop-label" style="text-align: left; font-size: 0.8rem; opacity: 0.6; display: block; margin-bottom: 5px;"><?php esc_html_e( 'Me retiro porque...', 'sistema-pro' ); ?></span>
                        <select class="sop-select-dark">
                            <option value=""><?php esc_html_e( 'Elegir una razon', 'sistema-pro' ); ?></option>
                            <option value="1"><?php esc_html_e( 'No entiendo la plataforma', 'sistema-pro' ); ?></option>
                            <option value="2"><?php esc_html_e( 'Es muy caro', 'sistema-pro' ); ?></option>
                            <option value="3"><?php esc_html_e( 'Otro motivo', 'sistema-pro' ); ?></option>
                        </select>
                    </div>
                    <button type="button" class="sop-btn-blue"><?php esc_html_e( 'Eliminar cuenta', 'sistema-pro' ); ?></button>
                </div>
            </div>
        </div>

    </div>
</div>
