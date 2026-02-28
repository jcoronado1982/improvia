<?php
/**
 * Vista para el Layout Genérico (25/75 Grid)
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="sop-cols-container">
    <div class="sop-col-left">
        <?php echo $menu; ?>
    </div>
    <div class="sop-col-right">
        <?php echo do_shortcode( $content ); ?>
    </div>
</div>
