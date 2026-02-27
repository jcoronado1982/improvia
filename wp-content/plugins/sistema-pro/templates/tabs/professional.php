<?php
/**
 * Template para la pestaña de Información Profesional
 * Actúa como "Router de componentes" basado en el rol del usuario
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$user = wp_get_current_user();
$is_provider = current_user_can( 'entrenador' ) || current_user_can( 'especialista' );

$components_path = SOP_PATH . 'templates/components/professional/';
?>

<div>
    <?php wp_nonce_field( 'sop_profile_nonce', 'nonce' ); ?>
    <input type="hidden" name="sop_form_section" value="professional">

    <?php if ( $is_provider ) : ?>
        <!-- Provider: Ocupación arriba, Descripción debajo -->
        <div class="sop-prof-stack">
            <?php include $components_path . 'coach-ocupacion.php'; ?>
            <?php include $components_path . 'prof-description.php'; ?>
        </div>
    <?php else : ?>
        <!-- Atleta: Info básica + Descripción lado a lado -->
        <div class="sop-tab-split-md">
            <?php include $components_path . 'athlete-basic-info.php'; ?>
            <?php include $components_path . 'prof-description.php'; ?>
        </div>
    <?php endif; ?>

    <!-- Secciones adicionales según rol -->
    <?php if ( $is_provider ) : ?>
        <?php include $components_path . 'coach-formacion-reglada.php'; ?>
        <?php include $components_path . 'coach-estudios-secundarios.php'; ?>
        <?php include $components_path . 'coach-posiciones.php'; ?>
    <?php else : ?>
        <?php include $components_path . 'athlete-main-study.php'; ?>
    <?php endif; ?>

    <!-- Componente compartido: RRSS -->
    <?php include $components_path . 'prof-rrss.php'; ?>
</div>
