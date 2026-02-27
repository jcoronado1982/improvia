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

// Fetch the 6 Session Packages Data
$paquetes = array();
for ($i = 1; $i <= 6; $i++) {
    $qty = get_user_meta( $user_id, 'sop_cantidad_sesiones_' . $i, true );
    $price = get_user_meta( $user_id, 'sop_precio_sesiones_' . $i, true );
    
    if (!empty($qty) && !empty($price) && floatval($price) > 0 && intval($qty) > 0) {
        $paquetes[] = array('qty' => $qty, 'price' => $price);
    }
}

// Fallback if they haven't configured any valid session packages yet
if (empty($paquetes)) {
    $paquetes[] = array('qty' => 0, 'price' => 0); // Show at least one empty box as generic
}

$link_perfil = site_url( '/entrenador-detalle/?trainer_id=' . $user_id );

// Helper to gracefully show missing prices
function sop_get_display_price($val) {
    return !empty($val) && floatval($val) > 0 ? floatval($val) . '$' : 'N/A';
}
?>

<div class="sop-tc-pricing-card" style="padding: 25px; background-color: #0b1120; border-radius: 16px; border: 1px solid #1a233a; display: flex; flex-direction: column; justify-content: center; min-width: 250px;">
    
    <!-- 4-Box Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 30px;">
        
        <?php foreach ($paquetes as $pkg): 
            $qty_display = (!empty($pkg['qty']) && intval($pkg['qty']) > 0) ? intval($pkg['qty']) . ' sesiones' : '- sesiones';
        ?>
        <div style="background-color: #071533; border: 1px solid #1d4ed8; border-radius: 8px; text-align: center; padding: 25px 10px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            <span style="font-size: 28px; color: #fff; font-weight: 300;"><?php echo sop_get_display_price($pkg['price']); ?></span>
            <span style="font-size: 12px; color: #888;"><?php echo esc_html($qty_display); ?></span>
        </div>
        <?php endforeach; ?>
        
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; flex-direction: column; gap: 15px;">
        <button type="button" class="sop-btn-fav" style="width: 100%; border: 1px solid #d1d5db; background: transparent; color: #d1d5db; border-radius: 30px; padding: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; transition: all 0.2s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
            Añadir a favoritos
        </button>
        
        <a href="<?php echo esc_url($link_perfil); ?>" style="width: 100%; border: none; background: #092189; color: #fff; border-radius: 30px; padding: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; text-decoration: none; font-weight: 500; transition: background 0.2s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Ver perfil
        </a>
    </div>

</div>
