<!-- SOP DEBUG: PROFESSIONAL DESCRIPTION RENDERED -->
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
    <div class="sop-title-with-line sop-prof-title-row">
        <h3 style="margin: 0;">
            <?php esc_html_e( 'DESCRIPCION PROFESIONAL', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span>
        </h3>
        <span id="sop-edit-prof-desc-btn" class="sop-edit-icon-btn" title="<?php esc_attr_e( 'Editar descripción', 'sistema-pro' ); ?>">
            <img src="<?php echo esc_url( SOP_URL . 'assets/images/edit.png' ); ?>" alt="Edit" style="width: 24px; height: 24px;">
        </span>
    </div>
    
    <!-- Professional Editor UI (Quill.js) -->
    <div id="sop-prof-editor-wrapper" style="display: none; margin-bottom: 20px;">
        <div id="sop-quill-editor" style="height: 200px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 0 0 8px 8px; color: inherit; font-size: 1rem;"></div>
    </div>

    <div id="sop-prof-desc-display" class="sop-prof-desc-display sop-prof-desc-text">
        <?php echo !empty($prof_description) ? $prof_description : esc_html($default_desc); ?>
    </div>
    
    <textarea id="sop-prof-desc-input" name="sop_prof_description" style="display: none;"><?php echo esc_textarea($prof_description); ?></textarea>
</div>
