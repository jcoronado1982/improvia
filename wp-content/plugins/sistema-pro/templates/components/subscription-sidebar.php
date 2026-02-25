<?php
/**
 * Componente Reutilizable: Tarjeta de Suscripción (Sidebar)
 * Se muestra en el perfil público del entrenador y en la previsualización.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="sop-preview-sidebar">
    <div class="sop-preview-subscription">
        <h4 class="sop-preview-sub-title"><?php esc_html_e( 'Periodo', 'sistema-pro' ); ?></h4>
        <div class="sop-preview-period-tabs">
            <button class="sop-period-tab"><?php esc_html_e( 'Semanal', 'sistema-pro' ); ?></button>
            <button class="sop-period-tab active"><?php esc_html_e( 'Mensual', 'sistema-pro' ); ?></button>
            <button class="sop-period-tab"><?php esc_html_e( 'Trimestral', 'sistema-pro' ); ?></button>
            <button class="sop-period-tab"><?php esc_html_e( 'Anual', 'sistema-pro' ); ?></button>
        </div>
        <div class="sop-preview-price">160$</div>
        <div class="sop-preview-slots"><?php esc_html_e( 'Cupos', 'sistema-pro' ); ?> <span>0/5</span></div>
        <p class="sop-preview-sub-desc">Lorem ipsum dolor sit amet consectetur. Pretium at libero fermentum in vulputate. Est hendrerit elit ante vivamus</p>
        <button class="sop-preview-subscribe-btn"><?php esc_html_e( 'SUSCRIBIRSE', 'sistema-pro' ); ?></button>
    </div>
</div>
