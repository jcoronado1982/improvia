<?php
/**
 * Template para la pestaña de Seguridad
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$current_user = wp_get_current_user();
$user_email = $current_user->user_email;
?>

<div class="sop-security-container">

    <!-- SECCIÓN CORREO -->
    <div class="sop-security-section">
        <h4 class="sop-security-title"><?php esc_html_e( 'CORREO ELECTRÓNICO', 'sistema-pro' ); ?></h4>
        <div class="sop-security-box">
            <span class="sop-security-label"><?php esc_html_e( 'Correo actual', 'sistema-pro' ); ?></span>
            <span class="sop-security-badge"><?php echo esc_html($user_email); ?></span>
            <button type="button" class="sop-btn-blue"><?php esc_html_e( 'Cambiar correo', 'sistema-pro' ); ?></button>
        </div>
    </div>

    <!-- SECCIÓN CONTRASEÑA -->
    <div class="sop-security-section">
        <h4 class="sop-security-title"><?php esc_html_e( 'CONTRASEÑA', 'sistema-pro' ); ?></h4>
        <div class="sop-security-box">
            <span class="sop-security-label"><?php esc_html_e( 'Contraseña actual', 'sistema-pro' ); ?></span>
            <span class="sop-security-badge">********</span>
            <button type="button" class="sop-btn-blue"><?php esc_html_e( 'Cambiar contraseña', 'sistema-pro' ); ?></button>
        </div>
    </div>

</div>
