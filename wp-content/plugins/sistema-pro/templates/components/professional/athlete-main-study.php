<?php
/**
 * Componente exclusivo Atleta: Estudio Principal (Subida de documentos)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$prof_doc_id   = get_user_meta( $user->ID, 'sop_professional_document_id', true );
$prof_doc_url  = $prof_doc_id ? wp_get_attachment_url( $prof_doc_id ) : '';
$prof_doc_name = $prof_doc_id ? basename( get_attached_file( $prof_doc_id ) ) : '';
?>

<div class="sop-tab-panel sop-tab-panel-basic">
    <h3 class="sop-title-with-line"><?php esc_html_e( 'ESTUDIO PRINCIPAL', 'sistema-pro' ); ?></h3>
    <label for="sop_professional_document" class="sop-pointer-block-full">
        <div class="sop-tab-upload-area-main"> 
             <div class="sop-tab-upload-area" id="sop-doc-upload-area">
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
                                            <?php esc_html_e( 'Adjunta tu título o certificación para su verificación, de no adjuntarlo tu título no aparecerá para los Entrenadores', 'sistema-pro' ); ?>
                                        </div>
                                        <div>
                                            <?php esc_html_e( 'Recuerda que los entrenadores necesitan claridad y transparencia verificando que jugadores como tu son realmente titulados y puedan ofrecerte la ayuda que necesitas', 'sistema-pro' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
              <div>
             <div id="sop-doc-preview-container" class="sop-preview-container-base" style="display: <?php echo $prof_doc_id ? 'block' : 'none'; ?>;">
        <div class="sop-tab-badge">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/check-circle.png' ); ?>" class="sop-icon-check-img" alt="Check">
                <a id="sop-doc-link" href="<?php echo esc_url( $prof_doc_url ); ?>" target="_blank" class="sop-link-no-decor" style="color: inherit;">
                    <span id="sop-doc-filename"><?php echo esc_html( $prof_doc_name ); ?></span>
                </a>
            </div>
            <span class="sop-badge-remove" onclick="sopRemoveDocument(event)">✕</span>
        </div>
    </div>
        </div>
        </div>
       
    </label>

   
    <input type="file" id="sop_professional_document" name="sop_professional_document" accept=".pdf, image/jpeg, image/jpg, image/png, image/webp" class="sop-hidden-file-input" onchange="sopPreviewDocument(this)">

</div>

<script>
function sopPreviewDocument(input) {
    if (input.files && input.files[0]) {
        var container = document.getElementById('sop-doc-preview-container');
        var filename = document.getElementById('sop-doc-filename');
        if (container && filename) {
            filename.textContent = input.files[0].name;
            container.style.display = 'block';
            document.getElementById('sop-doc-upload-area').style.borderColor = '#ffde00';
        }
    }
}

function sopRemoveDocument(e) {
    e.preventDefault();
    e.stopPropagation();
    var input = document.getElementById('sop_professional_document');
    var container = document.getElementById('sop-doc-preview-container');
    if (input) input.value = '';
    if (container) container.style.display = 'none';
    document.getElementById('sop-doc-upload-area').style.borderColor = '';
}
</script>
