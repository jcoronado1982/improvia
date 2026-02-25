<?php
/**
 * Componente exclusivo Coach: Ocupación
 * Ocupación actual y Experiencia — Layout compacto como en Figma
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$ocupaciones  = get_terms( array( 'taxonomy' => 'sop_ocupacion', 'hide_empty' => false ) );
$experiencias = get_terms( array( 'taxonomy' => 'sop_experiencia', 'hide_empty' => false ) );

$ocupacion_act   = get_user_meta( $user->ID, 'sop_ocupacion_id', true );
$experiencia_act = get_user_meta( $user->ID, 'sop_experiencia_id', true );
?>

<div class="sop-tab-panel sop-tab-panel-flex-main">
    <h3 class="sop-title-with-line"><?php esc_html_e( 'OCUPACION', 'sistema-pro' ); ?></h3>
    <div class="sop-prof-row">
        <div style="min-width: 120px;">
            <label class="sop-label"><?php esc_html_e( 'Ocupación actual', 'sistema-pro' ); ?></label>
            <select name="sop_ocupacion_id" class="sop-input" style="min-width: 120px;">
                <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                <?php foreach ($ocupaciones as $oc) : ?>
                    <option value="<?php echo $oc->term_id; ?>" <?php selected($ocupacion_act, $oc->term_id); ?>><?php echo esc_html($oc->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="min-width: 120px;">
            <label class="sop-label"><?php esc_html_e( 'Experiencia', 'sistema-pro' ); ?></label>
            <select name="sop_experiencia_id" class="sop-input" style="min-width: 120px;">
                <option value=""><?php esc_html_e( 'Seleccionar', 'sistema-pro' ); ?></option>
                <?php foreach ($experiencias as $exp) : ?>
                    <option value="<?php echo $exp->term_id; ?>" <?php selected($experiencia_act, $exp->term_id); ?>><?php echo esc_html($exp->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
