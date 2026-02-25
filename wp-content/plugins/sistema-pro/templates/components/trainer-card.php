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
$profile_image_url = $profile_image_id ? wp_get_attachment_image_url( $profile_image_id, 'medium' ) : SOP_URL . 'assets/images/coach.png';

// Fetch Professional Data
$ocupacion_id   = get_user_meta( $user_id, 'sop_ocupacion_id', true );
$experiencia_id = get_user_meta( $user_id, 'sop_experiencia_id', true );
$nivel_id       = get_user_meta( $user_id, 'sop_nivel_prof_id', true );
$desc           = get_user_meta( $user_id, 'sop_prof_description', true );
$precio         = get_user_meta( $user_id, 'sop_precio_mensual', true );
$p_sesiones     = get_user_meta( $user_id, 'sop_precio_sesiones', true );
$c_sesiones     = get_user_meta( $user_id, 'sop_cantidad_sesiones', true );

$is_sesiones = !empty($p_sesiones) && floatval($p_sesiones) > 0 && !empty($c_sesiones) && intval($c_sesiones) > 0;

if ($is_sesiones) {
    $precio_display = floatval($p_sesiones);
    $suffix_display = intval($c_sesiones) . ' sesiones';
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
        if ( ! empty( $i['lang'] ) ) {
            $codes[] = strtolower( substr( $i['lang'], 0, 2 ) );
        }
    }
    $idiomas_str = implode( '-', array_unique( $codes ) );
}

// Fetch Focus Tags (Positions & Phases)
$posiciones_ids      = get_user_meta( $user_id, 'sop_posiciones_ids', true ) ?: array();
$fases_ofensivas_ids = get_user_meta( $user_id, 'sop_fase_ofensiva_ids', true ) ?: array();
$focus_names         = array();

if ( ! empty( $posiciones_ids ) ) {
    foreach ( array_slice($posiciones_ids, 0, 3) as $pid ) {
        $term = get_term( $pid );
        if ( $term && ! is_wp_error( $term ) ) $focus_names[] = $term->name;
    }
}
if ( count($focus_names) < 4 && ! empty( $fases_ofensivas_ids ) ) {
    foreach ( array_slice($fases_ofensivas_ids, 0, 2) as $foid ) {
        $term = get_term( $foid );
        if ( $term && ! is_wp_error( $term ) ) $focus_names[] = $term->name;
    }
}
?>
<div class="sop-trainer-card">
    <a href="<?php echo esc_url( home_url( '/detalle-entrenador?trainer_id=' . $user_id ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; flex:1;">
    <div class="sop-tc-header">
        <div class="sop-tc-image-wrapper">
            <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="<?php echo esc_attr($name); ?>" class="sop-tc-image">
            <button class="sop-tc-icon-btn">⚡</button>
        </div>
        <div class="sop-tc-header-right">
            <div class="sop-tc-header-top-row">
                <button class="sop-tc-menu-btn">⋮</button>
            </div>
            <div class="sop-tc-badges">
                <div class="sop-tc-rating" style="margin-bottom: 5px;">
                    <span class="sop-stars-sm">★★★★★</span> <span class="sop-tc-rating-count">(10)</span>
                </div>
                <div class="sop-tc-badge-row">
                    <span class="sop-tc-badge-label"><?php esc_html_e( 'Experience', 'sistema-pro' ); ?></span>
                    <div class="sop-tc-badge-box"><?php echo esc_html( $experiencia_name ); ?></div>
                </div>
                <?php if ( ! empty( $idiomas_str ) ) : ?>
                    <div class="sop-tc-badge-row">
                        <span class="sop-tc-badge-label"><?php esc_html_e( 'Idioma', 'sistema-pro' ); ?></span>
                        <div class="sop-tc-badge-box"><?php echo esc_html( $idiomas_str ); ?></div>
                    </div>
                <?php endif; ?>
                <?php if ( ! empty( $nationality_flag ) ) : ?>
                    <div class="sop-tc-badge-row">
                        <span class="sop-tc-flag-icon" style="font-size: 1.2rem; line-height: 1;"><?php echo $nationality_flag; ?></span>
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
            <span class="sop-tc-tag-val"><?php echo esc_html( $nivel_name ); ?></span>
            <span class="sop-tc-tag-label" style="margin-left: 10px;"><?php esc_html_e( 'Cupos', 'sistema-pro' ); ?></span>
            <span class="sop-tc-tag-val sop-tc-tag-cupos">8/10</span>
        </div>

        <?php if ( ! empty( $focus_names ) ) : ?>
            <div class="sop-tc-focus">
                <span class="sop-tc-focus-label"><?php esc_html_e( 'Focus', 'sistema-pro' ); ?></span>
                <div class="sop-tc-focus-tags">
                    <?php 
                    $displayed_focus = array_slice($focus_names, 0, 3);
                    foreach ( $displayed_focus as $fname ) : ?>
                        <span class="sop-tc-focus-tag"><?php echo esc_html( $fname ); ?></span>
                    <?php endforeach; ?>
                    <?php if ( count($focus_names) > 3 ) : ?>
                        <span class="sop-tc-focus-more">...</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="sop-tc-footer" style="padding: 15px; background: rgba(255, 255, 255, 0.03); border-top: 1px solid rgba(255, 255, 255, 0.05); border-radius: 0 0 12px 12px;">
        <span class="sop-tc-desde" style="color:var(--sop-primary, #00f0ff); font-weight:bold;"><?php esc_html_e( 'Desde', 'sistema-pro' ); ?></span>
        <div class="sop-tc-price-pill" style="float:right;">
            <strong><?php echo esc_html($precio_display); ?>$</strong> <span style="font-size: 0.8em;"><?php echo esc_html($suffix_display); ?></span>
        </div>
        <div style="clear:both;"></div>
    </div>

    </a>
</div>
