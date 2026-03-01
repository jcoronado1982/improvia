<?php
/**
 * Componente Reutilizable: Tarjeta de Suscripción (Sidebar)
 * Se muestra en el perfil público del entrenador y en la previsualización.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = isset($current_user) ? $current_user->ID : get_current_user_id();
$is_my_own_profile = ( get_current_user_id() > 0 && get_current_user_id() === intval($user_id) );

// Fetch 6 Session Packages
$paquetes = array();
for ($i = 1; $i <= 6; $i++) {
    $qty = get_user_meta( $user_id, 'sop_cantidad_sesiones_' . $i, true );
    $price = get_user_meta( $user_id, 'sop_precio_sesiones_' . $i, true );
    if (!empty($qty) && !empty($price) && floatval($price) > 0 && intval($qty) > 0) {
        $paquetes[] = array('qty' => $qty, 'price' => $price);
    }
}

// Fetch Periods
$periodos_raw = array(
    'semanal' => get_user_meta( $user_id, 'sop_precio_semanal', true ),
    'mensual' => get_user_meta( $user_id, 'sop_precio_mensual', true ),
    'trimestral' => get_user_meta( $user_id, 'sop_precio_trimestral', true ),
    'anual' => get_user_meta( $user_id, 'sop_precio_anual', true ),
);

$active_periodos = array();
foreach ($periodos_raw as $key => $val) {
    if (!empty($val) && floatval($val) > 0) {
        $active_periodos[$key] = floatval($val);
    }
}

$has_paquetes = !empty($paquetes);
$has_periodos = !empty($active_periodos);

// Determine default price
$default_price = 0;
if ($has_paquetes) {
    $default_price = $paquetes[0]['price'];
} elseif ($has_periodos) {
    $default_price = reset($active_periodos);
}

// Fetch "Primera consulta gratis" setting
$consulta_gratis = get_user_meta( $user_id, 'sop_consulta_gratis', true );
?>
<div class="sop-preview-sidebar">
    <div class="sop-preview-subscription">
        
        <?php if ($consulta_gratis === 'yes') : ?>
        <div class="sop-preview-consultation-gratis">
            <span class="sop-dot-green"></span>
            <?php esc_html_e( 'Primera consulta gratis', 'sistema-pro' ); ?>
        </div>
        <?php endif; ?>

        <?php if ($has_paquetes) : ?>
            <h4 class="sop-preview-sub-title"><?php esc_html_e( 'Sesiones', 'sistema-pro' ); ?></h4>
            <div class="sop-preview-period-tabs sop-grid-4">
                <?php foreach ($paquetes as $index => $pkg) : 
                    $label_desc = sprintf( _n( '%d Clase', '%d Clases', $pkg['qty'], 'sistema-pro' ), $pkg['qty'] );
                ?>
                    <button class="sop-period-tab <?php echo ($index === 0) ? 'active' : ''; ?>" data-price="<?php echo esc_attr($pkg['price']); ?>$" data-label="<?php echo esc_attr($label_desc); ?>" data-group="pricing-option">
                        <?php echo esc_html($pkg['qty']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($has_periodos) : ?>
            <h4 class="sop-preview-sub-title"><?php esc_html_e( 'Periodo', 'sistema-pro' ); ?></h4>
            <div class="sop-preview-period-tabs">
                <?php 
                $first_period = !$has_paquetes;
                foreach ($active_periodos as $key => $price) : 
                    $label = ucfirst($key);
                    $label_desc = '';
                    if ($key === 'semanal') $label_desc = __('1 Semana', 'sistema-pro');
                    elseif ($key === 'mensual') $label_desc = __('1 Mes', 'sistema-pro');
                    elseif ($key === 'trimestral') $label_desc = __('3 Meses', 'sistema-pro');
                    elseif ($key === 'anual') $label_desc = __('1 Año', 'sistema-pro');
                    else $label_desc = sprintf( esc_html__( 'Plan %s', 'sistema-pro' ), $label );
                    
                    $active_class = $first_period ? 'active' : '';
                    $first_period = false;
                ?>
                    <button class="sop-period-tab <?php echo $active_class; ?>" data-price="<?php echo esc_attr($price); ?>$" data-label="<?php echo esc_attr($label_desc); ?>" data-group="pricing-option">
                        <?php echo esc_html($label); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="sop-preview-price-box">
            <div class="sop-preview-price" id="sop-sidebar-price"><?php echo esc_html($default_price); ?>$</div>
        </div>

        <div class="sop-preview-slots">
            <?php esc_html_e( 'Cupos', 'sistema-pro' ); ?> 
            <span>0/5</span>
        </div>
        <p class="sop-preview-sub-desc"><?php esc_html_e( 'Lorem ipsum dolor sit amet consectetur. Pretium at libero fermentum in vulputate. Est hendrerit elit ante vivamus', 'sistema-pro' ); ?></p>
        <?php if ( ! $is_my_own_profile ) : ?>
        <button class="sop-preview-subscribe-btn" id="sop-subscribe-trigger"><?php esc_html_e( 'SUSCRIBIRSE', 'sistema-pro' ); ?></button>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.sop-period-tab[data-group="pricing-option"]');
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

    const subscribeBtn = document.getElementById('sop-subscribe-trigger');
    if (subscribeBtn) {
        subscribeBtn.addEventListener('click', function() {
            const activeTab = document.querySelector('.sop-period-tab.active');
            const price = activeTab ? activeTab.dataset.price.replace('$', '') : '<?php echo $default_price; ?>';
            const planName = activeTab ? activeTab.dataset.label : 'Plan';

            <?php 
            $trainer_obj = get_userdata($user_id);
            $trainer_name_final = $trainer_obj ? $trainer_obj->display_name : __('Entrenador', 'sistema-pro');
            ?>
            const trainerId = '<?php echo $user_id; ?>';
            const checkoutUrl = '<?php echo esc_url( home_url( '/checkout-simulado' ) ); ?>';

            subscribeBtn.disabled = true;
            subscribeBtn.style.opacity = '0.5';

            fetch(sop_ajax.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'sop_prepare_checkout',
                    nonce: sop_ajax.nonce,
                    trainer_id: trainerId,
                    amount: price,
                    plan: planName
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = checkoutUrl;
                } else {
                    alert('Error al preparar el checkout: ' + data.data);
                    subscribeBtn.disabled = false;
                    subscribeBtn.style.opacity = '1';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                subscribeBtn.disabled = false;
                subscribeBtn.style.opacity = '1';
            });
        });
    }
});
</script>
