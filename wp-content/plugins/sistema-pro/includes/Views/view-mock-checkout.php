<div class="sop-container sop-mock-checkout">
    <div class="sop-mock-checkout-card">
        <h2>Simulación de Checkout</h2>
        <p>Estás a punto de suscribirte al entrenador <strong><span id="sop-mock-trainer-name"></span></strong>.</p>
        <div class="sop-mock-plan-details">
            <p><strong>Plan:</strong> Premium Mensual</p>
            <p><strong>Precio:</strong> 50€ / mes</p>
        </div>
        
        <div class="sop-mock-warning">
            <p><em>⚠️ Esta es una pasarela de prueba para la presentación. No se realizarán cargos reales a ninguna tarjeta.</em></p>
        </div>

        <button id="sop-mock-pay-btn" class="sop-btn sop-btn-primary">Simular Pago Exitoso (50€)</button>
        <button id="sop-mock-cancel-btn" class="sop-btn sop-btn-secondary">Cancelar</button>
        
        <div id="sop-mock-loading" style="display:none; text-align: center; margin-top: 15px;">
            <p>Procesando pago simulado...</p>
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
    // Leer parámetros de URL (ej. ?trainer_id=123)
    const urlParams = new URLSearchParams(window.location.search);
    const trainerId = urlParams.get('trainer_id');
    const trainerName = urlParams.get('tname') || 'Entrenador Seleccionado';
    
    document.getElementById('sop-mock-trainer-name').innerText = trainerName;

    document.getElementById('sop-mock-cancel-btn').addEventListener('click', function() {
        window.history.back();
    });

    document.getElementById('sop-mock-pay-btn').addEventListener('click', function() {
        if(!trainerId) {
            alert("Error: Faltan datos del entrenador.");
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
                amount: 50
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert("¡Pago Simulado Exitoso! Ya tienes acceso al entrenador.");
                // Redirigir al dashboard/mensajes
                window.location.href = '<?php echo esc_url( home_url('/mensajes') ); ?>';
            } else {
                alert("Error en la simulación: " + (data.data || "Desconocido"));
                document.getElementById('sop-mock-pay-btn').style.display = 'block';
                document.getElementById('sop-mock-cancel-btn').style.display = 'block';
                document.getElementById('sop-mock-loading').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error de conexión al simular el pago.");
        });
    });
});
</script>
