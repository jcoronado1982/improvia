<?php
/**
 * Vista para el formulario de inicio de sesión
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Si el usuario ya está conectado, mostrar mensaje y enlace de cierre
if ( is_user_logged_in() ) {
    echo '<div class="sop-alert">' . esc_html__( 'Ya has iniciado sesión.', 'sistema-pro' ) . ' <a href="' . esc_url( wp_logout_url( home_url( '/home' ) ) ) . '">' . esc_html__( 'Cerrar Sesión', 'sistema-pro' ) . '</a></div>';
    return;
}
?>

<div class="sop-login-container">
    <div class="sop-auth-header">
        <h2><?php esc_html_e( 'Bienvenido de nuevo', 'sistema-pro' ); ?></h2>
        <p><?php esc_html_e( 'Ingresa tus credenciales para continuar', 'sistema-pro' ); ?></p>
    </div>

    <form method="post" action="">
        <div class="sop-auth-row">
            <label for="sop_user" class="sop-label sop-label-light"><?php esc_html_e( 'Usuario o Correo', 'sistema-pro' ); ?></label>
            <input type="text" name="sop_log" id="sop_user" class="sop-input sop-input-light" required>
        </div>
        
        <div class="sop-auth-row-large">
            <label for="sop_pass" class="sop-label sop-label-light"><?php esc_html_e( 'Contraseña', 'sistema-pro' ); ?></label>
            <input type="password" name="sop_pwd" id="sop_pass" class="sop-input sop-input-light" required>
        </div>

        <?php wp_nonce_field( 'sop_login_action', 'sop_nonce' ); ?>
        
        <button type="submit" name="sop_login_submit" class="sop-btn-nav sop-btn-register sop-btn-full">
            <?php esc_html_e( 'Entrar al Sistema', 'sistema-pro' ); ?>
        </button>
        
        <div class="sop-auth-footer">
            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( '¿Olvidaste tu contraseña?', 'sistema-pro' ); ?></a>
        </div>
    </form>
</div>
