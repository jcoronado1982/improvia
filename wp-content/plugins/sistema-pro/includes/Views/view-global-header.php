<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$user = wp_get_current_user();
$display_name = 'INICIAR SESIÓN';

if ( is_user_logged_in() ) {
    $first_name = get_user_meta( $user->ID, 'first_name', true );
    $last_name = get_user_meta( $user->ID, 'last_name', true );
    
    if ( ! empty( $first_name ) ) {
        $display_name = strtoupper( $first_name . ( ! empty( $last_name ) ? ' ' . substr( $last_name, 0, 1 ) : '' ) );
    } else {
        $display_name = strtoupper( $user->display_name );
    }
}
?>
<header class="sop-global-header">
    <div class="sop-header-left">
        <div class="sop-header-logo">
            <a href="<?php echo home_url('/home'); ?>">
                <?php if ( is_page('login') || is_page('registro') ) : ?>
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/logo_blue.png' ); ?>" alt="IMPROVIA">
                <?php else : ?>
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/logo_white.png' ); ?>" alt="IMPROVIA">
                <?php endif; ?>
            </a>
        </div>
    </div>

    <div class="sop-header-right">
        <?php if ( ! is_page('login') && ! is_page('registro') ) : ?>
        <div class="sop-header-top">
            <div class="sop-lang-selector">
                <span class="sop-lang-text">ES</span>
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/flag_es.png' ); ?>" alt="Spanish" class="sop-lang-flag">
            </div>
        </div>
        <?php endif; ?>
        
        <div class="sop-header-bottom">
            <?php if ( ! is_page('login') && ! is_page('registro') ) : ?>
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
            <?php endif; ?>

            <div class="sop-header-user">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo home_url('/perfil'); ?>" class="sop-user-pill">
                        <?php echo esc_html( $display_name ); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo home_url('/login'); ?>" class="sop-user-pill">
                        INICIAR SESIÓN
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
