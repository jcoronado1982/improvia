<div class="wrap">
    <h1>Logs de Transacciones Simuladas (Stripe Mock)</h1>
    <p>Estos registros simulan el dinero que habría entrado al sistema y la partición (Split Payment) entre Improvia y los Entrenadores.</p>

    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Atleta (ID)</th>
                <th>Entrenador (ID)</th>
                <th>Total Pagado</th>
                <th>Comisión Improvia (10%)</th>
                <th>Ganancia Entrenador</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $logs = get_option( 'sop_mock_transactions_log', array() );
            if ( empty($logs) ) {
                echo '<tr><td colspan="7">No hay transacciones simuladas aún.</td></tr>';
            } else {
                foreach ( array_reverse($logs) as $log ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $log['date'] ) . '</td>';
                    echo '<td>User #' . esc_html( $log['athlete_id'] ) . '</td>';
                    echo '<td>User #' . esc_html( $log['trainer_id'] ) . '</td>';
                    echo '<td><strong>' . esc_html( $log['total_amount'] ) . '€</strong></td>';
                    echo '<td style="color:red;">' . esc_html( $log['platform_fee'] ) . '€</td>';
                    echo '<td style="color:green;">' . esc_html( $log['trainer_earning'] ) . '€</td>';
                    echo '<td><span class="dashicons dashicons-yes-alt" style="color:green;"></span> ' . esc_html( $log['status'] ) . '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>
