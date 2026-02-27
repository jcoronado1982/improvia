<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$is_trainer = current_user_can('entrenador') || current_user_can('especialista');

// Obtener logs reales
$all_logs = get_option( 'sop_mock_transactions_log', array() );
$my_requests = array();

foreach ( $all_logs as $log ) {
    if ( $is_trainer && intval($log['trainer_id']) === $user_id ) {
        // El entrenador ve solicitudes de atletas
        $athlete = get_userdata( $log['athlete_id'] );
        $plan_display = isset($log['plan_name']) ? $log['plan_name'] : 'Premium Mensual';
        $my_requests[] = array(
            'txn_id'   => $log['id'],
            'name'     => $athlete ? $athlete->display_name : 'Usuario #' . $log['athlete_id'],
            'image'    => get_avatar_url( $log['athlete_id'] ),
            'price'    => '$' . number_format($log['total_amount'], 2) . ' / ' . strtoupper($plan_display),
            'status'   => $log['status'] === 'FONDOS_RESERVADOS_SIMULADOS' ? 'pendiente' : ($log['status'] === 'SUSCRIPCION_ACTIVA' ? 'aceptada' : 'rechazada'),
            'raw_status' => $log['status'],
            'desc'     => sprintf( esc_html__( 'Solicitud de %s enviada el %s', 'sistema-pro' ), $plan_display, $log['date'] ),
            'rating'   => 5,
            'reviews'  => 0,
            'club'     => __( 'Atleta', 'sistema-pro' )
        );
    } elseif ( !$is_trainer && intval($log['athlete_id']) === $user_id ) {
        // El atleta ve sus propias solicitudes a entrenadores
        $trainer = get_userdata( $log['trainer_id'] );
        $my_requests[] = array(
            'txn_id'   => $log['id'],
            'name'     => $trainer ? $trainer->display_name : 'Entrenador #' . $log['trainer_id'],
            'image'    => get_avatar_url( $log['trainer_id'] ),
            'price'    => '$' . number_format($log['total_amount'], 2),
            'status'   => $log['status'] === 'FONDOS_RESERVADOS_SIMULADOS' ? 'pendiente' : ($log['status'] === 'SUSCRIPCION_ACTIVA' ? 'aceptada' : 'rechazada'),
            'raw_status' => $log['status'],
            'desc'     => __( 'Tu solicitud al entrenador está siendo procesada.', 'sistema-pro' ),
            'rating'   => 5,
            'reviews'  => 0,
            'club'     => __( 'Coach', 'sistema-pro' )
        );
    }
}

// Invertir para ver lo más reciente primero
$my_requests = array_reverse($my_requests);
?>

<div class="sop-solicitudes-container">

    <!-- Filtros Superiores -->
    <div class="sop-sol-filters">
        <button class="sop-sol-filter-btn active" data-filter="all"><?php esc_html_e( 'Todas', 'sistema-pro' ); ?></button>
        <button class="sop-sol-filter-btn outline" data-filter="pendiente"><?php esc_html_e( 'Pendientes', 'sistema-pro' ); ?></button>
        <button class="sop-sol-filter-btn outline" data-filter="aceptada"><?php esc_html_e( 'Aceptadas', 'sistema-pro' ); ?></button>
        <button class="sop-sol-filter-btn outline" data-filter="rechazada"><?php esc_html_e( 'Rechazadas', 'sistema-pro' ); ?></button>
    </div>

    <!-- Lista de Solicitudes -->
    <div class="sop-sol-list">
        <?php if (empty($my_requests)): ?>
            <div style="text-align: center; padding: 40px; color: #6b7280; background: #fff; border-radius: 16px;">
                <p>No hay solicitudes registradas.</p>
            </div>
        <?php endif; ?>

        <?php foreach ( $my_requests as $req ) : ?>
        <div class="sop-sol-card" data-status="<?php echo esc_attr($req['status']); ?>">
            
            <div class="sop-sol-image-wrapper">
                <img src="<?php echo esc_url($req['image']); ?>" alt="<?php echo esc_attr($req['name']); ?>">
            </div>
            
            <div class="sop-sol-content">
                <div class="sop-sol-header-row">
                    <div class="sop-sol-header-left">
                        <div class="sop-sol-rating">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <?php if($i <= $req['rating']): ?>
                                    <span class="dashicons dashicons-star-filled"></span>
                                <?php else: ?>
                                    <span class="dashicons dashicons-star-empty"></span>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span class="sop-sol-reviews">(<?php echo $req['reviews']; ?>)</span>
                        </div>
                        <h2 class="sop-sol-name"><?php echo esc_html($req['name']); ?></h2>
                        <div class="sop-sol-club-row">
                            <span class="sop-sol-club-label"><?php echo $is_trainer ? 'Tipo de Usuario' : 'Rol'; ?></span>
                            <span class="sop-sol-club-box"><?php echo esc_html($req['club']); ?></span>
                        </div>
                    </div>
                    <div class="sop-sol-header-right">
                        <div class="sop-sol-price"><?php echo esc_html($req['price']); ?></div>
                        <div class="sop-sol-status-badge <?php echo esc_attr($req['status']); ?>" style="font-size: 10px; margin-top: 5px; text-transform: uppercase; font-weight: bold; text-align: right;">
                            <?php 
                            if ($req['raw_status'] === 'FONDOS_RESERVADOS_SIMULADOS') {
                                esc_html_e( 'Fondos Reservados', 'sistema-pro' );
                            } elseif ($req['raw_status'] === 'SUSCRIPCION_ACTIVA') {
                                esc_html_e( 'Activa', 'sistema-pro' );
                            } else {
                                esc_html_e( 'Rechazada', 'sistema-pro' );
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="sop-sol-desc">
                    <?php echo esc_html($req['desc']); ?>
                </div>

                <div class="sop-sol-actions">
                    <?php if ( $is_trainer && $req['raw_status'] === 'FONDOS_RESERVADOS_SIMULADOS' ) : ?>
                        <button class="sop-btn-blue sop-sol-approve" data-txn="<?php echo esc_attr($req['txn_id']); ?>"><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'ACEPTAR', 'sistema-pro' ); ?></button>
                        <button class="sop-btn-white sop-sol-reject" data-txn="<?php echo esc_attr($req['txn_id']); ?>"><span class="dashicons dashicons-no"></span> <?php esc_html_e( 'RECHAZAR', 'sistema-pro' ); ?></button>
                    <?php endif; ?>
                    <button class="sop-btn-white"><span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'REVISAR', 'sistema-pro' ); ?></button>
                </div>
            </div>
            
        </div>
        <?php endforeach; ?>
    </div>

</div>

<style>
/* ============ SOLICITUDES ============ */
.sop-solicitudes-container {
    font-family: var(--sop-font-main);
    color: #1a1e29;
}

/* FILTROS */
.sop-sol-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
}

.sop-sol-filter-btn {
    padding: 6px 18px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.sop-sol-filter-btn.active {
    background: #092189;
    color: #ffffff;
    border: 1.5px solid #092189;
}

.sop-sol-filter-btn.outline {
    background: transparent;
    color: #092189;
    border: 1px solid #092189;
}

/* TARJETAS */
.sop-sol-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.sop-sol-card {
    display: flex;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.sop-sol-image-wrapper {
    flex: 0 0 240px;
    max-width: 240px;
}
.sop-sol-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.sop-sol-content {
    flex: 1;
    padding: 24px;
    display: flex;
    flex-direction: column;
}

.sop-sol-header-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.sop-sol-rating {
    display: flex;
    align-items: center;
    color: #092189; /* Azul Improvia */
    margin-bottom: 6px;
}
.sop-sol-rating .dashicons {
    font-size: 14px;
    width: 14px; height: 14px;
}
.sop-sol-reviews {
    color: #6b7280;
    font-size: 12px;
    margin-left: 6px;
}

.sop-sol-name {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 700;
    color: #092189; /* Azul oscuro Improvia */
    text-transform: uppercase;
}

.sop-sol-club-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #4b5563;
}
.sop-sol-club-box {
    border: 1px solid #d1d5db;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.sop-sol-price {
    font-size: 18px;
    font-weight: 700;
    color: #092189;
}

.sop-sol-desc {
    font-size: 13px;
    color: #9ca3af;
    line-height: 1.5;
    margin-bottom: 25px;
    flex-grow: 1;
}

/* BOTONES DE ACCION */
.sop-sol-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.sop-sol-actions button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    text-transform: uppercase;
}
.sop-sol-actions button .dashicons {
    font-size: 14px; width: 14px; height: 14px; margin-top:2px;
}

.sop-btn-blue {
    background: #4263eb; /* Gradiente o color exacto: asumo azul botón de Improvia */
    background: linear-gradient(90deg, #2b4cba 0%, #4263eb 100%);
    color: #fff;
    border: none;
}
.sop-btn-white {
    background: transparent;
    color: #092189;
    border: 1px solid #092189;
}

.sop-sol-status-badge.pendiente { color: #f59e0b; }
.sop-sol-status-badge.aceptada { color: #10b981; }
.sop-sol-status-badge.rechazada { color: #ef4444; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var filterBtns = document.querySelectorAll('.sop-sol-filter-btn');
    var cards = document.querySelectorAll('.sop-sol-card');

    // Filter logic function
    function applyFilter(filter) {
        // Update buttons UI
        filterBtns.forEach(btn => {
            if (btn.getAttribute('data-filter') === filter) {
                btn.classList.remove('outline');
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
                btn.classList.add('outline');
            }
        });

        // Toggle cards
        cards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-status') === filter) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        // Save state
        localStorage.setItem('sop_sol_filter', filter);
    }

    // Click event
    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            applyFilter(this.getAttribute('data-filter'));
        });
    });

    // Load saved filter
    const savedFilter = localStorage.getItem('sop_sol_filter') || 'all';
    applyFilter(savedFilter);

    // Accept/Reject Click logic
    var approveBtns = document.querySelectorAll('.sop-sol-approve');
    var rejectBtns = document.querySelectorAll('.sop-sol-reject');

    function handleSolicitude(txnId, action) {
        if (!confirm('¿Estás seguro de que deseas ' + (action === 'accept' ? 'aceptar' : 'rechazar') + ' esta solicitud?')) return;

        var formData = new URLSearchParams();
        formData.append('action', 'sop_' + action + '_solicitude');
        formData.append('action_type', action);
        formData.append('txn_id', txnId);
        formData.append('nonce', sop_ajax.nonce);

        fetch(sop_ajax.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.data);
                location.reload();
            } else {
                alert('Error: ' + data.data);
            }
        })
        .catch(e => console.error(e));
    }

    approveBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            handleSolicitude(this.getAttribute('data-txn'), 'accept');
        });
    });

    rejectBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            handleSolicitude(this.getAttribute('data-txn'), 'reject');
        });
    });
});
</script>
