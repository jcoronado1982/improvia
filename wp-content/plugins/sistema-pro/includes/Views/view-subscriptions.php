<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$user_id = get_current_user_id();

// Extraer precios guardados o dejarlos en blanco
$p_semanal = get_user_meta( $user_id, 'sop_precio_semanal', true );
$p_mensual = get_user_meta( $user_id, 'sop_precio_mensual', true );
$p_trimestral = get_user_meta( $user_id, 'sop_precio_trimestral', true );
$p_anual = get_user_meta( $user_id, 'sop_precio_anual', true );
$p_sesiones = get_user_meta( $user_id, 'sop_precio_sesiones', true );
$c_sesiones = get_user_meta( $user_id, 'sop_cantidad_sesiones', true );
?>
<div class="sop-subscriptions-page">
    
    <!-- Top Tabs -->
    <div class="sop-subs-tabs">
        <button class="sop-subs-tab-btn active" data-tab="subscribers" onclick="sopSwitchSubTab(this)"><?php esc_html_e( 'Suscriptores', 'sistema-pro' ); ?> <span class="dashicons dashicons-editor-ul"></span></button>
        <button class="sop-subs-tab-btn outline" data-tab="prices" onclick="sopSwitchSubTab(this)"><?php esc_html_e( 'Precios', 'sistema-pro' ); ?> <span class="dashicons dashicons-shield"></span></button>
        <button class="sop-subs-tab-btn outline" data-tab="collection" onclick="sopSwitchSubTab(this)"><?php esc_html_e( 'Métodos de cobro', 'sistema-pro' ); ?> <span class="dashicons dashicons-bank"></span></button>
    </div>

    <!-- ==================== TAB: SUBSCRIBERS ==================== -->
    <div class="sop-sub-tab-panel" data-panel="subscribers" style="display: block;">
        
        <!-- Subscriber Limit -->
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <span style="font-size: 14px; color: #6b7280;">Límite de Suscriptores</span>
            <span style="font-size: 14px; color: #6b7280;">|</span>
            <select style="border: 1px solid #d1d5db; border-radius: 6px; padding: 4px 10px; font-size: 14px; color: #1a1e29; background: #fff;">
                <option>12</option>
                <option>20</option>
                <option>30</option>
                <option>50</option>
            </select>
        </div>

        <!-- Table Header -->
        <div class="sop-subscribers-table">
            <div class="sop-st-header">
                <div class="sop-st-col sop-st-num"><?php esc_html_e( 'N°', 'sistema-pro' ); ?></div>
                <div class="sop-st-col sop-st-name"><span class="dashicons dashicons-admin-users" style="font-size:14px; width:14px; height:14px;"></span> <?php esc_html_e( 'Suscriptores', 'sistema-pro' ); ?></div>
                <div class="sop-st-col sop-st-plan"><span class="dashicons dashicons-cart" style="font-size:14px; width:14px; height:14px;"></span> <?php esc_html_e( 'Plan', 'sistema-pro' ); ?></div>
                <div class="sop-st-col sop-st-received"><span class="dashicons dashicons-calendar-alt" style="font-size:14px; width:14px; height:14px;"></span> <?php esc_html_e( 'Recibido', 'sistema-pro' ); ?></div>
                <div class="sop-st-col sop-st-deadline"><span class="dashicons dashicons-clock" style="font-size:14px; width:14px; height:14px;"></span> <?php esc_html_e( 'Fecha límite', 'sistema-pro' ); ?></div>
                <div class="sop-st-col sop-st-earnings"><span class="dashicons dashicons-money-alt" style="font-size:14px; width:14px; height:14px;"></span> <?php esc_html_e( 'Ganancia', 'sistema-pro' ); ?></div>
                <div class="sop-st-col sop-st-info"><?php esc_html_e( 'Más información', 'sistema-pro' ); ?></div>
            </div>

            <?php
            // Obtener suscripciones reales desde la base de datos
            $all_transactions = get_option( 'sop_mock_transactions_log', array() );
            
            // Filtrar transacciones donde este entrenador es el trainer_id y la suscripción está ACTIVA
            $my_subscriptions = array();
            foreach ($all_transactions as $txn) {
                if ( isset($txn['trainer_id']) && intval($txn['trainer_id']) === $user_id ) {
                    // Solo mostrar si ya fue capturada (Aceptada)
                    if ( isset($txn['status']) && $txn['status'] === 'SUSCRIPCION_ACTIVA' ) {
                        $my_subscriptions[] = $txn;
                    }
                }
            }

            if ( empty($my_subscriptions) ) :
            ?>
                <div style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                    <span class="dashicons dashicons-groups" style="font-size: 40px; width: 40px; height: 40px; margin-bottom: 10px;"></span>
                    <p style="font-size: 15px;">Aún no tienes suscriptores</p>
                </div>
            <?php
            else :
                foreach ($my_subscriptions as $i => $txn) :
                    $num = $i + 1;
                    $athlete_id = intval($txn['athlete_id']);
                    $athlete = get_userdata($athlete_id);
                    $athlete_name = $athlete ? $athlete->display_name : 'Deportista #' . $athlete_id;
                    
                    $amount = floatval($txn['total_amount']);
                    $recibe_sub = floatval($txn['trainer_earning']);
                    $comision_sub = floatval($txn['platform_fee']);
                    $comision_pct = ($amount > 0) ? round(($comision_sub / $amount) * 100) : 0;
                    
                    $txn_date = isset($txn['date']) ? date('d/m/Y', strtotime($txn['date'])) : '--';
                    $txn_day = isset($txn['date']) ? date_i18n('l', strtotime($txn['date'])) : '--';
                    
                    // Calcular fecha límite dinámica basada en el plan
                    $plan_name_lower = isset($txn['plan_name']) ? strtolower($txn['plan_name']) : '';
                    $days_to_add = 30; // default mensual
                    if (strpos($plan_name_lower, 'semana') !== false) $days_to_add = 7;
                    elseif (strpos($plan_name_lower, 'trimestral') !== false || strpos($plan_name_lower, '3 meses') !== false) $days_to_add = 90;
                    elseif (strpos($plan_name_lower, 'mes') !== false || strpos($plan_name_lower, 'mensual') !== false) $days_to_add = 30;
                    elseif (strpos($plan_name_lower, 'anual') !== false || strpos($plan_name_lower, 'año') !== false) $days_to_add = 365;
                    elseif (strpos($plan_name_lower, 'clase') !== false || strpos($plan_name_lower, 'sesion') !== false) $days_to_add = 30; // Default 30 days for packages too

                    $deadline_ts = isset($txn['date']) ? strtotime($txn['date'] . ' +' . $days_to_add . ' days') : 0;
                    $deadline_date = $deadline_ts ? date('d/m/Y', $deadline_ts) : '--';
                    $days_left = $deadline_ts ? ceil(($deadline_ts - time()) / 86400) : 0;
                    
                    if ($days_left <= 0) {
                        $deadline_day = 'Vencido';
                        $deadline_alert = true;
                    } elseif ($days_left == 1) {
                        $deadline_day = 'Mañana';
                        $deadline_alert = true;
                    } elseif ($days_left <= 3) {
                        $deadline_day = 'En ' . $days_left . ' días';
                        $deadline_alert = true;
                    } else {
                        $deadline_day = date_i18n('l', $deadline_ts);
                        $deadline_alert = false;
                    }
                    
                    $deadline_badge_style = $deadline_alert 
                        ? 'background: #ef4444; color: #fff;'
                        : 'background: #10b981; color: #fff;';
            ?>
            <div class="sop-st-row">
                <div class="sop-st-col sop-st-num"><span class="sop-st-num-circle"><?php echo $num; ?></span></div>
                <div class="sop-st-col sop-st-name">
                    <div style="font-weight: 500;"><?php echo esc_html($athlete_name); ?></div>
                    <div class="sop-st-sub-date"><span class="sop-st-date-box"><?php echo esc_html($txn_date); ?></span></div>
                </div>
                <div class="sop-st-col sop-st-plan">
                    <span class="sop-st-badge" style="background: rgba(9, 33, 137, 0.1); color: #092189; border: 1px solid rgba(9, 33, 137, 0.2);"><?php echo esc_html($txn['plan_name'] ?? 'Plan'); ?></span>
                </div>
                <div class="sop-st-col sop-st-received">
                    <div style="font-weight: 500;"><?php echo esc_html($txn_day); ?></div>
                    <div class="sop-st-badge" style="background: #10b981; color: #fff;"><?php echo esc_html($txn_date); ?></div>
                </div>
                <div class="sop-st-col sop-st-deadline">
                    <div><?php echo esc_html($deadline_day); ?></div>
                    <div class="sop-st-badge" style="<?php echo $deadline_badge_style; ?>"><?php echo esc_html($deadline_date); ?></div>
                </div>
                <div class="sop-st-col sop-st-earnings">
                    <span class="sop-st-price">$<?php echo number_format($amount, 0); ?></span>
                    <div class="sop-st-earnings-detail">
                        <span style="color: #10b981;">Recibes: $<?php echo number_format($recibe_sub, 2); ?></span>
                        <span style="color: #ef4444;">Comisión (<?php echo $comision_pct; ?>%): -$<?php echo number_format($comision_sub, 2); ?></span>
                    </div>
                </div>
                <div class="sop-st-col sop-st-info">
                    <button type="button" class="sop-st-more-btn" title="Más opciones">
                        <svg width="4" height="16" viewBox="0 0 4 16" fill="#6b7280"><circle cx="2" cy="2" r="2"/><circle cx="2" cy="8" r="2"/><circle cx="2" cy="14" r="2"/></svg>
                    </button>
                </div>
            </div>
            <?php endforeach;
            endif; ?>
        </div>

        <!-- Warning Footer -->
        <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 12px 20px; margin-top: 20px; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; color: #92400e;">
            <span class="dashicons dashicons-warning" style="font-size: 16px; width: 16px; height: 16px; color: #f59e0b;"></span>
            <?php esc_html_e( 'Has superado el límite de jugadores que estableciste', 'sistema-pro' ); ?> (10 <?php esc_html_e( 'jugadores', 'sistema-pro' ); ?>)
        </div>
    </div>

    <!-- ==================== TAB: PRICES ==================== -->
    <div class="sop-sub-tab-panel" data-panel="prices" style="display: none;">

    <!-- Headers -->
    <div class="sop-subs-header-row">
        <span class="sop-subs-col-left"><span class="dashicons dashicons-calendar-alt"></span> Modo</span>
        <span class="sop-subs-col-right"><span class="dashicons dashicons-sort"></span> Precio</span>
    </div>

    <!-- Formulario -->
    <form id="sop-subscriptions-form">
        <?php wp_nonce_field( 'sop_profile_nonce', 'nonce' ); ?>
        <input type="hidden" name="action" value="sop_update_profile">
        <input type="hidden" name="sop_form_type" value="subscriptions">
        
        <?php
        $periodos = array(
            'semanal' => array('label' => 'Semanal', 'val' => $p_semanal),
            'mensual' => array('label' => 'Mensual', 'val' => $p_mensual),
            'trimestral' => array('label' => 'Trimestral', 'val' => $p_trimestral),
            'anual' => array('label' => 'Anual', 'val' => $p_anual),
        );
        
        $paquetes_sesiones = array();
        for ($i = 1; $i <= 6; $i++) {
            $qty = get_user_meta( $user_id, 'sop_cantidad_sesiones_' . $i, true );
            $price = get_user_meta( $user_id, 'sop_precio_sesiones_' . $i, true );
            $paquetes_sesiones[$i] = array('qty' => $qty, 'price' => $price);
        }
        
        // Determinar cuántos paquetes están activos para el botón
        $active_count = 0;
        
        // Estado de "Primera consulta gratis"
        $consulta_gratis = get_user_meta( $user_id, 'sop_consulta_gratis', true );
        $is_gratis_checked = ($consulta_gratis === 'yes') ? 'checked' : '';
        ?>

        <!-- OPCIONES ADICIONALES -->
        <div style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.3); padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="sop-subs-dot" style="background-color: #10b981;"></span>
                <span style="font-weight: 500; color: #1a1e29;">Primera consulta gratis</span>
            </div>
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="sop_consulta_gratis" value="yes" <?php echo $is_gratis_checked; ?> style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-size: 13px; color: #6b7280;">Activar promoción</span>
            </label>
        </div>

        <!-- RENDER FIXED PERIODS (NOW REMOVABLE) -->
        <div id="sop-fixed-periods-container">
            <?php foreach ($periodos as $key => $data) :
                $val = $data['val'];
                $has_data = !empty($val) && floatval($val) > 0;
                $display_style = $has_data ? 'display: flex;' : 'display: none;';
                
                $row_class = $has_data ? 'active-row' : 'inactive-row';
                $dot_class = $has_data ? 'dot-active' : 'dot-inactive';
                
                $f_val = floatval($val);
                $recibe = $f_val * 0.90;
                $comision = $f_val * 0.10;
            ?>
            <div class="sop-subs-period-row sop-pricing-row fixed-period-row <?php echo $row_class; ?>" data-type="fixed" style="<?php echo $display_style; ?> padding: 12px 15px; flex-wrap: nowrap; gap: 10px; overflow-x: auto;">
                <div class="sop-subs-period-left" style="display: flex; align-items: center; gap: 8px; flex: 0 0 auto;">
                    <span class="sop-subs-dot <?php echo $dot_class; ?>"></span>
                    <span class="sop-subs-period-name" style="white-space: nowrap; min-width: max-content;"><?php echo esc_html($data['label']); ?></span>
                </div>
                
                <div class="sop-subs-period-right" style="display: flex; align-items: center; flex: 1 1 auto; justify-content: flex-end; gap: 10px; min-width: max-content;">
                    <div class="sop-subs-calculation" style="display: flex; align-items: center; gap: 8px; white-space: nowrap; font-size: 13px;">
                        <span class="calc-recibe">Recibes (90%): <span class="val-recibe">$<?php echo number_format($recibe, 2); ?></span></span>
                        <span class="calc-comision">Comisión (10%): <span class="val-comision">-$<?php echo number_format($comision, 2); ?></span></span>
                    </div>
                    
                    <span class="sop-subs-equals" style="font-weight: bold; margin: 0 5px;">=</span>
                    
                    <div class="sop-subs-input-wrap" style="display: flex; align-items: center; gap: 4px; flex: 0 0 auto;">
                        <span class="sop-subs-currency" style="color: #6b7280; font-weight: 500;">$</span>
                        <input type="number" step="0.01" class="sop-subs-price-input" 
                               name="sop_precio_<?php echo esc_attr($key); ?>" 
                               oninput="sopCalculateRow(this)"
                               value="<?php echo esc_attr($val); ?>"
                               style="width: 80px;">
                    </div>

                    <button type="button" onclick="sopRemovePricingRow(this)" style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; opacity: 0.6; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'" title="Eliminar plan">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- RENDER SESSION PACKAGES -->
        <div id="sop-session-packages-container">
            <?php foreach ($paquetes_sesiones as $index => $data): 
                $has_data = !empty($data['price']) && !empty($data['qty']);
                if ($has_data) $active_count++;
                
                // Show by default ONLY if it has data
                $should_show = $has_data;
                $display_style = $should_show ? 'display: flex;' : 'display: none;';
                
                $is_active = $has_data && floatval($data['price']) > 0 && intval($data['qty']) > 0;
                $row_class = $is_active ? 'active-row' : 'inactive-row';
                $dot_class = $is_active ? 'dot-active' : 'dot-inactive';
                
                $f_val = floatval($data['price']);
                $recibe = $f_val * 0.90;
                $comision = $f_val * 0.10;
            ?>
            <div class="sop-subs-period-row sop-pricing-row session-package-row <?php echo $row_class; ?>" data-index="<?php echo $index; ?>" data-type="session" style="<?php echo $display_style; ?> padding: 12px 15px; flex-wrap: nowrap; gap: 10px; overflow-x: auto;">
                <div class="sop-subs-period-left" style="display: flex; align-items: center; gap: 8px; flex: 0 0 auto;">
                    <span class="sop-subs-dot <?php echo $dot_class; ?>"></span>
                    <span class="sop-subs-period-name" style="white-space: nowrap; min-width: max-content;">Paquete de</span>
                    <input type="number" 
                           name="sop_cantidad_sesiones_<?php echo $index; ?>" 
                           class="sop-subs-quantity-input" 
                           placeholder="Nº"
                           value="<?php echo esc_attr($data['qty']); ?>" 
                           oninput="sopCalculateRow(this)"
                           style="width: 50px; padding: 4px; border: 1px solid #d1d5db; border-radius: 4px; text-align: center; font-size: 14px;">
                    <span class="sop-subs-period-name" style="white-space: nowrap;">Sesiones:</span>
                </div>
                
                <div class="sop-subs-period-right" style="display: flex; align-items: center; flex: 1 1 auto; justify-content: flex-end; gap: 10px; min-width: max-content;">
                    <div class="sop-subs-calculation" style="display: flex; align-items: center; gap: 8px; white-space: nowrap; font-size: 13px;">
                        <span class="calc-recibe">Recibes (90%): <span class="val-recibe">$<?php echo number_format($recibe, 2); ?></span></span>
                        <span class="calc-comision">Comisión (10%): <span class="val-comision">-$<?php echo number_format($comision, 2); ?></span></span>
                    </div>
                    
                    <span class="sop-subs-equals" style="font-weight: bold; margin: 0 5px;">=</span>
                    
                    <div class="sop-subs-input-wrap" style="display: flex; align-items: center; gap: 4px; flex: 0 0 auto;">
                        <span class="sop-subs-currency" style="color: #6b7280; font-weight: 500;">$</span>
                        <input type="number" step="0.01" class="sop-subs-price-input" 
                               name="sop_precio_sesiones_<?php echo $index; ?>" 
                               oninput="sopCalculateRow(this)"
                               value="<?php echo esc_attr($data['price']); ?>"
                               style="width: 80px;">
                    </div>
                    
                    <button type="button" onclick="sopRemovePricingRow(this)" style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; opacity: 0.6; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'" title="Eliminar paquete">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- ADD BUTTONS (Separated) -->
        <div style="margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap;" id="sop-add-buttons-container">
            <?php 
            // Calculate if we should show buttons initially
            $hidden_plans = 0;
            foreach ($periodos as $key => $data) { if (empty($data['val'])) $hidden_plans++; }
            
            $hidden_packages = 0;
            foreach ($paquetes_sesiones as $p) { if (empty($p['price'])) $hidden_packages++; }
            ?>
            
            <div id="sop-add-plan-wrapper" style="<?php echo ($hidden_plans > 0) ? 'display: block;' : 'display: none;'; ?>">
                <button type="button" onclick="sopAddPlanRow()" style="background: transparent; border: 1px dashed #1d4ed8; color: #1d4ed8; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Agregar plan
                </button>
            </div>

            <div id="sop-add-package-wrapper" style="<?php echo ($hidden_packages > 0) ? 'display: block;' : 'display: none;'; ?>">
                <button type="button" onclick="sopAddPackageRow()" style="background: transparent; border: 1px dashed #1d4ed8; color: #1d4ed8; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Agregar paquete
                </button>
            </div>
        </div>

        <script>
        function sopAddPlanRow() {
            var fixedRows = document.querySelectorAll('.fixed-period-row');
            for (var i = 0; i < fixedRows.length; i++) {
                if (fixedRows[i].style.display === 'none') {
                    fixedRows[i].style.display = 'flex';
                    // Check if that was the last one
                    var visibleCount = Array.from(fixedRows).filter(r => r.style.display !== 'none').length;
                    if (visibleCount >= fixedRows.length) {
                        document.getElementById('sop-add-plan-wrapper').style.display = 'none';
                    }
                    return;
                }
            }
        }

        function sopAddPackageRow() {
            var sessionRows = document.querySelectorAll('.session-package-row');
            for (var j = 0; j < sessionRows.length; j++) {
                if (sessionRows[j].style.display === 'none') {
                    sessionRows[j].style.display = 'flex';
                    // Check if that was the last one
                    var visibleCount = Array.from(sessionRows).filter(r => r.style.display !== 'none').length;
                    if (visibleCount >= sessionRows.length) {
                        document.getElementById('sop-add-package-wrapper').style.display = 'none';
                    }
                    return;
                }
            }
        }
        
        function sopRemovePricingRow(btn) {
            var row = btn.closest('.sop-pricing-row');
            if (!row) return;
            
            var type = row.getAttribute('data-type'); // 'fixed' or 'session'
            
            // Clear inputs
            var inputs = row.querySelectorAll('input');
            inputs.forEach(inp => inp.value = '');
            
            // Reset calculation display
            var valRecibe = row.querySelector('.val-recibe');
            var valComision = row.querySelector('.val-comision');
            var dot = row.querySelector('.sop-subs-dot');
            if (valRecibe) valRecibe.textContent = '$0.00';
            if (valComision) valComision.textContent = '-$0.00';
            if (dot) { dot.classList.remove('dot-active'); dot.classList.add('dot-inactive'); }
            
            // Hide the row
            row.style.setProperty('display', 'none', 'important');
            row.classList.remove('active-row');
            row.classList.add('inactive-row');
            
            // Show the appropriate add button again
            if (type === 'fixed') {
                var wrapper = document.getElementById('sop-add-plan-wrapper');
                if (wrapper) wrapper.style.display = 'block';
            } else if (type === 'session') {
                var wrapper = document.getElementById('sop-add-package-wrapper');
                if (wrapper) wrapper.style.display = 'block';
            }
        }
        </script>

        <div class="sop-subs-actions">
            <button type="submit" id="sop-subs-save-btn" class="sop-btn-blue" style="min-width: 150px; border-radius: 8px;">Save</button>
        </div>
        <div id="sop-subs-msg" style="text-align: right; margin-top: 10px; font-size: 0.9rem;"></div>
    </form>


    </div><!-- END TAB: PRICES -->

    <!-- ==================== TAB: COLLECTION METHODS ==================== -->
    <div class="sop-sub-tab-panel" data-panel="collection" style="display: none;">
        <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
            <span class="dashicons dashicons-bank" style="font-size: 48px; width: 48px; height: 48px; margin-bottom: 15px;"></span>
            <p style="font-size: 16px;">Métodos de cobro — Próximamente</p>
        </div>
    </div>

</div>

<style>
.sop-subscriptions-page {
    font-family: var(--sop-font-main);
    color: #1a1e29; /* Dark text for light mode usually inside content area */
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

/* Solo si estamos en un contenedor con fondo oscuro heredado (el diseño de WP puede variar). 
   Vamos a forzar que el texto sea visible */
.sop-subscriptions-page * {
    box-sizing: border-box;
}

.sop-subs-tabs {
    display: flex;
    gap: 15px;
    margin-bottom: 40px;
}

.sop-subs-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    background: transparent;
    transition: all 0.2s;
}

.sop-subs-tab-btn.active {
    background: #092189;
    color: #ffffff;
    border: 1px solid #092189;
}

.sop-subs-tab-btn.outline {
    background: transparent;
    color: #092189;
    border: 1.5px solid #092189;
}

.sop-subs-tab-btn.outline .dashicons {
    color: #092189;
}

.sop-subs-tab-btn.active .dashicons {
    color: #ffffff;
}

.sop-subs-header-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
}
body .sop-subs-header-row { color: rgba(255,255,255,0.6); }

.sop-subs-header-row span.dashicons {
    font-size: 16px;
    width: 16px; height: 16px; margin-top: 2px;
}

.sop-subs-col-left, .sop-subs-col-right {
    display: flex; align-items: center; gap: 5px;
}
.sop-subs-col-right {
    padding-right: 100px; /* Offset to align with price inputs somewhat */
}

/* Filas de precios */
.sop-subs-period-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

body .sop-subs-period-row.active-row {
    background: #ffffff;
    border: 1px solid transparent;
}
body .sop-subs-period-row.inactive-row {
    background: #ffffff;
    border: 1px solid #ff4d4f;
}

.sop-subs-period-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.sop-subs-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.dot-active { background-color: #10b981; }
.dot-inactive { background-color: #ff4d4f; }

.sop-subs-period-name {
    font-weight: 500;
    color: #1a1e29;
}

.sop-subs-period-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.sop-subs-calc-box {
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-size: 0.75rem;
    text-align: right;
}

.sop-calc-line {
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: flex-end;
}

.sop-calc-green {
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 500;
}
.sop-calc-grey {
    color: #6b7280;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
}

.sop-subs-equals {
    font-weight: bold;
    color: #9ca3af;
    margin: 0 5px;
}

.sop-subs-input-wrap {
    display: flex;
    align-items: center;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0 10px;
    background: #ffffff;
    width: 100px;
    transition: border-color 0.2s;
}
.sop-subs-input-wrap:focus-within {
    border-color: #092189;
}

.sop-subs-currency {
    color: #6b7280;
    font-weight: 500;
}

.sop-subs-price-input {
    border: none;
    outline: none;
    width: 100%;
    padding: 10px 5px;
    font-size: 0.95rem;
    color: #1a1e29;
    text-align: center;
    background: transparent;
    -moz-appearance: textfield;
}
.sop-subs-price-input::-webkit-outer-spin-button,
.sop-subs-price-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.sop-subs-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 30px;
}

/* ============ SUBSCRIBERS TABLE ============ */
.sop-subscribers-table {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
}

.sop-st-header {
    display: grid;
    grid-template-columns: 50px 1.5fr 1.2fr 1fr 1fr 1.5fr 120px;
    gap: 10px;
    padding: 12px 20px;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
    align-items: center;
    white-space: nowrap;
}

.sop-st-row {
    display: grid;
    grid-template-columns: 50px 1.5fr 1.2fr 1fr 1fr 1.5fr 120px;
    gap: 10px;
    padding: 16px 20px;
    background: #ffffff;
    border-bottom: 1px solid #f3f4f6;
    align-items: center;
    transition: background 0.15s;
}
.sop-st-row:hover {
    background: #f9fafb;
}

.sop-st-col {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sop-st-num {
    font-weight: 600;
    color: #1a1e29;
    font-size: 15px;
    justify-content: center;
}

.sop-st-name {
    color: #1a1e29;
    font-weight: 500;
}
.sop-st-sub-date {
    font-size: 12px;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    width: fit-content;
}

.sop-st-badge {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 4px;
    width: fit-content;
    font-weight: 500;
}

.sop-st-price {
    background: #10b981;
    color: #fff;
    padding: 4px 14px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 14px;
    width: fit-content;
}

.sop-st-earnings-detail {
    display: flex;
    flex-direction: column;
    gap: 2px;
    font-size: 11px;
}

.sop-st-info {
    align-items: center;
    justify-content: center;
}

.sop-st-more-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 4px;
    transition: background 0.15s;
}
.sop-st-more-btn:hover {
    background: #f3f4f6;
}

.sop-st-header .sop-st-col {
    flex-direction: row;
    align-items: center;
    gap: 6px;
}

.sop-st-num-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1.5px solid #d1d5db;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 600;
    color: #1a1e29;
}

.sop-st-date-box {
    display: inline-block;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 12px;
    color: #6b7280;
    background: #fff;
}
</style>

<script>
function sopSwitchSubTab(btn) {
    // Update buttons
    var allBtns = document.querySelectorAll('.sop-subs-tab-btn');
    allBtns.forEach(function(b) {
        b.classList.remove('active');
        b.classList.add('outline');
    });
    btn.classList.remove('outline');
    btn.classList.add('active');
    
    // Update panels
    var targetPanel = btn.getAttribute('data-tab');
    var allPanels = document.querySelectorAll('.sop-sub-tab-panel');
    allPanels.forEach(function(p) {
        p.style.display = 'none';
    });
    
    var activePanel = document.querySelector('.sop-sub-tab-panel[data-panel="' + targetPanel + '"]');
    if (activePanel) {
        activePanel.style.display = 'block';
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Patrón Arquitectónico del Proyecto: Array de Formularios AJAX
    const forms = [
        { id: 'sop-subscriptions-form', msgId: 'sop-subs-msg' }
    ];

    forms.forEach(f => {
        const el = document.getElementById(f.id);
        if(!el) return;
        el.addEventListener('submit', (e) => {
            e.preventDefault();
            const msgEl = document.getElementById(f.msgId);
            msgEl.textContent = 'Guardando...';
            msgEl.style.color = '#fff';

            const formData = new FormData(el);
            formData.append('action', 'sop_update_profile');

            const targetUrl = (typeof sop_ajax !== 'undefined' && sop_ajax.ajax_url) ? sop_ajax.ajax_url : '/wp-admin/admin-ajax.php';

            fetch(targetUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msgEl.textContent = '✓ Guardado correctamente';
                    msgEl.style.color = '#10b981';
                } else {
                    msgEl.textContent = data.data || 'Error al guardar';
                    msgEl.style.color = '#ff4d4f';
                }
            })
            .catch(error => {
                msgEl.textContent = 'Error grave de red. Intenta nuevamente.';
                msgEl.style.color = '#ff4d4f';
            });
        });
    });
});
</script>

<script>
window.sopCalculateRow = function(element) {
    var row = element.closest('.sop-subs-period-row');
    if (!row) return;
    
    var priceInput = row.querySelector('.sop-subs-price-input');
    var qtyInput = row.querySelector('.sop-subs-quantity-input');
    
    var val = parseFloat(priceInput.value);
    var isValid = true;
    
    if (isNaN(val) || val <= 0) {
        isValid = false;
    }
    
    if (qtyInput) {
        var qty = parseInt(qtyInput.value);
        if (isNaN(qty) || qty <= 0) {
            isValid = false;
        }
    }
    
    var dot = row.querySelector('.sop-subs-dot');
    var valRecibe = row.querySelector('.val-recibe');
    var valComision = row.querySelector('.val-comision');
    
    if (!isValid) {
        row.classList.remove('active-row');
        row.classList.add('inactive-row');
        if(dot) { dot.classList.remove('dot-active'); dot.classList.add('dot-inactive'); }
        if(valRecibe) valRecibe.textContent = '$0.00';
        if(valComision) valComision.textContent = '-$0.00';
    } else {
        row.classList.remove('inactive-row');
        row.classList.add('active-row');
        if(dot) { dot.classList.remove('dot-inactive'); dot.classList.add('dot-active'); }
        
        var recibe = val * 0.90;
        var comision = val * 0.10;
        if(valRecibe) valRecibe.textContent = '$' + recibe.toFixed(2);
        if(valComision) valComision.textContent = '-$' + comision.toFixed(2);
    }
};

window.sopSubmitSubscriptions = function(e, formElement) {
    e.preventDefault();
    
    var btn = document.getElementById('sop-subs-save-btn');
    var msg = document.getElementById('sop-subs-msg');
    
    if(btn) {
        btn.disabled = true;
        btn.textContent = 'Guardando...';
    }
    if(msg) {
        msg.style.color = '#6b7280';
        msg.textContent = '';
    }

    var formData = new FormData(formElement);
    var targetUrl = (typeof sop_ajax !== 'undefined' && sop_ajax.ajax_url) ? sop_ajax.ajax_url : '/wp-admin/admin-ajax.php';

    fetch(targetUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if(!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if(data.success) {
            if(msg) { msg.style.color = '#10b981'; msg.textContent = 'Precios actualizados exitosamente.'; }
        } else {
            if(msg) { msg.style.color = '#ff4d4f'; msg.textContent = 'Error: ' + (data.data || 'Problema de conexión'); }
        }
    })
    .catch(error => {
        if(msg) { msg.style.color = '#ff4d4f'; msg.textContent = 'Error grave de red. Intenta nuevamente.'; }
    })
    .finally(() => {
        if(btn) {
            btn.disabled = false;
            btn.textContent = 'Save';
        }
        setTimeout(function(){ if(msg) msg.textContent = ''; }, 4000);
    });
    
    return false;
};
</script>
