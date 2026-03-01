<?php
/**
 * Component: Trainer Card
 * Dynamic card for displaying a trainer's profile summary.
 * 
 * Expected vars: 
 * - $trainer (WP_User object)
 * - $name (string) - trainer display name
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! isset( $trainer ) || ! ( $trainer instanceof WP_User ) ) {
    return; // Don't render if user data is missing
}

$user_id = $trainer->ID;
$name    = !empty($trainer->display_name) ? strtoupper($trainer->display_name) : strtoupper($trainer->user_login);

// Fetch Profile Image
$profile_image_id  = get_user_meta( $user_id, 'sop_profile_image_id', true );
$profile_image_url = $profile_image_id ? wp_get_attachment_image_url( $profile_image_id, 'medium' ) : SOP_URL . 'assets/images/no image.png';

// Fetch Professional Data
$ocupacion_id   = get_user_meta( $user_id, 'sop_ocupacion_id', true );
$experiencia_id = get_user_meta( $user_id, 'sop_experiencia_id', true );
$nivel_id       = get_user_meta( $user_id, 'sop_nivel_prof_id', true );
$desc           = get_user_meta( $user_id, 'sop_prof_description', true );
$precio         = get_user_meta( $user_id, 'sop_precio_mensual', true );
// Determine lowest price for "Desde" display
$lowest_pkg_price = null;
$lowest_pkg_qty = 0;

for ($i = 1; $i <= 6; $i++) {
    $qty = get_user_meta( $user_id, 'sop_cantidad_sesiones_' . $i, true );
    $price = get_user_meta( $user_id, 'sop_precio_sesiones_' . $i, true );
    
    if (!empty($qty) && !empty($price) && floatval($price) > 0 && intval($qty) > 0) {
        $p = floatval($price);
        if ($lowest_pkg_price === null || $p < $lowest_pkg_price) {
            $lowest_pkg_price = $p;
            $lowest_pkg_qty = intval($qty);
        }
    }
}

$precio = get_user_meta( $user_id, 'sop_precio_mensual', true );

if ($lowest_pkg_price !== null) {
    $precio_display = $lowest_pkg_price;
    $suffix_display = $lowest_pkg_qty . ' ' . (intval($lowest_pkg_qty) === 1 ? 'sesión' : 'sesiones');
} else {
    $precio_display = !empty($precio) ? $precio : '50';
    $suffix_display = '/ mes';
}

$ocupacion_term   = $ocupacion_id ? get_term( $ocupacion_id ) : null;
$experiencia_term = $experiencia_id ? get_term( $experiencia_id ) : null;
$nivel_term       = $nivel_id ? get_term( $nivel_id ) : null;

$ocupacion_name   = ( $ocupacion_term && ! is_wp_error( $ocupacion_term ) ) ? $ocupacion_term->name : 'COACH';
$experiencia_name = ( $experiencia_term && ! is_wp_error( $experiencia_term ) ) ? $experiencia_term->name : 'N/A';
$nivel_name       = ( $nivel_term && ! is_wp_error( $nivel_term ) ) ? $nivel_term->name : 'N/A';
$short_desc       = ! empty( $desc ) ? wp_trim_words( $desc, 15, '...' ) : 'Sin descripción profesional registrada.';

// Fetch Nationality Flag
$nationality_id   = get_user_meta( $user_id, 'sop_nacionalidad_id', true );
$nationality_term = $nationality_id ? get_term( $nationality_id ) : null;
$nationality_name = ( $nationality_term && ! is_wp_error( $nationality_term ) ) ? $nationality_term->name : '';
$nationality_flag = SOP_UI::get_nationality_flag( $nationality_name );

// Fetch Languages (simplified string for the badge)
$idiomas_data = get_user_meta( $user_id, 'sop_idiomas_data', true ) ?: array();
$idiomas_str  = '';
if ( ! empty( $idiomas_data ) ) {
    $codes = array();
    foreach ( $idiomas_data as $i ) {
        if ( ! empty( $i['lang_name'] ) ) {
            $nameRaw = strtolower( trim( $i['lang_name'] ) );
            $code    = '';
            
            if ( false !== strpos( $nameRaw, 'español' ) || false !== strpos( $nameRaw, 'spanish' ) ) {
                $code = 'ES';
            } elseif ( false !== strpos( $nameRaw, 'inglés' ) || false !== strpos( $nameRaw, 'english' ) || false !== strpos( $nameRaw, 'ingles' ) ) {
                $code = 'EN';
            } elseif ( false !== strpos( $nameRaw, 'italiano' ) || false !== strpos( $nameRaw, 'italian' ) ) {
                $code = 'IT';
            } elseif ( false !== strpos( $nameRaw, 'francés' ) || false !== strpos( $nameRaw, 'french' ) ) {
                $code = 'FR';
            } elseif ( false !== strpos( $nameRaw, 'alemán' ) || false !== strpos( $nameRaw, 'german' ) ) {
                $code = 'DE';
            } elseif ( false !== strpos( $nameRaw, 'portugués' ) || false !== strpos( $nameRaw, 'portuguese' ) ) {
                $code = 'PT';
            } else {
                $code = strtoupper( substr( $nameRaw, 0, 2 ) );
            }
            
            if ( ! empty( $code ) ) {
                $codes[] = $code;
            }
        }
    }
    $idiomas_str = implode( ' - ', array_unique( $codes ) );
}

// Fetch Focus Tags (Positions, Offensive & Defensive Phases)
$posiciones_ids      = get_user_meta( $user_id, 'sop_posiciones_ids', true ) ?: array();
$fases_ofensivas_ids = get_user_meta( $user_id, 'sop_fase_ofensiva_ids', true ) ?: array();
$fases_defensivas_ids = get_user_meta( $user_id, 'sop_fase_defensiva_ids', true ) ?: array();
$focus_names         = array();

// 1. Positions
if ( ! empty( $posiciones_ids ) ) {
    foreach ( array_slice($posiciones_ids, 0, 2) as $pid ) {
        $term = get_term( $pid );
        if ( $term && ! is_wp_error( $term ) ) $focus_names[] = $term->name;
    }
}
// 2. Offensive Phases
if ( count($focus_names) < 4 && ! empty( $fases_ofensivas_ids ) ) {
    foreach ( array_slice($fases_ofensivas_ids, 0, 2) as $foid ) {
        if ( count($focus_names) >= 4 ) break;
        $term = get_term( $foid );
        if ( $term && ! is_wp_error( $term ) ) $focus_names[] = $term->name;
    }
}
// 3. Defensive Phases
if ( count($focus_names) < 4 && ! empty( $fases_defensivas_ids ) ) {
    foreach ( array_slice($fases_defensivas_ids, 0, 2) as $fdid ) {
        if ( count($focus_names) >= 4 ) break;
        $term = get_term( $fdid );
        if ( $term && ! is_wp_error( $term ) ) $focus_names[] = $term->name;
    }
}
?>
<div class="sop-trainer-card">
    <a href="<?php echo esc_url( home_url( '/detalle-entrenador?trainer_id=' . $user_id ) ); ?>" class="sop-tc-link">
    <div class="sop-tc-header">
        <div class="sop-tc-image-wrapper">
            <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="<?php echo esc_attr($name); ?>" class="sop-tc-image">
            <button class="sop-tc-icon-btn">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/ray.png' ); ?>" alt="Ray" class="sop-tc-rayo-img">
            </button>
        </div>
        <div class="sop-tc-header-right">
            <div class="sop-tc-header-top-row">
                <div class="sop-tc-rating">
                    <span class="sop-stars-sm">★★★★★</span> <span class="sop-tc-rating-count">(10)</span>
                </div>
                <button class="sop-tc-menu-btn">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/more_opcions.png' ); ?>" alt="More" class="sop-tc-more-img">
                </button>
            </div>
            <div class="sop-tc-badges">
                <div class="sop-tc-badge-row">
                    <span class="sop-tc-badge-label"><?php esc_html_e( 'Experience', 'sistema-pro' ); ?></span>
                    <div class="sop-tc-badge-box"><?php echo esc_html( $experiencia_name ); ?></div>
                </div>
                <div class="sop-tc-badge-row">
                    <span class="sop-tc-badge-label"><?php esc_html_e( 'Idioma', 'sistema-pro' ); ?></span>
                    <div class="sop-tc-badge-box"><?php echo ! empty( $idiomas_str ) ? esc_html( $idiomas_str ) : 'N/A'; ?></div>
                </div>
                <?php if ( ! empty( $nationality_flag ) ) : ?>
                    <div class="sop-tc-badge-row">
                        <img src="<?php echo esc_url($nationality_flag); ?>" alt="Flag" class="sop-tc-flag-img">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="sop-tc-body">
        <h3 class="sop-tc-name"><?php echo esc_html($name); ?></h3>
        <span class="sop-tc-role"><?php echo esc_html( strtoupper( $ocupacion_name ) ); ?></span>
        <p class="sop-tc-desc"><?php echo esc_html( $short_desc ); ?></p>
        
        <div class="sop-tc-tags-row">
            <span class="sop-tc-tag-label"><?php esc_html_e( 'Nivel', 'sistema-pro' ); ?></span>
            <span class="sop-tc-tag-val sop-tc-tag-nivel"><?php echo esc_html( $nivel_name ); ?></span>
            <span class="sop-tc-tag-label sop-tc-tag-spacer"><?php esc_html_e( 'Cupos', 'sistema-pro' ); ?></span>
            <span class="sop-tc-tag-val sop-tc-tag-cupos">8/10</span>
        </div>

        <?php if ( ! empty( $focus_names ) ) : ?>
            <div class="sop-tc-focus">
                <span class="sop-tc-focus-label"><?php esc_html_e( 'Focus', 'sistema-pro' ); ?></span>
                <div class="sop-tc-focus-tags">
                    <?php 
                    $displayed_focus = array_slice($focus_names, 0, 4);
                    foreach ( $displayed_focus as $fname ) : ?>
                        <span class="sop-tc-focus-tag"><?php echo esc_html( $fname ); ?></span>
                    <?php endforeach; ?>
                    <?php if ( count($focus_names) > 4 ) : ?>
                        <span class="sop-tc-focus-more">...</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="sop-tc-footer">
        <span class="sop-tc-desde"><?php esc_html_e( 'Desde', 'sistema-pro' ); ?></span>
        <div class="sop-tc-price-pill">
            <strong><?php echo esc_html($precio_display); ?>$</strong> <span class="sop-tc-price-suffix"><?php echo esc_html($suffix_display); ?></span>
        </div>
    </div>

    </a>
</div>
