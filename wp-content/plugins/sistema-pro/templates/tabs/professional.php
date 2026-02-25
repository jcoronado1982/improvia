<?php
/**
 * Template para la pestaña de Información Profesional
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$user = wp_get_current_user();

// Taxonomías dinámicas
$piernas = get_terms( array( 'taxonomy' => 'sop_pierna', 'hide_empty' => false ) );
$alturas = get_terms( array( 'taxonomy' => 'sop_altura', 'hide_empty' => false ) );
$pesos = get_terms( array( 'taxonomy' => 'sop_peso', 'hide_empty' => false ) );
$niveles_prof = get_terms( array( 'taxonomy' => 'sop_nivel_prof', 'hide_empty' => false ) );
$redes_sociales = get_terms( array( 'taxonomy' => 'sop_red_social', 'hide_empty' => false ) );

// Metadatos
$pierna_act = get_user_meta( $user->ID, 'sop_pierna_id', true );
$altura_act = get_user_meta( $user->ID, 'sop_altura_id', true );
$peso_act = get_user_meta( $user->ID, 'sop_peso_id', true );
$nivel_prof_act = get_user_meta( $user->ID, 'sop_nivel_prof_id', true );
$prof_description = get_user_meta( $user->ID, 'sop_prof_description', true );

$default_desc = 'Lorem ipsum dolor sit amet consectetur. Pretium at libero fermentum in vulputate. Viverra cum non ultricies tempor arcu in accumsan eu. Fringilla ut nulla neque leo phasellus tellus. Dignissim ante pulvinar purus in non tristique sed cursus. Ac sapien amet tellus quam pulvinar. Ac ipsum rutrum ac gravida massa iaculis sociis etiam. Facilisis augue auctor risus elementum. Aenean duis egestas amet urna viverra vitae bibendum blandit gravida.';
?>

<div>
    <?php wp_nonce_field( 'sop_profile_nonce', 'nonce' ); ?>
    <div class="sop-tab-split-md">
        <!-- INFORMACIÓN PARA MI ESPECIALISTA -->
        <div class="sop-tab-panel sop-tab-panel-flex-main">
            <h3 class="sop-title-with-line"><?php esc_html_e( 'INFORMACIÓN PARA MI ESPECIALISTA', 'sistema-pro' ); ?></h3>
            <div class="sop-tab-grid-4-sm">
                <div>
                    <label class="sop-label"><?php esc_html_e( 'Pierna dominante', 'sistema-pro' ); ?></label>
                    <select name="sop_pierna_id" class="sop-input">
                        <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                        <?php foreach ($piernas as $p) : ?>
                            <option value="<?php echo $p->term_id; ?>" <?php selected($pierna_act, $p->term_id); ?>><?php echo esc_html($p->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="sop-label"><?php esc_html_e( 'Altura (cm)', 'sistema-pro' ); ?></label>
                    <select name="sop_altura_id" class="sop-input">
                        <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                        <?php foreach ($alturas as $a) : ?>
                            <option value="<?php echo $a->term_id; ?>" <?php selected($altura_act, $a->term_id); ?>><?php echo esc_html($a->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="sop-label"><?php esc_html_e( 'Peso', 'sistema-pro' ); ?></label>
                    <select name="sop_peso_id" class="sop-input">
                        <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                        <?php foreach ($pesos as $pes) : ?>
                            <option value="<?php echo $pes->term_id; ?>" <?php selected($peso_act, $pes->term_id); ?>><?php echo esc_html($pes->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="sop-label"><?php esc_html_e( 'Nivel', 'sistema-pro' ); ?></label>
                    <select name="sop_nivel_prof_id" class="sop-input">
                        <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                        <?php foreach ($niveles_prof as $np) : ?>
                            <option value="<?php echo $np->term_id; ?>" <?php selected($nivel_prof_act, $np->term_id); ?>><?php echo esc_html($np->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- PROFESSIONAL DESCRIPTION -->
        <div class="sop-tab-panel sop-tab-panel-flex-side">
            <h3 class="sop-title-with-line">
                <?php esc_html_e( 'PROFESSIONAL DESCRIPTION', 'sistema-pro' ); ?> 
                <span id="sop-edit-prof-desc-btn" style="cursor: pointer; float: right; display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: transparent; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; transition: background 0.3s;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #fff;"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                </span>
            </h3>
            
            <div id="sop-prof-desc-display" style="font-size: 1rem; line-height: 1.6; opacity: 0.8; white-space: pre-wrap;"><?php echo !empty($prof_description) ? nl2br(esc_html(trim($prof_description))) : esc_html($default_desc); ?></div>
            
            <textarea id="sop-prof-desc-input" name="sop_prof_description" class="sop-input" style="height: 150px; resize: none; display: none;" placeholder="<?php echo esc_attr($default_desc); ?>"><?php echo esc_textarea($prof_description); ?></textarea>
        </div>
    </div>

    <!-- ESTUDIO PRINCIPAL -->
    <div class="sop-tab-panel sop-tab-panel-basic">
        <h3 class="sop-title-with-line"><?php esc_html_e( 'ESTUDIO PRINCIPAL', 'sistema-pro' ); ?></h3>
        <div class="sop-tab-upload-area">
            <div style="text-align: center;">
                <i style="opacity: 0.5;">📎</i>
                <span style="font-size: 1rem; opacity: 0.7; margin-left: 10px;">PDF / JPF</span>
            </div>
            
            <div class="sop-tab-warning-box">
                <div style="display: flex; gap: 15px; align-items: flex-start;">
                    <i style="color: #ffab00; font-size: 1.2rem;">⚠️</i>
                    <p style="margin: 0; font-size: 0.85rem; line-height: 1.5; opacity: 0.8; color: #fff;">
                        <?php esc_html_e( 'Adjunta tu título o certificación para su verificación, de no adjuntarlo tu título no aparecerá para los Entrenadores', 'sistema-pro' ); ?><br><br>
                        <?php esc_html_e( 'Recuerda que los entrenadores necesitan claridad y transparencia verificando que jugadores como tu son realmente titulados y puedan ofrecerte la ayuda que necesitas', 'sistema-pro' ); ?>
                    </p>
                </div>
            </div>

            <div style="position: absolute; bottom: 20px; left: 20px;">
                <div class="sop-tab-badge">
                    <i style="color: #fff; font-size: 1rem;">✓</i>
                    <span>Fifa ID.pdf</span>
                    <span style="cursor: pointer; opacity: 0.5;">✕</span>
                </div>
            </div>
        </div>
    </div>

    <!-- PROFESSIONAL RRSS -->
    <div class="sop-tab-panel sop-tab-panel-basic">
        <h3 class="sop-title-with-line"><?php esc_html_e( 'PROFESSIONAL RRSS', 'sistema-pro' ); ?></h3>
        <div class="sop-tab-inline-form">
            <div style="flex: 1; max-width: 250px;">
                <label class="sop-label"><?php esc_html_e( 'Red social', 'sistema-pro' ); ?></label>
                <select id="sop-rrss-type" class="sop-input">
                    <?php foreach ($redes_sociales as $red) : ?>
                        <option value="<?php echo $red->term_id; ?>"><?php echo esc_html($red->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1; max-width: 350px;">
                <label class="sop-label">@Example</label>
                <input type="text" id="sop-rrss-value" class="sop-input" placeholder="@Example">
            </div>
            <button type="button" id="sop-add-rrss" class="sop-btn-blue"><?php esc_html_e( 'Add', 'sistema-pro' ); ?></button>
        </div>

        <div id="sop-rrss-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
            <div class="sop-tab-badge">
                <span>Instagram: @Jugador1</span>
                <span style="cursor: pointer; opacity: 0.5;">✕</span>
            </div>
        </div>
    </div>
</div>
