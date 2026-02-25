<?php
/**
 * View: Trainer Detail Page
 * Public facing view of a trainer's profile (re-uses preview tab).
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$trainer_id = isset( $_GET['trainer_id'] ) ? intval( $_GET['trainer_id'] ) : 0;

if ( ! $trainer_id ) {
    echo '<p>' . esc_html__( 'Entrenador no especificado.', 'sistema-pro' ) . '</p>';
    return;
}

$trainer_user = get_userdata( $trainer_id );

if ( ! $trainer_user || is_wp_error( $trainer_user ) ) {
    echo '<p>' . esc_html__( 'Entrenador no encontrado.', 'sistema-pro' ) . '</p>';
    return;
}

// Variables required by preview.php
$sop_preview_user_id = $trainer_id;

// Optional: Wrap in a custom container if we want different spacing
?>
<div class="sop-trainer-detail-page">
    <div style="margin-bottom: 20px;">
        <a href="<?php echo esc_url( home_url( '/entrenadores' ) ); ?>" class="sop-btn sop-btn-secondary" style="display:inline-block; margin-bottom: 10px;">&larr; <?php esc_html_e( 'Volver al directorio', 'sistema-pro' ); ?></a>
    </div>
    
    <?php 
    $preview_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/tabs/preview.php';
    if ( file_exists( $preview_path ) ) {
        require $preview_path;
    } else {
        echo '<p>Template not found.</p>';
    }
    ?>
</div>
