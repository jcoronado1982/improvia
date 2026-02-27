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
    <div class="sop-preview-subscription" style="background-color: #1a1e29; padding: 25px; border-radius: 16px;">
        
        <?php if ($consulta_gratis === 'yes') : ?>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 25px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px;">
            <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%; box-shadow: 0 0 4px #10b981;"></span>
            Primera consulta gratis
        </div>
        <?php endif; ?>

        <?php if ($has_paquetes) : ?>
            <h4 class="sop-preview-sub-title" style="margin-bottom: 15px; font-weight: 400; color:#fff; font-size: 18px;"><?php esc_html_e( 'Sesiones', 'sistema-pro' ); ?></h4>
            <div class="sop-preview-period-tabs" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 25px;">
                <?php foreach ($paquetes as $index => $pkg) : 
                    $label_desc = sprintf( _n( '%d Clase', '%d Clases', $pkg['qty'], 'sistema-pro' ), $pkg['qty'] );
                ?>
                    <button class="sop-period-tab <?php echo ($index === 0) ? 'active' : ''; ?>" data-price="<?php echo esc_attr($pkg['price']); ?>$" data-label="<?php echo esc_attr($label_desc); ?>" data-group="pricing-option" style="padding: 12px 10px; border: 1px solid #374151; background: transparent; border-radius: 8px; color: #d1d5db; transition: all 0.2s; font-size: 15px; cursor: pointer;">
                        <?php echo esc_html($pkg['qty']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($has_periodos) : ?>
            <h4 class="sop-preview-sub-title" style="margin-bottom: 15px; font-weight: 400; color:#fff; font-size: 18px;"><?php esc_html_e( 'Periodo', 'sistema-pro' ); ?></h4>
            <div class="sop-preview-period-tabs" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 25px;">
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
                    <button class="sop-period-tab <?php echo $active_class; ?>" data-price="<?php echo esc_attr($price); ?>$" data-label="<?php echo esc_attr($label_desc); ?>" data-group="pricing-option" style="padding: 12px 10px; border: 1px solid #374151; background: transparent; border-radius: 8px; color: #d1d5db; transition: all 0.2s; font-size: 15px; cursor: pointer;">
                        <?php echo esc_html($label); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="sop-preview-price-box" style="border: 1px solid #374151; border-radius: 12px; padding: 30px; text-align: center; margin-bottom: 25px;">
            <div class="sop-preview-price" id="sop-sidebar-price" style="font-size: 42px; font-weight: 300; color: #fff; line-height: 1;"><?php echo esc_html($default_price); ?>$</div>
        </div>

        <div class="sop-preview-slots" style="color: #9ca3af; font-size: 14px; margin-bottom: 15px; display: flex; gap: 10px; align-items: center;">
            <?php esc_html_e( 'Cupos', 'sistema-pro' ); ?> 
            <span style="background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 2px 8px; border-radius: 4px; font-size: 12px;">0/5</span>
        </div>
        <p class="sop-preview-sub-desc" style="color: #6b7280; font-size: 13px; line-height: 1.5; margin-bottom: 25px;">Lorem ipsum dolor sit amet consectetur. Pretium at libero fermentum in vulputate. Est hendrerit elit ante vivamus</p>
        <?php if ( ! $is_my_own_profile ) : ?>
        <button class="sop-preview-subscribe-btn" id="sop-subscribe-trigger" style="width: 100%; padding: 14px; background: linear-gradient(90deg, #669DED, #406AC4, #092189, #092189, #669DED, #669DED, #5889DD, #092189); color: #fff; border: none; border-radius: 30px; font-weight: 500; font-size: 14px; cursor: pointer; transition: opacity 0.2s; letter-spacing: 0.5px;"><?php esc_html_e( 'SUSCRIBIRSE', 'sistema-pro' ); ?></button>
        <?php endif; ?>
    </div>
</div>

<style>
.sop-period-tab.active {
    background: #fff !important;
    color: #000 !important;
    border-color: #fff !important;
    font-weight: 500;
}
.sop-preview-subscribe-btn:hover {
    opacity: 0.9;
}
</style>

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
