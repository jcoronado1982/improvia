<?php
/**
 * Componente Reutilizable: RRSS (Redes Sociales)
 * Se muestra en el perfil público del entrenador y en la previsualización.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- Social Media -->
<div class="sop-preview-card sop-full-width sop-rrss-card">
    <h3 class="sop-preview-card-title"><?php esc_html_e( 'RRSS', 'sistema-pro' ); ?></h3>
    <div class="sop-rrss-icons">
        <img src="<?php echo esc_url( SOP_URL . 'assets/images/youtube.png' ); ?>" alt="YT" class="sop-rrss-icon-img">
        <img src="<?php echo esc_url( SOP_URL . 'assets/images/linkedin.png' ); ?>" alt="IN" class="sop-rrss-icon-img">
        <img src="<?php echo esc_url( SOP_URL . 'assets/images/instagram-logo.png' ); ?>" alt="IG" class="sop-rrss-icon-img">
        <img src="<?php echo esc_url( SOP_URL . 'assets/images/zap.png' ); ?>" alt="TK" class="sop-rrss-icon-img">
    </div>
</div>
