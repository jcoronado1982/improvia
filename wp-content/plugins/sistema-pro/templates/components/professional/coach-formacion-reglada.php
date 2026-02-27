<?php
/**
 * Componente exclusivo Coach: Formación Reglada
 * Formulario en caja con bordes + zona de subida de documentos
 * Layout Figma: Row1 (Título, Instituto, Tipo licencia) | Row2 (País, Fecha, Add)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$titulos      = get_terms( array( 'taxonomy' => 'sop_titulo', 'hide_empty' => false ) );
$institutos   = get_terms( array( 'taxonomy' => 'sop_instituto', 'hide_empty' => false ) );
$lugares      = get_terms( array( 'taxonomy' => 'sop_lugar_estudio', 'hide_empty' => false ) );
$tipos_titulo = get_terms( array( 'taxonomy' => 'sop_tipo_titulo', 'hide_empty' => false ) );
$paises       = get_terms( array( 'taxonomy' => 'sop_pais', 'hide_empty' => false ) );

// Documento de certificación del coach
$coach_doc_id   = get_user_meta( $user->ID, 'sop_coach_certification_doc_id', true );
$coach_doc_url  = $coach_doc_id ? wp_get_attachment_url( $coach_doc_id ) : '';
$coach_doc_name = $coach_doc_id ? basename( get_attached_file( $coach_doc_id ) ) : '';
?>

<div class="sop-tab-panel sop-tab-panel-basic">
    <h3 class="sop-title-with-line"><?php esc_html_e( 'FORMACION REGLADA', 'sistema-pro' ); ?></h3>
    
    <!-- Formulario dentro de caja con bordes -->
    <div class="sop-coach-form-box">
        <div class="sop-coach-form-grid">
            <div>
                <label class="sop-label"><?php esc_html_e( 'Título de estudio', 'sistema-pro' ); ?></label>
                <select id="sop-formacion-titulo" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <?php foreach ($titulos as $t) : ?>
                        <option value="<?php echo $t->term_id; ?>"><?php echo esc_html($t->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="sop-label"><?php esc_html_e( 'Instituto', 'sistema-pro' ); ?></label>
                <select id="sop-formacion-instituto" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <?php foreach ($institutos as $inst) : ?>
                        <option value="<?php echo $inst->term_id; ?>"><?php echo esc_html($inst->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="sop-label"><?php esc_html_e( 'Tipo de licencia', 'sistema-pro' ); ?></label>
                <select id="sop-formacion-tipo" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <?php foreach ($tipos_titulo as $tt) : ?>
                        <option value="<?php echo $tt->term_id; ?>"><?php echo esc_html($tt->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="sop-coach-form-grid-row2">
            <div>
                <label class="sop-label"><?php esc_html_e( 'País', 'sistema-pro' ); ?></label>
                <select id="sop-formacion-pais" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                    <option value=""></option>
                    <?php foreach ($paises as $pa) : ?>
                        <option value="<?php echo $pa->term_id; ?>"><?php echo esc_html($pa->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="sop-label"><?php esc_html_e( 'Fecha de egreso', 'sistema-pro' ); ?></label>
                <input type="text" id="sop-formacion-fecha" class="sop-input sop-datepicker">
            </div>
            <button type="button" id="sop-add-formacion" class="sop-btn-blue"><?php esc_html_e( 'Agregar', 'sistema-pro' ); ?></button>
        </div>
    </div>

    <!-- Lista dinámica de formaciones agregadas -->
    <div id="sop-formacion-list" class="sop-chip-list-spaced">
        <!-- Dinámico vía JS -->
    </div>

    <!-- Zona de subida de certificación -->
    <label for="sop_coach_certification_doc" style="cursor: pointer; display: block; width: 100%;">
        <div class="sop-tab-upload-area" id="sop-coach-doc-upload-area">
            <div style="text-align: center;">
                <i style="opacity: 0.5;">📎</i>
                <span class="sop-prof-file-hint"><?php esc_html_e( 'PDF / JPG', 'sistema-pro' ); ?></span>
            </div>
            
            <div class="sop-tab-warning-box">
                <div class="sop-prof-row-start">
                    <i style="color: #ffab00; font-size: 1.2rem;">⚠️</i>
                    <p style="margin: 0; font-size: 0.85rem; line-height: 1.5; opacity: 0.8;">
                        <?php esc_html_e( 'Adjunta tu título o certificación para su verificación, de no adjuntarlo tu título no aparecerá para los jugadores', 'sistema-pro' ); ?><br><br>
                        <?php esc_html_e( 'Recuerda que tus futuros jugadores necesitan claridad y transparencia verificando que coaches como tu son realmente titulados.', 'sistema-pro' ); ?>
                    </p>
                </div>
            </div>

            <div id="sop-coach-doc-preview-container" style="position: absolute; bottom: 20px; left: 20px; display: <?php echo $coach_doc_id ? 'block' : 'none'; ?>;">
                <div class="sop-tab-badge">
                    <i style="font-size: 1rem;">✓</i>
                    <span id="sop-coach-doc-filename"><?php echo esc_html( $coach_doc_name ); ?></span>
                </div>
                <?php if ( $coach_doc_url ) : ?>
                    <div style="margin-top: 10px; font-size: 0.85rem;">
                        <a href="<?php echo esc_url( $coach_doc_url ); ?>" target="_blank" style="color: #092189; text-decoration: none;"><?php esc_html_e( 'Ver archivo actual', 'sistema-pro' ); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </label>
    <input type="file" id="sop_coach_certification_doc" name="sop_coach_certification_doc" accept=".pdf, image/jpeg, image/jpg, image/png, image/webp" style="display: none;" onchange="sopPreviewCoachDoc(this)">
</div>

<script>
function sopPreviewCoachDoc(input) {
    if (input.files && input.files[0]) {
        var container = document.getElementById('sop-coach-doc-preview-container');
        var filename = document.getElementById('sop-coach-doc-filename');
        if (container && filename) {
            filename.textContent = input.files[0].name;
            container.style.display = 'block';
            document.getElementById('sop-coach-doc-upload-area').style.borderColor = '#092189';
        }
    }
}
</script>
