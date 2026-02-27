<?php
/**
 * Componente exclusivo Coach: Estudios Secundarios
 * Formulario en caja con bordes — Layout Figma: una sola fila
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$certificaciones = get_terms( array( 'taxonomy' => 'sop_certificacion', 'hide_empty' => false ) );
$institutos      = get_terms( array( 'taxonomy' => 'sop_instituto', 'hide_empty' => false ) );
$lugares         = get_terms( array( 'taxonomy' => 'sop_lugar_estudio', 'hide_empty' => false ) );
?>

<div class="sop-tab-panel sop-tab-panel-basic">
    <h3 class="sop-title-with-line"><?php esc_html_e( 'ESTUDIOS SECUNDARIOS', 'sistema-pro' ); ?></h3>
    
    <div class="sop-coach-form-box">
        <div class="sop-coach-form-grid-4">
            <div>
                <label class="sop-label"><?php esc_html_e( 'Certificado o premio', 'sistema-pro' ); ?></label>
                <select id="sop-estudios-cert" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <?php foreach ($certificaciones as $cert) : ?>
                        <option value="<?php echo $cert->term_id; ?>"><?php echo esc_html($cert->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="sop-label"><?php esc_html_e( 'Instituto', 'sistema-pro' ); ?></label>
                <input type="text" id="sop-estudios-instituto" class="sop-input" placeholder="<?php esc_attr_e( 'Lugar de estudio', 'sistema-pro' ); ?>">
            </div>
            <div>
                <label class="sop-label"><?php esc_html_e( 'Lugar de estudio', 'sistema-pro' ); ?></label>
                <select id="sop-estudios-lugar" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <?php foreach ($lugares as $lg) : ?>
                        <option value="<?php echo $lg->term_id; ?>"><?php echo esc_html($lg->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="sop-label"><?php esc_html_e( 'Fecha', 'sistema-pro' ); ?></label>
                <input type="text" id="sop-estudios-fecha" class="sop-input sop-datepicker">
            </div>
            <button type="button" id="sop-add-estudios" class="sop-btn-blue"><?php esc_html_e( 'Agregar', 'sistema-pro' ); ?></button>
        </div>
    </div>

    <div id="sop-estudios-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
        <!-- Dinámico vía JS -->
    </div>
</div>
