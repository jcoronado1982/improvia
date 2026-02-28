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
    <label for="sop_coach_certification_doc" class="sop-pointer-block-full">
        <div class="sop-tab-upload-area-main">
            <div class="sop-tab-upload-area" id="sop-coach-doc-upload-area">
                <div class="sop-upload-inner-flex">
                    <div class="sop-upload-col-hint">
                        <div class="sop-text-center">
                        <img src="<?php echo esc_url( SOP_URL . 'assets/images/adjuntar.png' ); ?>" class="sop-tab-upload-icon-img" alt="Attach">
                        <span class="sop-upload-hint"><?php esc_html_e( 'PDF / JPG', 'sistema-pro' ); ?></span>
                    </div>
                    </div>
                    
                    <div class="sop-upload-col-warning">
                        <div class="sop-tab-warning-box">
                            <div class="sop-warning-flex-row">
                                <img class="sop-tab-warning-icon" src="<?php echo esc_url( SOP_URL . 'assets/images/alert-circle.png' ); ?>" alt="Alert">
                                <div class="sop-tab-warning-text">
                                    <div>
                                        <div>
                                            <?php esc_html_e( 'Adjunta tu título o certificación para su verificación, de no adjuntarlo tu título no aparecerá para los jugadores', 'sistema-pro' ); ?>
                                        </div>
                                        <div>
                                            <?php esc_html_e( 'Recuerda que tus futuros jugadores necesitan claridad y transparencia verificando que coaches como tu son realmente titulados.', 'sistema-pro' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sop-coach-doc-preview-container" class="sop-preview-container-base" style="display: <?php echo $coach_doc_id ? 'block' : 'none'; ?>;">
                <div class="sop-tab-badge">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="<?php echo esc_url( SOP_URL . 'assets/images/check-circle.png' ); ?>" class="sop-icon-check-img" alt="Check">
                        <a id="sop-coach-doc-link" href="<?php echo esc_url( $coach_doc_url ); ?>" target="_blank" class="sop-link-no-decor" style="color: inherit;">
                            <span id="sop-coach-doc-filename"><?php echo esc_html( $coach_doc_name ); ?></span>
                        </a>
                    </div>
                    <span class="sop-badge-remove" onclick="sopRemoveCoachDoc(event)">✕</span>
                </div>
            </div>
        </div>
    </label>

    <input type="file" id="sop_coach_certification_doc" name="sop_coach_certification_doc" accept=".pdf, image/jpeg, image/jpg, image/png, image/webp" class="sop-hidden-file-input" onchange="sopPreviewCoachDoc(this)">
</div>

<script>
function sopPreviewCoachDoc(input) {
    if (input.files && input.files[0]) {
        var container = document.getElementById('sop-coach-doc-preview-container');
        var filename = document.getElementById('sop-coach-doc-filename');
        if (container && filename) {
            filename.textContent = input.files[0].name;
            container.style.display = 'block';
            document.getElementById('sop-coach-doc-upload-area').style.borderColor = '#ffde00';
        }
    }
}

function sopRemoveCoachDoc(e) {
    e.preventDefault();
    e.stopPropagation();
    var input = document.getElementById('sop_coach_certification_doc');
    var container = document.getElementById('sop-coach-doc-preview-container');
    if (input) input.value = '';
    if (container) container.style.display = 'none';
    document.getElementById('sop-coach-doc-upload-area').style.borderColor = '';
}
</script>
