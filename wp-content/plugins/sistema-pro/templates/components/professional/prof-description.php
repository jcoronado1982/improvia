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
        <?php esc_html_e( 'DESCRIPCION PROFESIONAL', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span>
        <span id="sop-edit-prof-desc-btn" style="cursor: pointer; float: right; display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: transparent; border: 1px solid currentColor; border-radius: 6px; transition: background 0.3s; opacity: 0.5;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
        </span>
    </h3>
    
    <!-- Professional Editor UI (Quill.js) -->
    <div id="sop-prof-editor-wrapper" style="display: none; margin-bottom: 20px;">
        <div id="sop-quill-editor" style="height: 200px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 0 0 8px 8px; color: inherit; font-size: 1rem;"></div>
    </div>

    <div id="sop-prof-desc-display" class="sop-prof-desc-text" style="min-height: 100px; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); line-height: 1.6;">
        <?php echo !empty($prof_description) ? $prof_description : esc_html($default_desc); ?>
    </div>
    
    <textarea id="sop-prof-desc-input" name="sop_prof_description" style="display: none;"><?php echo esc_textarea($prof_description); ?></textarea>
</div>
