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
    <div class="sop-security-section" id="sop-email-section">
        <h4 class="sop-security-title"><?php esc_html_e( 'CORREO ELECTRÓNICO', 'sistema-pro' ); ?></h4>
        
        <!-- Display State -->
        <div class="sop-security-box sop-display-state">
            <span class="sop-security-label"><?php esc_html_e( 'Correo actual', 'sistema-pro' ); ?></span>
            <span class="sop-security-badge"><?php echo esc_html($user_email); ?></span>
            <button type="button" class="sop-btn-blue sop-toggle-edit"><?php esc_html_e( 'Cambiar correo', 'sistema-pro' ); ?></button>
        </div>

        <!-- Edit State (Hidden) -->
        <div class="sop-security-box sop-edit-state" style="display: none;">
            <form id="sop-update-email-form">
                <div class="sop-security-form-row">
                    <label class="sop-security-label"><?php esc_html_e( 'Nuevo correo', 'sistema-pro' ); ?></label>
                    <input type="email" name="new_email" class="sop-security-input" value="<?php echo esc_attr($user_email); ?>" required>
                </div>
                <div class="sop-security-actions">
                    <button type="submit" class="sop-btn-blue"><?php esc_html_e( 'Guardar', 'sistema-pro' ); ?></button>
                    <button type="button" class="sop-btn-white sop-toggle-edit"><?php esc_html_e( 'Cancelar', 'sistema-pro' ); ?></button>
                </div>
                <div class="sop-security-msg"></div>
            </form>
        </div>
    </div>

    <!-- SECCIÓN CONTRASEÑA -->
    <div class="sop-security-section" id="sop-password-section">
        <h4 class="sop-security-title"><?php esc_html_e( 'CONTRASEÑA', 'sistema-pro' ); ?></h4>
        
        <!-- Display State -->
        <div class="sop-security-box sop-display-state">
            <span class="sop-security-label"><?php esc_html_e( 'Contraseña actual', 'sistema-pro' ); ?></span>
            <span class="sop-security-badge">********</span>
            <button type="button" class="sop-btn-blue sop-toggle-edit"><?php esc_html_e( 'Cambiar contraseña', 'sistema-pro' ); ?></button>
        </div>

        <!-- Edit State (Hidden) -->
        <div class="sop-security-box sop-edit-state" style="display: none;">
            <form id="sop-update-password-form">
                <div class="sop-security-form-row">
                    <label class="sop-security-label"><?php esc_html_e( 'Contraseña actual', 'sistema-pro' ); ?></label>
                    <input type="password" name="current_password" class="sop-security-input" required>
                </div>
                <div class="sop-security-form-row">
                    <label class="sop-security-label"><?php esc_html_e( 'Nueva contraseña', 'sistema-pro' ); ?></label>
                    <input type="password" name="new_password" class="sop-security-input" required>
                </div>
                <div class="sop-security-form-row">
                    <label class="sop-security-label"><?php esc_html_e( 'Confirmar nueva contraseña', 'sistema-pro' ); ?></label>
                    <input type="password" name="confirm_password" class="sop-security-input" required>
                </div>
                <div class="sop-security-actions">
                    <button type="submit" class="sop-btn-blue"><?php esc_html_e( 'Guardar', 'sistema-pro' ); ?></button>
                    <button type="button" class="sop-btn-white sop-toggle-edit"><?php esc_html_e( 'Cancelar', 'sistema-pro' ); ?></button>
                </div>
                <div class="sop-security-msg"></div>
            </form>
        </div>
    </div>

</div>

<style>
.sop-security-form-row {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.sop-security-input {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 10px 15px;
    color: #fff;
    width: 100%;
}
.sop-provider-theme-light .sop-security-input {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    color: #111827;
}
.sop-security-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}
.sop-security-box form {
    width: 100%;
}
.sop-security-msg {
    margin-top: 10px;
    font-size: 13px;
}
</style>
