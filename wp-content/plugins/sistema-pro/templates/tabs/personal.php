<?php
/**
 * Template para la pestaña de Información Personal
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$user = wp_get_current_user();
$idiomas = get_terms( array( 'taxonomy' => 'sop_idioma', 'hide_empty' => false ) );
$niveles = get_terms( array( 'taxonomy' => 'sop_nivel', 'hide_empty' => false ) );
$nacionalidades = get_terms( array( 'taxonomy' => 'sop_nacionalidad', 'hide_empty' => false ) );
$ubicaciones = get_terms( array( 'taxonomy' => 'sop_ubicacion', 'hide_empty' => false ) );

$full_name = !empty($user->display_name) ? $user->display_name : '';
$nacionalidad_act = get_user_meta( $user->ID, 'sop_nacionalidad_id', true );
$ubicacion_act = get_user_meta( $user->ID, 'sop_ubicacion_id', true );
$nacimiento = get_user_meta( $user->ID, 'sop_fecha_nacimiento', true );
?>

<form id="sop-profile-form" enctype="multipart/form-data">
    <?php wp_nonce_field( 'sop_profile_nonce', 'nonce' ); ?>
    
    <div class="sop-tab-panel">
        <h3 class="sop-title-with-line"><?php esc_html_e( 'ABOUT ME', 'sistema-pro' ); ?></h3>
    <div class="sop-tab-split">
<?php
        $profile_image_id = get_user_meta( $user->ID, 'sop_profile_image_id', true );
        $profile_image_url = $profile_image_id ? wp_get_attachment_image_url( $profile_image_id, 'thumbnail' ) : '';
        ?>
        <div style="text-align: center; position: relative; flex: 0 0 160px;">
            <label for="sop_profile_picture" style="cursor: pointer; display: block;">
                <div class="sop-profile-img-upload" style="overflow: hidden; position: relative; width: 140px; height: 140px; border-radius: 50%; margin: 0 auto; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <?php if ( $profile_image_url ) : ?>
                        <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;" id="sop-profile-img-preview">
                    <?php else : ?>
                        <i style="opacity: 0.5;" id="sop-profile-img-icon">📷</i>
                        <img src="" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; display: none;" id="sop-profile-img-preview">
                    <?php endif; ?>
                </div>
                <p style="font-size: 0.85rem; margin-top: 15px; cursor: pointer;"><?php esc_html_e( 'Upload image', 'sistema-pro' ); ?></p>
            </label>
            <input type="file" id="sop_profile_picture" name="sop_profile_picture" accept="image/jpeg, image/png, image/webp" style="display: none;" onchange="sopPreviewImage(this)">
        </div>

        <div style="flex: 1; min-width: 300px;">
            <div class="sop-tab-grid-4">
                <div style="grid-column: span 2;">
                    <label class="sop-label"><?php esc_html_e( 'Nombre completo', 'sistema-pro' ); ?></label>
                    <input type="text" name="display_name" value="<?php echo esc_attr($full_name); ?>" class="sop-input" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                </div>
                <div style="grid-column: span 1;">
                    <label class="sop-label"><?php esc_html_e( 'Ubicación', 'sistema-pro' ); ?></label>
                    <select name="sop_ubicacion_id" class="sop-input">
                        <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                        <?php foreach ($ubicaciones as $ub) : ?>
                            <option value="<?php echo $ub->term_id; ?>" <?php selected($ubicacion_act, $ub->term_id); ?>>
                                <?php echo esc_html($ub->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="grid-column: span 1;">
                    <label class="sop-label"><?php esc_html_e( 'Nacionalidad', 'sistema-pro' ); ?></label>
                    <select name="sop_nacionalidad_id" class="sop-input">
                        <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                        <?php foreach ($nacionalidades as $nac) : ?>
                            <option value="<?php echo $nac->term_id; ?>" <?php selected($nacionalidad_act, $nac->term_id); ?>>
                                <?php echo esc_html($nac->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="grid-column: span 1;">
                    <label class="sop-label"><?php esc_html_e( 'Nacimiento', 'sistema-pro' ); ?></label>
                    <input type="date" name="sop_fecha_nacimiento" value="<?php echo esc_attr($nacimiento); ?>" class="sop-input">
                </div>
            </div>

            <div class="sop-tab-nested-box">
                <h4 class="sop-tab-nested-title"><?php esc_html_e( 'Idiomas que manejo', 'sistema-pro' ); ?></h4>
                <div class="sop-tab-inline-form">
                    <div style="flex: 1; max-width: 200px;">
                        <label class="sop-label"><?php esc_html_e( 'Lenguaje', 'sistema-pro' ); ?></label>
                        <select id="new-lang-id" class="sop-input">
                            <?php foreach ($idiomas as $i) : ?>
                                <option value="<?php echo $i->term_id; ?>"><?php echo esc_html($i->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex: 1; max-width: 200px;">
                        <label class="sop-label"><?php esc_html_e( 'Nivel', 'sistema-pro' ); ?></label>
                        <select id="new-lang-level" class="sop-input">
                            <?php foreach ($niveles as $niv) : ?>
                                <option value="<?php echo $niv->term_id; ?>"><?php echo esc_html($niv->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" id="sop-add-lang" class="sop-btn-blue" style="width: auto; min-width: 100px;"><?php esc_html_e( 'Agregar', 'sistema-pro' ); ?></button>
                </div>

                <div id="sop-languages-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <!-- Dinámico vía JS -->
                </div>
            </div>
        </div>
    </div> <!-- Close flex container -->
</div> <!-- End ABOUT ME panel -->

    <div class="sop-tab-footer">
        <span id="sop-profile-msg" class="sop-tab-msg"></span>
        <button type="submit" class="sop-btn-white"><?php esc_html_e( 'Guardar Cambios', 'sistema-pro' ); ?></button>
    </div>
</form>

<script>
function sopPreviewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var icon = document.getElementById('sop-profile-img-icon');
            if (icon) icon.style.display = 'none';
            var preview = document.getElementById('sop-profile-img-preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
