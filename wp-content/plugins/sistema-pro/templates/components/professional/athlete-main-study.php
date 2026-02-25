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
    <label for="sop_professional_document" style="cursor: pointer; display: block; width: 100%;">
        <div class="sop-tab-upload-area" id="sop-doc-upload-area">
            <div style="text-align: center;">
                <i style="opacity: 0.5;">📎</i>
                <span style="font-size: 1rem; opacity: 0.7; margin-left: 10px;"><?php esc_html_e( 'PDF / JPG', 'sistema-pro' ); ?></span>
            </div>
            
            <div class="sop-tab-warning-box">
                <div style="display: flex; gap: 15px; align-items: flex-start;">
                    <i style="color: #ffab00; font-size: 1.2rem;">⚠️</i>
                    <p style="margin: 0; font-size: 0.85rem; line-height: 1.5; opacity: 0.8; color: #fff;">
                        <?php esc_html_e( 'Adjunta tu título o certificación para su verificación, de no adjuntarlo tu título no aparecerá para los Entrenadores', 'sistema-pro' ); ?><br><br>
                        <?php esc_html_e( 'Recuerda que los entrenadores necesitan claridad y transparencia verificando que jugadores como tu son realmente titulados y puedan ofrecerte la ayuda que necesitas', 'sistema-pro' ); ?>
                    </p>
                </div>
            </div>

            <div id="sop-doc-preview-container" style="position: absolute; bottom: 20px; left: 20px; display: <?php echo $prof_doc_id ? 'block' : 'none'; ?>;">
                <div class="sop-tab-badge">
                    <i style="color: #fff; font-size: 1rem;">✓</i>
                    <span id="sop-doc-filename"><?php echo esc_html( $prof_doc_name ); ?></span>
                </div>
                <?php if ( $prof_doc_url ) : ?>
                    <div style="margin-top: 10px; font-size: 0.85rem;">
                        <a href="<?php echo esc_url( $prof_doc_url ); ?>" target="_blank" style="color: #ffde00; text-decoration: none;"><?php esc_html_e( 'Ver archivo actual', 'sistema-pro' ); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </label>
    <input type="file" id="sop_professional_document" name="sop_professional_document" accept=".pdf, image/jpeg, image/jpg, image/png, image/webp" style="display: none;" onchange="sopPreviewDocument(this)">
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
</script>
