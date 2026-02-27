<?php
/**
 * Componente compartido: Redes Sociales Profesionales
 * Layout Figma Coach: inline compacto (Red social | @Example | Add)
 * Se usa tanto para Atletas como para Entrenadores/Especialistas
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$redes_sociales = get_terms( array( 'taxonomy' => 'sop_red_social', 'hide_empty' => false ) );
?>

<div class="sop-tab-panel sop-tab-panel-basic">
    <h3 class="sop-title-with-line"><?php esc_html_e( 'RRSS PROFESIONAL', 'sistema-pro' ); ?></h3>
    <div class="sop-coach-form-box">
        <div class="sop-prof-row-tight">
            <div style="flex: 0 0 160px;">
                <label class="sop-label"><?php esc_html_e( 'Red social', 'sistema-pro' ); ?></label>
                <select id="sop-rrss-type" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <?php foreach ($redes_sociales as $red) : ?>
                        <option value="<?php echo $red->term_id; ?>"><?php echo esc_html($red->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="sop-label"><?php esc_html_e( 'Usuario', 'sistema-pro' ); ?></label>
                <input type="text" id="sop-rrss-value" class="sop-input" placeholder="<?php esc_attr_e( '@Ejemplo', 'sistema-pro' ); ?>">
            </div>
            <button type="button" id="sop-add-rrss" class="sop-btn-blue" style="margin-bottom: 0;"><?php esc_html_e( 'Agregar', 'sistema-pro' ); ?></button>
        </div>
    </div>

    <div id="sop-rrss-list" class="sop-chip-list">
        <!-- Dinámico vía JS -->
    </div>
</div>
