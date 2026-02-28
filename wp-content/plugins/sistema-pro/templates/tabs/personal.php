<?php
/**
 * Template para la pestaña de Información Personal
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$user = wp_get_current_user();
$idiomas = get_terms( array( 'taxonomy' => 'sop_idioma', 'hide_empty' => false ) );
$niveles = get_terms( array( 'taxonomy' => 'sop_nivel', 'hide_empty' => false ) );

// Sort $idiomas to put Español first
usort($idiomas, function($a, $b) {
    if (strtolower($a->name) === 'español') return -1;
    if (strtolower($b->name) === 'español') return 1;
    return strcasecmp($a->name, $b->name);
});

// Sort $niveles to put Nativo first
usort($niveles, function($a, $b) {
    if (strtolower($a->name) === 'nativo') return -1;
    if (strtolower($b->name) === 'nativo') return 1;
    return strcasecmp($a->name, $b->name);
});

$nacionalidades = get_terms( array( 'taxonomy' => 'sop_nacionalidad', 'hide_empty' => false ) );
$ubicaciones = get_terms( array( 'taxonomy' => 'sop_ubicacion', 'hide_empty' => false ) );

// --- Sorting $nacionalidades ---
// Order: España > Europa > Sudamérica > Rest
$europe_nacs = array('Alemania', 'Francia', 'Italia', 'Portugal', 'Reino Unido', 'Países Bajos', 'Bélgica', 'Suiza', 'Austria', 'Suecia', 'Noruega', 'Dinamarca', 'Finlandia', 'Polonia', 'Croacia', 'Serbia', 'Grecia', 'Turquía', 'Rumania', 'Ucrania', 'República Checa', 'Hungría', 'Irlanda', 'Escocia');
$south_america_nacs = array('Argentina', 'Brasil', 'Colombia', 'Chile', 'Uruguay', 'Perú', 'Ecuador', 'Venezuela', 'Paraguay');

usort($nacionalidades, function($a, $b) use ($europe_nacs, $south_america_nacs) {
    $get_prio = function($name) use ($europe_nacs, $south_america_nacs) {
        if ($name === 'España') return 1;
        if (in_array($name, $europe_nacs)) return 2;
        if (in_array($name, $south_america_nacs)) return 3;
        return 4;
    };
    $pa = $get_prio($a->name);
    $pb = $get_prio($b->name);
    if ($pa !== $pb) return $pa - $pb;
    return strcasecmp($a->name, $b->name);
});

// --- Sorting $ubicaciones ---
// Order: Madrid > Barcelona > Rest of Spain > Europa > Latam > Rest
$other_spain_ubic = array('A Coruña', 'Álava / Vitoria', 'Albacete', 'Alicante', 'Almería', 'Ávila', 'Badajoz', 'Bilbao', 'Burgos', 'Cáceres', 'Cádiz', 'Castellón de la Plana', 'Ceuta', 'Ciudad Real', 'Córdoba', 'Cuenca', 'Girona', 'Granada', 'Guadalajara', 'Huelva', 'Huesca', 'Jaén', 'Las Palmas de Gran Canaria', 'León', 'Lleida', 'Logroño', 'Lugo', 'Málaga', 'Melilla', 'Murcia', 'Ourense', 'Oviedo', 'Palencia', 'Palma de Mallorca', 'Pamplona', 'Pontevedra', 'Salamanca', 'San Sebastián', 'Santa Cruz de Tenerife', 'Santander', 'Segovia', 'Sevilla', 'Soria', 'Tarragona', 'Teruel', 'Toledo', 'Valencia', 'Valladolid', 'Vigo', 'Zamora', 'Zaragoza', 'Gijón', 'Elche', 'Cartagena', 'Jerez de la Frontera', 'Marbella', 'Getafe', 'Leganés', 'Alcorcón', 'Cornellà', 'Hospitalet de Llobregat');
$europe_ubic = array('Londres', 'Mánchester', 'Liverpool', 'París', 'Lyon', 'Marsella', 'Berlín', 'Múnich', 'Dortmund', 'Roma', 'Milán', 'Turín', 'Nápoles', 'Lisboa', 'Oporto', 'Ámsterdam', 'Róterdam', 'Bruselas', 'Viena', 'Zúrich', 'Estocolmo', 'Copenhague', 'Oslo', 'Varsovia', 'Praga', 'Budapest', 'Atenas', 'Estambul', 'Zagreb', 'Belgrado');
$latam_ubic = array('Buenos Aires', 'Ciudad de México', 'Bogotá', 'Santiago', 'Lima', 'Montevideo', 'São Paulo');

usort($ubicaciones, function($a, $b) use ($other_spain_ubic, $europe_ubic, $latam_ubic) {
    $get_prio = function($name) use ($other_spain_ubic, $europe_ubic, $latam_ubic) {
        if ($name === 'Madrid') return 1;
        if ($name === 'Barcelona') return 2;
        if (in_array($name, $other_spain_ubic)) return 3;
        if (in_array($name, $europe_ubic)) return 4;
        if (in_array($name, $latam_ubic)) return 5;
        return 6;
    };
    $pa = $get_prio($a->name);
    $pb = $get_prio($b->name);
    if ($pa !== $pb) return $pa - $pb;
    return strcasecmp($a->name, $b->name);
});

$full_name = !empty($user->display_name) ? $user->display_name : '';
$nacionalidad_act = get_user_meta( $user->ID, 'sop_nacionalidad_id', true );
$ubicacion_act = get_user_meta( $user->ID, 'sop_ubicacion_id', true );
$nacimiento = get_user_meta( $user->ID, 'sop_fecha_nacimiento', true );
?>

<form id="sop-profile-form" enctype="multipart/form-data">
    <?php wp_nonce_field( 'sop_profile_nonce', 'nonce' ); ?>
    <input type="hidden" name="sop_form_section" value="personal">
    
    <div class="sop-tab-panel">
        <h3 class="sop-title-with-line"><?php esc_html_e( 'ABOUT ME', 'sistema-pro' ); ?></h3>
    <div class="sop-tab-split">
<?php
        $profile_image_id = get_user_meta( $user->ID, 'sop_profile_image_id', true );
        $profile_image_url = $profile_image_id ? wp_get_attachment_image_url( $profile_image_id, 'medium' ) : SOP_URL . 'assets/images/no image.png';
        ?>
        <div class="sop-tab-profile-col-left">
            <label for="sop_profile_picture" class="sop-profile-picture-label">
                <div class="sop-profile-img-upload">
                    <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="Profile Picture" class="sop-profile-img-preview" id="sop-profile-img-preview">
                </div>
                <p class="sop-profile-upload-text"><?php esc_html_e( 'Upload image', 'sistema-pro' ); ?></p>
            </label>
            <input type="file" id="sop_profile_picture" name="sop_profile_picture" accept="image/jpeg, image/png, image/webp" class="sop-hidden-file-input" onchange="sopPreviewImage(this)">
        </div>

        <div class="sop-tab-profile-col-right">
            <div class="sop-tab-grid-4">
                <div class="sop-col-span-2">
                    <label class="sop-label"><?php esc_html_e( 'Nombre completo', 'sistema-pro' ); ?> <span class="sop-required-asterisk">*</span></label>
                    <input type="text" name="display_name" value="<?php echo esc_attr($full_name); ?>" class="sop-input" placeholder="<?php esc_attr_e( 'Ej. Juan Pérez', 'sistema-pro' ); ?>" required>
                </div>
                <div class="sop-col-span-1">
                    <label class="sop-label"><?php esc_html_e( 'Ubicación', 'sistema-pro' ); ?> <span class="sop-required-asterisk">*</span></label>
                    <select name="sop_ubicacion_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>" required>
                        <option value=""></option>
                        <?php foreach ($ubicaciones as $ub) : ?>
                            <option value="<?php echo $ub->term_id; ?>" <?php selected($ubicacion_act, $ub->term_id); ?>>
                                <?php echo esc_html($ub->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sop-col-span-1">
                    <label class="sop-label"><?php esc_html_e( 'Nacionalidad', 'sistema-pro' ); ?> <span class="sop-required-asterisk">*</span></label>
                    <select name="sop_nacionalidad_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>" required>
                        <option value=""></option>
                        <?php foreach ($nacionalidades as $nac) : ?>
                            <option value="<?php echo $nac->term_id; ?>" <?php selected($nacionalidad_act, $nac->term_id); ?>>
                                <?php echo esc_html($nac->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sop-col-span-1">
                    <label class="sop-label"><?php esc_html_e( 'Nacimiento', 'sistema-pro' ); ?> <span class="sop-required-asterisk">*</span></label>
                    <input type="text" name="sop_fecha_nacimiento" value="<?php echo esc_attr($nacimiento); ?>" class="sop-input sop-datepicker" required>
                </div>
            </div>

            <div class="sop-tab-nested-box">
                <h4 class="sop-tab-nested-title"><?php esc_html_e( 'Idiomas que manejo', 'sistema-pro' ); ?> <span class="sop-required-asterisk">*</span></h4>
                <div class="sop-tab-inline-form">
                    <div class="sop-language-field-col">
                        <label class="sop-label"><?php esc_html_e( 'Lenguaje', 'sistema-pro' ); ?></label>
                        <select id="new-lang-id" class="sop-input">
                            <?php foreach ($idiomas as $i) : ?>
                                <option value="<?php echo $i->term_id; ?>"><?php echo esc_html($i->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sop-language-field-col">
                        <label class="sop-label"><?php esc_html_e( 'Nivel', 'sistema-pro' ); ?></label>
                        <select id="new-lang-level" class="sop-input">
                            <?php foreach ($niveles as $niv) : ?>
                                <option value="<?php echo $niv->term_id; ?>"><?php echo esc_html($niv->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" id="sop-add-lang" class="sop-btn-blue sop-btn-auto-width"><?php esc_html_e( 'Agregar', 'sistema-pro' ); ?></button>
                </div>

                <div id="sop-languages-list" class="sop-languages-list-container">
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
