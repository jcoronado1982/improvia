<?php
/**
 * Component: Pricing / Subscription Card (4-Box Layout)
 * Displays the 4 distinct session packages configured by the trainer.
 * Matches the Figma design for the directory view.
 * 
 * Expected vars: 
 * - $trainer (WP_User object)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! isset( $trainer ) || ! ( $trainer instanceof WP_User ) ) {
    return;
}

$user_id = $trainer->ID;


// Fetch the 4 Fixed Periods Data
$periodos = array(
    array('id' => 'semanal',   'label' => 'Semanal',    'default' => '80'),
    array('id' => 'mensual',   'label' => 'Mensual',    'default' => '160'),
    array('id' => 'trimestral', 'label' => 'Trimestral', 'default' => '540'),
    array('id' => 'anual',     'label' => 'Anual',      'default' => '1.100'),
);

$display_items = array();
foreach ($periodos as $p) {
    $price = get_user_meta( $user_id, 'sop_precio_' . $p['id'], true );
    if (empty($price) || floatval($price) <= 0) {
        $price = $p['default'];
    } else {
        $price = number_format(floatval($price), 0, ',', '.');
    }
    $display_items[] = array('label' => $p['label'], 'price' => $price);
}

$link_perfil = site_url( '/entrenador-detalle/?trainer_id=' . $user_id );
?>

<div class="sop-tc-pricing-card">
    
    <!-- 4-Box Grid -->
    <div class="sop-tc-pricing-grid">
        
        <?php foreach ($display_items as $item): ?>
        <div class="sop-tc-pricing-item">
            <div class="sop-tc-pricing-box">
                <span class="sop-tc-price-amount"><?php echo esc_html($item['price']); ?>$</span>
            </div>
            <span class="sop-tc-price-period"><?php echo esc_html($item['label']); ?></span>
        </div>
        <?php endforeach; ?>
        
    </div>

    <!-- Action Buttons -->
    <div class="sop-tc-pricing-actions">
        <button type="button" class="sop-tc-action-btn sop-tc-action-fav">
            <img src="<?php echo esc_url( SOP_URL . 'assets/images/1.png' ); ?>" alt="Fav" class="sop-tc-btn-icon">
            Añadir a favoritos
        </button>
        
        <a href="<?php echo esc_url($link_perfil); ?>" class="sop-tc-action-btn sop-tc-action-ver">
            <img src="<?php echo esc_url( SOP_URL . 'assets/images/user_white.png' ); ?>" alt="User" class="sop-tc-btn-icon">
            Ver perfil
        </a>
    </div>

</div>
