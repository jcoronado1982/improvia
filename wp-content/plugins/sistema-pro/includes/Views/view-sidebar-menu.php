<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$is_provider = current_user_can( 'entrenador' ) || current_user_can( 'especialista' );
$user_status = get_user_meta( get_current_user_id(), 'sop_user_status', true );
$is_restricted = ( intval( $user_status ) === 1 );
?>
<nav class="sop-sidebar-menu">
    

    <?php foreach ( $items as $slug => $data ) : 
        // Si el usuario tiene estatus 1 (Inscrito), solo mostramos Perfil
        if ( $is_restricted && $slug !== 'perfil' ) continue;

        // Si el usuario es proveedor, ocultamos los directorios generales de la barra lateral.
        if ( $is_provider && ( $slug === 'entrenadores' || $slug === 'especialistas' ) ) continue;

        $url = home_url( '/' . $slug );
        $active_class = ( $current_slug === $slug ) ? 'active' : '';
        $dot_html = isset( $data['dot'] ) ? '<span class="sop-dot"></span>' : '';
    ?>
        <a href="<?php echo esc_url( $url ); ?>" class="sop-menu-item <?php echo esc_attr( $active_class ); ?>">
            <img src="<?php echo esc_url( SOP_URL . 'assets/images/' . $data['icon'] ); ?>" alt="" class="sop-menu-icon">
            <span><?php echo esc_html( $data['label'] ); ?></span>
            <?php echo $dot_html; ?>
        </a>
        
        <?php if ( $slug === 'mensajes' && $is_provider && ! $is_restricted ) : ?>
            <a href="<?php echo esc_url( home_url( '/solicitudes' ) ); ?>" class="sop-menu-item <?php echo ( $current_slug === 'solicitudes' ) ? 'active' : ''; ?>">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/users_2.png' ); ?>" alt="" class="sop-menu-icon">
                <span><?php esc_html_e( 'Solicitudes', 'sistema-pro' ); ?></span>
            </a>
            <a href="<?php echo esc_url( home_url( '/suscripciones' ) ); ?>" class="sop-menu-item <?php echo ( $current_slug === 'suscripciones' ) ? 'active' : ''; ?>">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/calendar.png' ); ?>" alt="" class="sop-menu-icon">
                <span><?php esc_html_e( 'Suscripciones', 'sistema-pro' ); ?></span>
            </a>
            <a href="<?php echo esc_url( home_url( '/improvia-pro' ) ); ?>" class="sop-menu-item <?php echo ( $current_slug === 'improvia-pro' ) ? 'active' : ''; ?>">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/star.png' ); ?>" alt="" class="sop-menu-icon">
                <span><?php esc_html_e( 'Improvia Pro', 'sistema-pro' ); ?></span>
            </a>
        <?php endif; ?>

    <?php endforeach; ?>

    <a href="<?php echo esc_url( wp_logout_url( home_url( '/home' ) ) ); ?>" class="sop-menu-item">
        <img src="<?php echo esc_url( SOP_URL . 'assets/images/log-out.png' ); ?>" alt="" class="sop-menu-icon">
        <span><?php esc_html_e( 'Salir', 'sistema-pro' ); ?></span>
    </a>
</nav>
