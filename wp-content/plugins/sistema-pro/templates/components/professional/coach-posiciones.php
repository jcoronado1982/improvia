<?php
/**
 * Componente exclusivo Coach: Posiciones Especializadas
 * Tags con pills — activas en azul oscuro #092189 con X para deseleccionar
 * Incluye sub-box para Posición y sub-box para Fases de juego
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$posiciones        = get_terms( array( 'taxonomy' => 'sop_posicion', 'hide_empty' => false ) );
$fases_ofensivas   = get_terms( array( 'taxonomy' => 'sop_fase_ofensiva', 'hide_empty' => false ) );
$fases_defensivas  = get_terms( array( 'taxonomy' => 'sop_fase_defensiva', 'hide_empty' => false ) );

$posiciones_act       = get_user_meta( $user->ID, 'sop_posiciones_ids', true ) ?: array();
$fases_ofensivas_act  = get_user_meta( $user->ID, 'sop_fase_ofensiva_ids', true ) ?: array();
$fases_defensivas_act = get_user_meta( $user->ID, 'sop_fase_defensiva_ids', true ) ?: array();
?>

<div class="sop-tab-panel sop-tab-panel-basic">
    <h3 class="sop-title-with-line"><?php esc_html_e( 'POSICIONES ESPECIALIZADAS', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></h3>
    
    <!-- Sub-box Posición -->
    <div class="sop-posiciones-box">
        <p class="sop-prof-section-label"><?php esc_html_e( 'Posicion', 'sistema-pro' ); ?></p>
        <div class="sop-tag-grid">
            <?php foreach ($posiciones as $pos) : 
                $is_active = in_array($pos->term_id, (array)$posiciones_act);
            ?>
                <label class="sop-tag-checkbox <?php echo $is_active ? 'active' : ''; ?>">
                    <input type="checkbox" name="sop_posiciones_ids[]" value="<?php echo $pos->term_id; ?>" <?php checked($is_active); ?> style="display: none;">
                    <span><?php echo esc_html($pos->name); ?></span>
                    <?php if ($is_active) : ?><span class="sop-tag-x">✕</span><?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sub-box Fases de juego -->
    <div class="sop-posiciones-box">
        <p class="sop-prof-section-label"><?php esc_html_e( 'Fases de juego', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></p>
        
        <!-- Fase Ofensiva -->
        <p class="sop-prof-sub-label"><?php esc_html_e( 'Fase Ofensiva', 'sistema-pro' ); ?></p>
        <div class="sop-tag-grid-spaced">
            <?php foreach ($fases_ofensivas as $fo) : 
                $is_active_fo = in_array($fo->term_id, (array)$fases_ofensivas_act);
            ?>
                <label class="sop-tag-checkbox <?php echo $is_active_fo ? 'active' : ''; ?>">
                    <input type="checkbox" name="sop_fase_ofensiva_ids[]" value="<?php echo $fo->term_id; ?>" <?php checked($is_active_fo); ?> style="display: none;">
                    <span><?php echo esc_html($fo->name); ?></span>
                    <?php if ($is_active_fo) : ?><span class="sop-tag-x">✕</span><?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- Fase Defensiva -->
        <p class="sop-prof-sub-label"><?php esc_html_e( 'Fase Defensiva', 'sistema-pro' ); ?></p>
        <div class="sop-tag-grid">
            <?php foreach ($fases_defensivas as $fd) : 
                $is_active_fd = in_array($fd->term_id, (array)$fases_defensivas_act);
            ?>
                <label class="sop-tag-checkbox <?php echo $is_active_fd ? 'active' : ''; ?>">
                    <input type="checkbox" name="sop_fase_defensiva_ids[]" value="<?php echo $fd->term_id; ?>" <?php checked($is_active_fd); ?> style="display: none;">
                    <span><?php echo esc_html($fd->name); ?></span>
                    <?php if ($is_active_fd) : ?><span class="sop-tag-x">✕</span><?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sop-tag-checkbox').forEach(function(label) {
        label.addEventListener('click', function(e) {
            // Prevenir el toggle nativo del label para evitar doble-toggle
            e.preventDefault();
            
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('active', checkbox.checked);
            
            // Toggle the X mark
            let xSpan = this.querySelector('.sop-tag-x');
            if (checkbox.checked) {
                if (!xSpan) {
                    xSpan = document.createElement('span');
                    xSpan.className = 'sop-tag-x';
                    xSpan.textContent = '✕';
                    this.appendChild(xSpan);
                }
            } else {
                if (xSpan) xSpan.remove();
            }
        });
    });
});
</script>
