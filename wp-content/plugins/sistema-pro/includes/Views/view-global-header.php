<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$user = wp_get_current_user();
$display_name = 'LOGIN';

if ( is_user_logged_in() ) {
    // Tratamos de obtener el nombre completo desde el display_name o los meta datos directamente
    $full_name = trim( $user->display_name );
    if ( empty( $full_name ) ) {
        $first_name = get_user_meta( $user->ID, 'first_name', true );
        $last_name = get_user_meta( $user->ID, 'last_name', true );
        $full_name = trim( $first_name . ' ' . $last_name );
    }

    if ( ! empty( $full_name ) ) {
        // Dividimos el nombre completo por espacios, limpiando espacios adicionales
        $parts = array_values( array_filter( explode( ' ', $full_name ) ) );
        
        $first_word = array_shift( $parts );
        // Si hay un segundo elemento (apellido/segundo nombre), tomamos la inicial sin el punto
        $initial = count( $parts ) > 0 ? mb_substr( trim($parts[0]), 0, 1 ) : '';
        
        $display_name = mb_strtoupper( $first_word . ( $initial ? ' ' . $initial : '' ) );
    } else {
        $display_name = mb_strtoupper( $user->user_login );
    }
}
?>
<header class="sop-global-header">
    <div class="header-content">
        <div class="sop-header-left">
            <div class="sop-header-logo">
                <a href="<?php echo home_url('/home'); ?>">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/logo_blue.png' ); ?>" alt="IMPROVIA" class="sop-logo-blue">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/logo_white.png' ); ?>" alt="IMPROVIA" class="sop-logo-white">
                </a>
            </div>
        </div>

        <div class="sop-header-right">
            <div class="sop-header-top">
                <div class="sop-lang-selector">
                    <span class="sop-lang-text">ES</span>
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/flag_es.png' ); ?>" alt="Spanish" class="sop-lang-flag">
                </div>
            </div>
            
            <div class="sop-header-bottom">
                <nav class="sop-header-nav">
                    <a href="#" class="sop-nav-item">ATHLETES</a>
                    <div class="sop-nav-item has-dropdown">
                        SPORTS SPECIALISTS 
                        <svg class="sop-dropdown-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                    <a href="#" class="sop-nav-item">COACHES</a>
                    <div class="sop-nav-item has-dropdown">
                        GET TO KNOW US 
                        <svg class="sop-dropdown-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </nav>

                <div class="sop-header-user">
                    <?php if ( is_user_logged_in() ) : ?>
                        <a href="<?php echo home_url('/perfil'); ?>" class="sop-user-pill">
                            <?php echo esc_html( $display_name ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo home_url('/login'); ?>" class="sop-user-pill sop-pill-login">
                            LOGIN
                        </a>
                        <a href="<?php echo home_url('/registro'); ?>" class="sop-user-pill sop-pill-register">
                            REGÍSTRATE
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>
