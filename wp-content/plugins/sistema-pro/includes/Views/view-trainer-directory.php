<?php
/**
 * View: Trainer Directory
 * Assembles modular components: Filter Bar, Trainer Cards, Pricing Cards, and Paginator.
 * 
 * Expected vars: $trainers (array of WP_User objects)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$components_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/components/';

// --- Filter Bar ---
$total_results = count( $trainers );
$total_label   = 'totales';
include $components_path . 'filter-bar.php';
?>

<div class="sop-trainer-grid">
    <?php if ( ! empty( $trainers ) ) : ?>
        <?php 
        $card_index = 0;
        foreach ( $trainers as $trainer ) : 
            $name = !empty($trainer->display_name) ? strtoupper($trainer->display_name) : strtoupper($trainer->user_login);
            $card_index++;

            // --- Trainer Card Component ---
            if ( $card_index === 3 ) {
                // Print the PRICING CARD strictly as the 3rd element (index 2)
                include $components_path . 'pricing-card.php';
            }

            include $components_path . 'trainer-card.php';

        endforeach; ?>
    <?php else : ?>
        <p>No se encontraron entrenadores registrados en este momento.</p>
    <?php endif; ?>
</div>

<?php
// --- Paginator ---
include $components_path . 'paginator.php';
?>
