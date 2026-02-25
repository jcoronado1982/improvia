<?php
/**
 * Componente Reutilizable: Tarjeta de Suscripción (Sidebar)
 * Se muestra en el perfil público del entrenador y en la previsualización.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php
$p_mensual    = get_user_meta( $current_user->ID, 'sop_precio_mensual', true );
$p_semanal    = get_user_meta( $current_user->ID, 'sop_precio_semanal', true );
$p_trimestral = get_user_meta( $current_user->ID, 'sop_precio_trimestral', true );
$p_anual      = get_user_meta( $current_user->ID, 'sop_precio_anual', true );

$p_sesiones = get_user_meta( $current_user->ID, 'sop_precio_sesiones', true );
$c_sesiones = get_user_meta( $current_user->ID, 'sop_cantidad_sesiones', true );
$is_sesiones = (!empty($p_sesiones) && floatval($p_sesiones) > 0) && (!empty($c_sesiones) && intval($c_sesiones) > 0);

$precio_mensual    = !empty($p_mensual) ? floatval($p_mensual) : 50;
$precio_semanal    = !empty($p_semanal) ? floatval($p_semanal) : 0;
$precio_trimestral = !empty($p_trimestral) ? floatval($p_trimestral) : 0;
$precio_anual      = !empty($p_anual) ? floatval($p_anual) : 0;
?>
<div class="sop-preview-sidebar">
    <div class="sop-preview-subscription">
        <h4 class="sop-preview-sub-title"><?php esc_html_e( 'Periodo', 'sistema-pro' ); ?></h4>
        <div class="sop-preview-period-tabs" id="sop-pricing-tabs">
            <?php if ( $is_sesiones ) : ?>
            <button class="sop-period-tab" data-price="<?php echo esc_attr(floatval($p_sesiones)); ?>$"><?php echo esc_html(intval($c_sesiones)); ?> <?php esc_html_e( 'Sesiones', 'sistema-pro' ); ?></button>
            <?php endif; ?>
            <button class="sop-period-tab" data-price="<?php echo esc_attr($precio_semanal); ?>$"><?php esc_html_e( 'Semanal', 'sistema-pro' ); ?></button>
            <button class="sop-period-tab active" data-price="<?php echo esc_attr($precio_mensual); ?>$"><?php esc_html_e( 'Mensual', 'sistema-pro' ); ?></button>
            <button class="sop-period-tab" data-price="<?php echo esc_attr($precio_trimestral); ?>$"><?php esc_html_e( 'Trimestral', 'sistema-pro' ); ?></button>
            <button class="sop-period-tab" data-price="<?php echo esc_attr($precio_anual); ?>$"><?php esc_html_e( 'Anual', 'sistema-pro' ); ?></button>
        </div>
        <div class="sop-preview-price" id="sop-sidebar-price"><?php echo esc_html($precio_mensual); ?>$</div>
        <div class="sop-preview-slots"><?php esc_html_e( 'Cupos', 'sistema-pro' ); ?> <span>0/5</span></div>
        <p class="sop-preview-sub-desc">Lorem ipsum dolor sit amet consectetur. Pretium at libero fermentum in vulputate. Est hendrerit elit ante vivamus</p>
        <button class="sop-preview-subscribe-btn" onclick="window.location.href='<?php echo esc_url( home_url( '/checkout-simulado?trainer_id=' . $current_user->ID . '&tname=' . urlencode($current_user->display_name) ) ); ?>'"><?php esc_html_e( 'SUSCRIBIRSE', 'sistema-pro' ); ?></button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#sop-pricing-tabs .sop-period-tab');
    const priceDisplay = document.getElementById('sop-sidebar-price');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            if(this.dataset.price) {
                priceDisplay.textContent = this.dataset.price;
            }
        });
    });
});
</script>
