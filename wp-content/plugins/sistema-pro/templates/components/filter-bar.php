<?php
/**
 * Component: Filter Bar for Directory Views
 * Reusable top filter bar with results count, search, and filter dropdowns.
 * 
 * Expected vars: $total_results (int), $total_trainers (int), $total_label (string)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$total_results  = isset($total_results) ? $total_results : 0;
$total_trainers = isset($total_trainers) ? $total_trainers : 0;
$total_label    = isset($total_label) ? $total_label : 'totales';
?>
<div class="sop-td-topbar">
    <div class="sop-td-results">
        <span><?php echo esc_html( $total_results ); ?> Resultados de <?php echo esc_html( $total_trainers ); ?> <?php echo esc_html( $total_label ); ?></span>
        <button class="sop-td-filter-btn sop-td-fav-btn">
            <img src="<?php echo esc_url( SOP_URL . 'assets/images/1.png' ); ?>" alt="Fav" class="sop-td-fav-icon">
            Favoritos
        </button>
    </div>
    <div class="sop-td-filters">
        <button class="sop-td-filter-btn">
            Deporte 
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div class="sop-td-search">
            <svg class="sop-td-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <span>Nombre</span>
        </div>
        <button class="sop-td-filter-btn">
            Filtrar 
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        </button>
        <button class="sop-td-filter-btn">
            Ordenar por 
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>
    </div>
</div>
