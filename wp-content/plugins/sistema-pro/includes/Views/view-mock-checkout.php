<?php
/**
 * View: Mock Checkout
 * Loads data from user_meta 'sop_pending_checkout_data' saved via AJAX.
 */
$user_id = get_current_user_id();
$checkout_data = get_user_meta( $user_id, 'sop_pending_checkout_data', true );

$trainer_id = 0;
$trainer_name = __( 'Entrenador Seleccionado', 'sistema-pro' );
$amount = '0.00';
$plan_name = __( 'Suscripción', 'sistema-pro' );

if ( ! empty( $checkout_data ) ) {
    $trainer_id = intval( $checkout_data['trainer_id'] );
    $amount     = floatval( $checkout_data['amount'] );
    $plan_name  = sanitize_text_field( $checkout_data['plan_name'] );

    $trainer_user = get_userdata( $trainer_id );
    if ( $trainer_user ) {
        $trainer_name = $trainer_user->display_name;
    }
}
?>
<div class="sop-container sop-mock-checkout">
    <div class="sop-mock-checkout-card">
        <h2><?php esc_html_e( 'Simulación de Checkout', 'sistema-pro' ); ?></h2>
        <p><?php esc_html_e( 'Estás a punto de suscribirte al entrenador', 'sistema-pro' ); ?> <strong><span id="sop-mock-trainer-name"><?php echo esc_html($trainer_name); ?></span></strong>.</p>
        <div class="sop-mock-plan-details">
            <p><strong><?php esc_html_e( 'Plan:', 'sistema-pro' ); ?></strong> <span id="sop-mock-plan-name"><?php echo esc_html($plan_name); ?></span></p>
            <p><strong><?php esc_html_e( 'Precio:', 'sistema-pro' ); ?></strong> <span id="sop-mock-amount-display"><?php echo esc_html($amount); ?></span>€ / <?php esc_html_e( 'mes', 'sistema-pro' ); ?></p>
        </div>
        
        <div class="sop-mock-info" style="background: rgba(59, 130, 246, 0.1); border: 1px solid #3b82f6; color: #3b82f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9em; text-align: left;">
            <p><strong>🔒 <?php esc_html_e( 'Fondos en Garantía:', 'sistema-pro' ); ?></strong> <?php esc_html_e( 'Tu dinero estará reservado de forma segura. El entrenador tiene 48h para aceptar tu solicitud. No se realizará ningún cargo real hasta que el entrenador apruebe la suscripción.', 'sistema-pro' ); ?></p>
        </div>

        <button id="sop-mock-pay-btn" class="sop-btn sop-btn-primary"><?php esc_html_e( 'Reservar FONDOS (Authorize)', 'sistema-pro' ); ?></button>
        <button id="sop-mock-cancel-btn" class="sop-btn sop-btn-secondary"><?php esc_html_e( 'Cancelar', 'sistema-pro' ); ?></button>
        
        <div id="sop-mock-loading" style="display:none; text-align: center; margin-top: 15px;">
            <p><?php esc_html_e( 'Reservando fondos en garantía...', 'sistema-pro' ); ?></p>
        </div>
    </div>
</div>

<style>
.sop-mock-checkout {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    background-color: var(--sop-bg, #1a1a1a);
    color: white;
}
.sop-mock-checkout-card {
    background-color: var(--sop-surface, #2a2a2a);
    padding: 30px;
    border-radius: 12px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    text-align: center;
}
.sop-mock-plan-details {
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
    text-align: left;
}
.sop-mock-plan-details p {
    margin: 5px 0;
}
.sop-mock-warning {
    color: #ff9800;
    background: rgba(255, 152, 0, 0.1);
    padding: 10px;
    border-radius: 6px;
    font-size: 0.9em;
    margin-bottom: 20px;
}
.sop-mock-checkout-card .sop-btn {
    width: 100%;
    margin-bottom: 10px;
    padding: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trainerId = '<?php echo $trainer_id; ?>';
    const amount = '<?php echo $amount; ?>';
    const planName = '<?php echo esc_js($plan_name); ?>';
    
    document.getElementById('sop-mock-cancel-btn').addEventListener('click', function() {
        window.history.back();
    });

    document.getElementById('sop-mock-pay-btn').addEventListener('click', function() {
        if(!trainerId || trainerId == '0') {
            alert("<?php esc_html_e( 'Error: Faltan datos del entrenador.', 'sistema-pro' ); ?>");
            return;
        }

        document.getElementById('sop-mock-pay-btn').style.display = 'none';
        document.getElementById('sop-mock-cancel-btn').style.display = 'none';
        document.getElementById('sop-mock-loading').style.display = 'block';

        // Petición AJAX al backend para simular el registro de la suscripción
        fetch(sop_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'sop_simulate_subscription',
                nonce: sop_ajax.nonce,
                trainer_id: trainerId,
                amount: amount,
                plan: planName
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert("<?php esc_html_e( '¡Reserva de fondos exitosa! Tu solicitud ha sido enviada al entrenador.', 'sistema-pro' ); ?>");
                window.location.href = '<?php echo esc_url( home_url('/mensajes') ); ?>';
            } else {
                alert("<?php esc_html_e( 'Error en la simulación:', 'sistema-pro' ); ?> " + (data.data || "<?php esc_html_e( 'Desconocido', 'sistema-pro' ); ?>"));
                document.getElementById('sop-mock-pay-btn').style.display = 'block';
                document.getElementById('sop-mock-cancel-btn').style.display = 'block';
                document.getElementById('sop-mock-loading').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("<?php esc_html_e( 'Error de conexión al simular el pago.', 'sistema-pro' ); ?>");
        });
    });
});
</script>
