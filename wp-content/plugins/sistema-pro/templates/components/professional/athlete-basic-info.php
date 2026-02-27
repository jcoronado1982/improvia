<?php
/**
 * Componente exclusivo Atleta: Información básica deportiva
 * Pierna dominante, Altura, Peso, Nivel
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$piernas     = get_terms( array( 'taxonomy' => 'sop_pierna', 'hide_empty' => false ) );
$alturas     = get_terms( array( 'taxonomy' => 'sop_altura', 'hide_empty' => false ) );
$pesos       = get_terms( array( 'taxonomy' => 'sop_peso', 'hide_empty' => false ) );
$niveles_prof = get_terms( array( 'taxonomy' => 'sop_nivel_prof', 'hide_empty' => false ) );

$pierna_act    = get_user_meta( $user->ID, 'sop_pierna_id', true );
$altura_act    = get_user_meta( $user->ID, 'sop_altura_id', true );
$peso_act      = get_user_meta( $user->ID, 'sop_peso_id', true );
$nivel_prof_act = get_user_meta( $user->ID, 'sop_nivel_prof_id', true );
$categorias     = get_terms( array( 'taxonomy' => 'sop_categoria', 'hide_empty' => false ) );
$categoria_act  = get_user_meta( $user->ID, 'sop_categoria_id', true );
?>

<div class="sop-tab-panel sop-tab-panel-flex-main">
    <h3 class="sop-title-with-line"><?php esc_html_e( 'INFORMACIÓN PARA MI ESPECIALISTA', 'sistema-pro' ); ?></h3>
    <div class="sop-tab-grid-4-sm">
        <div>
            <label class="sop-label"><?php esc_html_e( 'Pierna dominante', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></label>
            <select name="sop_pierna_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                <option value=""></option>
                <?php foreach ($piernas as $p) : ?>
                    <option value="<?php echo $p->term_id; ?>" <?php selected($pierna_act, $p->term_id); ?>><?php echo esc_html($p->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="sop-label"><?php esc_html_e( 'Altura (cm)', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></label>
            <select name="sop_altura_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                <option value=""></option>
                <?php foreach ($alturas as $a) : ?>
                    <option value="<?php echo $a->term_id; ?>" <?php selected($altura_act, $a->term_id); ?>><?php echo esc_html($a->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="sop-label"><?php esc_html_e( 'Peso', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></label>
            <select name="sop_peso_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                <option value=""></option>
                <?php foreach ($pesos as $pes) : ?>
                    <option value="<?php echo $pes->term_id; ?>" <?php selected($peso_act, $pes->term_id); ?>><?php echo esc_html($pes->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="sop-label"><?php esc_html_e( 'Categoría', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></label>
            <select name="sop_categoria_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                <option value=""></option>
                <?php foreach ($categorias as $cat) : ?>
                    <option value="<?php echo $cat->term_id; ?>" <?php selected($categoria_act, $cat->term_id); ?>><?php echo esc_html($cat->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="grid-column: span 1;">
            <label class="sop-label"><?php esc_html_e( 'Nivel', 'sistema-pro' ); ?> <span style="color: #ff4b4b;">*</span></label>
            <select name="sop_nivel_prof_id" class="sop-input sop-tom-select" placeholder="<?php esc_attr_e( 'Seleccionar', 'sistema-pro' ); ?>">
                <option value=""></option>
                <?php foreach ($niveles_prof as $np) : ?>
                    <option value="<?php echo $np->term_id; ?>" <?php selected($nivel_prof_act, $np->term_id); ?>><?php echo esc_html($np->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
