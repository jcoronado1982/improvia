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
    
    <!-- Top Tabs (Solo Maqueta visual por ahora según Figma) -->
    <div class="sop-subs-tabs">
        <button class="sop-subs-tab-btn outline">Subscribers <span class="dashicons dashicons-editor-ul"></span></button>
        <button class="sop-subs-tab-btn active">Prices <span class="dashicons dashicons-shield"></span></button>
        <button class="sop-subs-tab-btn outline">Collection methods <span class="dashicons dashicons-bank"></span></button>
    </div>

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
        
        // Bloque especial para Sesiones
        $is_sesiones_active = (!empty($p_sesiones) && floatval($p_sesiones) > 0) && (!empty($c_sesiones) && intval($c_sesiones) > 0);
        $s_row_class = $is_sesiones_active ? 'active-row' : 'inactive-row';
        $s_dot_class = $is_sesiones_active ? 'dot-active' : 'dot-inactive';
        $f_val_s = floatval($p_sesiones);
        $s_recibe = $f_val_s * 0.85;
        $s_comision = $f_val_s * 0.15;
        ?>
        
        <div class="sop-subs-period-row <?php echo $s_row_class; ?>">
            <div class="sop-subs-period-left">
                <span class="sop-subs-dot <?php echo $s_dot_class; ?>"></span>
                <span class="sop-subs-period-name">Paquete de Sesiones:</span>
                <input type="number" 
                       name="sop_cantidad_sesiones" 
                       class="sop-subs-quantity-input" 
                       placeholder="Nº"
                       value="<?php echo esc_attr($c_sesiones); ?>" 
                       oninput="sopCalculateRow(this)"
                       style="width: 60px; padding: 5px; border: 1px solid #d1d5db; border-radius: 4px; text-align: center;">
                <span style="font-size: 0.9em; color:#6b7280;">sesiones</span>
            </div>
            
            <div class="sop-subs-period-right">
                <div class="sop-subs-calc-box">
                    <div class="sop-calc-line sop-calc-green">
                        <span class="dashicons dashicons-clock" style="font-size:12px;width:12px;height:12px;margin-top:2px;"></span> 
                        Recibes (85%): <strong class="val-recibe">$<?php echo number_format($s_recibe, 2); ?></strong>
                    </div>
                    <div class="sop-calc-line sop-calc-grey">
                        <span class="dashicons dashicons-clock" style="font-size:12px;width:12px;height:12px;margin-top:2px;"></span> 
                        Comisión (15%): <span class="val-comision">-$<?php echo number_format($s_comision, 2); ?></span>
                    </div>
                </div>
                
                <span class="sop-subs-equals">=</span>
                
                <div class="sop-subs-input-wrap">
                    <span class="sop-subs-currency">$</span>
                    <input type="number" step="0.01" class="sop-subs-price-input" 
                           name="sop_precio_sesiones" 
                           oninput="sopCalculateRow(this)"
                           value="<?php echo esc_attr($p_sesiones); ?>">
                </div>
            </div>
        </div>

        <?php
        foreach ($periodos as $key => $data) :
            $val = $data['val'];
            $is_active = !empty($val) && floatval($val) > 0;
            $row_class = $is_active ? 'active-row' : 'inactive-row';
            $dot_class = $is_active ? 'dot-active' : 'dot-inactive';
            
            // Calculos base para SSR (Server Side Rendering)
            $f_val = floatval($val);
            $recibe = $f_val * 0.85;
            $comision = $f_val * 0.15;
        ?>
        <div class="sop-subs-period-row <?php echo $row_class; ?>">
            <div class="sop-subs-period-left">
                <span class="sop-subs-dot <?php echo $dot_class; ?>"></span>
                <span class="sop-subs-period-name"><?php echo esc_html($data['label']); ?></span>
            </div>
            
            <div class="sop-subs-period-right">
                <div class="sop-subs-calc-box">
                    <div class="sop-calc-line sop-calc-green">
                        <span class="dashicons dashicons-clock" style="font-size:12px;width:12px;height:12px;margin-top:2px;"></span> 
                        Recibes (85%): <strong class="val-recibe">$<?php echo number_format($recibe, 2); ?></strong>
                    </div>
                    <div class="sop-calc-line sop-calc-grey">
                        <span class="dashicons dashicons-clock" style="font-size:12px;width:12px;height:12px;margin-top:2px;"></span> 
                        Comisión (15%): <span class="val-comision">-$<?php echo number_format($comision, 2); ?></span>
                    </div>
                </div>
                
                <span class="sop-subs-equals">=</span>
                
                <div class="sop-subs-input-wrap">
                    <span class="sop-subs-currency">$</span>
                    <input type="number" step="0.01" class="sop-subs-price-input" 
                           name="sop_precio_<?php echo esc_attr($key); ?>" 
                           oninput="sopCalculateRow(this)"
                           value="<?php echo esc_attr($val); ?>">
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="sop-subs-actions">
            <button type="submit" id="sop-subs-save-btn" class="sop-btn-blue" style="min-width: 150px; border-radius: 8px;">Save</button>
        </div>
        <div id="sop-subs-msg" style="text-align: right; margin-top: 10px; font-size: 0.9rem;"></div>
    </form>

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
    color: #092189; /* Ajustar según si el fondo es oscuro o claro. Asumimos claro por la imagen */
    border: 1px solid #092189;
}

/* En caso de que el wrapper exterior siga siendo el oscuro de la plantilla Atleta/Coach */
body .sop-subs-tab-btn.outline {
    border-color: rgba(255,255,255,0.2) !important;
    color: #ffffff !important;
}
body .sop-subs-tab-btn.active {
    background: #092189 !important;
    color: #ffffff !important;
    border-color: #092189 !important;
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

</style>

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
        
        var recibe = val * 0.85;
        var comision = val * 0.15;
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
