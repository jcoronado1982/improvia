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
        <div style="flex: 1; min-width: 200px;">
            <label class="sop-label"><?php esc_html_e( 'Ocupación actual', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></label>
            <select name="sop_ocupacion_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>" required>
                <option value=""></option>
                <?php foreach ($ocupaciones as $oc) : ?>
                    <option value="<?php echo $oc->term_id; ?>" <?php selected($ocupacion_act, $oc->term_id); ?>><?php echo esc_html($oc->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label class="sop-label"><?php esc_html_e( 'Experiencia', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></label>
            <select name="sop_experiencia_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>" required>
                <option value=""></option>
                <?php foreach ($experiencias as $exp) : ?>
                    <option value="<?php echo $exp->term_id; ?>" <?php selected($experiencia_act, $exp->term_id); ?>><?php echo esc_html($exp->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
