<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="sop-landing-hero">
    <h1>Bienvenido a la Élite Deportiva</h1>
    <p>Gestiona tu carrera, conecta con expertos y lleva tu rendimiento al siguiente nivel con IMPROVIA PRO.</p>
    
    <div class="sop-landing-actions">
        <?php if ( ! is_user_logged_in() ) : ?>
            <a href="<?php echo home_url('/registro'); ?>" class="sop-btn-nav sop-btn-register sop-btn-landing-primary">Comenzar Ahora</a>
            <a href="<?php echo home_url('/login'); ?>" class="sop-btn-nav sop-btn-login sop-btn-landing-secondary">Ya tengo cuenta</a>
        <?php else : 
            $user = wp_get_current_user();
            $name = !empty($user->display_name) ? $user->display_name : $user->user_login;
        ?>
            <div class="sop-landing-welcome-box">
                <p>Hola de nuevo, <strong><?php echo esc_html($name); ?></strong></p>
                <a href="<?php echo home_url('/suscripcion'); ?>" class="sop-btn-nav sop-btn-register sop-landing-welcome-btn">Entrar a Mi Plataforma</a>
            </div>
        <?php endif; ?>
    </div>
</div>
