<?php
/**
 * Componente compartido: Descripción Profesional
 * Se usa tanto para Atletas como para Entrenadores/Especialistas
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$prof_description = get_user_meta( $user->ID, 'sop_prof_description', true );
$default_desc = __( 'Escribe aquí tu descripción profesional...', 'sistema-pro' );
?>

<div class="sop-tab-panel sop-tab-panel-flex-side">
    <h3 class="sop-title-with-line">
        <?php esc_html_e( 'DESCRIPCION PROFESIONAL', 'sistema-pro' ); ?> 
        <span id="sop-edit-prof-desc-btn" style="cursor: pointer; float: right; display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: transparent; border: 1px solid currentColor; border-radius: 6px; transition: background 0.3s; opacity: 0.5;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
        </span>
    </h3>
    
    <div id="sop-prof-desc-display" class="sop-prof-desc-text"><?php echo !empty($prof_description) ? nl2br(esc_html(trim($prof_description))) : esc_html($default_desc); ?></div>
    
    <textarea id="sop-prof-desc-input" name="sop_prof_description" class="sop-input" style="height: 150px; resize: none; display: none;" placeholder="<?php echo esc_attr($default_desc); ?>"><?php echo esc_textarea($prof_description); ?></textarea>
</div>
