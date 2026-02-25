<?php
/**
 * Vista para el formulario de registro
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Si el usuario ya está conectado
if ( is_user_logged_in() ) {
    echo '<div class="sop-alert">' . esc_html__( 'Ya tienes una cuenta activa.', 'sistema-pro' ) . '</div>';
    return;
}

// Mostrar errores si fueron pasados desde el controlador
if ( ! empty( $this->reg_errors ) ) {
    echo '<div class="sop-alert sop-error" style="background:#ffebee; border-left-color:#f44336; margin-bottom:20px; color: #d32f2f;">';
    foreach ( $this->reg_errors as $error ) {
        echo '<p style="margin: 0 0 5px 0;"><strong>' . esc_html__( 'Error:', 'sistema-pro' ) . '</strong> ' . esc_html( $error ) . '</p>';
    }
    echo '</div>';
}
?>

<div class="sop-login-container sop-register-container">
    <div class="sop-auth-header">
        <h2><?php esc_html_e( 'Crear tu cuenta', 'sistema-pro' ); ?></h2>
        <p><?php esc_html_e( 'Únete a la plataforma IMPROVIA', 'sistema-pro' ); ?></p>
    </div>

    <form method="post" action="">
        <div class="sop-auth-grid">
            <div>
                <label for="reg_nombre" class="sop-label sop-label-light"><?php esc_html_e( 'Nombre Completo', 'sistema-pro' ); ?></label>
                <input type="text" name="reg_nombre" id="reg_nombre" class="sop-input sop-input-light" value="<?php echo isset($_POST['reg_nombre']) ? esc_attr($_POST['reg_nombre']) : ''; ?>" required>
            </div>
            <div>
                <label for="reg_user" class="sop-label sop-label-light"><?php esc_html_e( 'Nombre de Usuario', 'sistema-pro' ); ?></label>
                <input type="text" name="reg_user" id="reg_user" class="sop-input sop-input-light" value="<?php echo isset($_POST['reg_user']) ? esc_attr($_POST['reg_user']) : ''; ?>" required>
            </div>
        </div>

        <div class="sop-auth-grid">
            <div>
                <label for="reg_email" class="sop-label sop-label-light"><?php esc_html_e( 'Correo Electrónico', 'sistema-pro' ); ?></label>
                <input type="email" name="reg_email" id="reg_email" class="sop-input sop-input-light" value="<?php echo isset($_POST['reg_email']) ? esc_attr($_POST['reg_email']) : ''; ?>" required>
            </div>
            <div>
                <label for="reg_tel" class="sop-label sop-label-light"><?php esc_html_e( 'Teléfono', 'sistema-pro' ); ?></label>
                <input type="text" name="reg_tel" id="reg_tel" class="sop-input sop-input-light" value="<?php echo isset($_POST['reg_tel']) ? esc_attr($_POST['reg_tel']) : ''; ?>" required>
            </div>
        </div>

        <div class="sop-auth-row">
            <label for="reg_role" class="sop-label sop-label-light"><?php esc_html_e( '¿Cuál es tu rol?', 'sistema-pro' ); ?></label>
            <select name="reg_role" id="reg_role" class="sop-input sop-input-light" required>
                <option value="atleta" <?php selected(isset($_POST['reg_role']) ? $_POST['reg_role'] : '', 'atleta'); ?>><?php esc_html_e( 'Soy Atleta', 'sistema-pro' ); ?></option>
                <option value="entrenador" <?php selected(isset($_POST['reg_role']) ? $_POST['reg_role'] : '', 'entrenador'); ?>><?php esc_html_e( 'Soy Entrenador', 'sistema-pro' ); ?></option>
                <option value="especialista" <?php selected(isset($_POST['reg_role']) ? $_POST['reg_role'] : '', 'especialista'); ?>><?php esc_html_e( 'Soy Especialista', 'sistema-pro' ); ?></option>
            </select>
        </div>

        <div class="sop-auth-row-large">
            <label for="reg_pass" class="sop-label sop-label-light"><?php esc_html_e( 'Contraseña', 'sistema-pro' ); ?></label>
            <input type="password" name="reg_pass" id="reg_pass" class="sop-input sop-input-light" required>
        </div>

        <?php wp_nonce_field( 'sop_register_action', 'sop_reg_nonce' ); ?>
        
        <button type="submit" name="sop_register_submit" class="sop-btn-nav sop-btn-register sop-btn-full">
            <?php esc_html_e( 'Registrarme ahora', 'sistema-pro' ); ?>
        </button>
    </form>
</div>
