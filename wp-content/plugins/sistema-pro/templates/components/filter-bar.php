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
        <button class="sop-td-filter-btn sop-td-fav-btn">★ Favoritos</button>
    </div>
    <div class="sop-td-filters">
        <button class="sop-td-filter-btn">Deporte ▾</button>
        <div class="sop-td-search">
            <span>🔍</span>
            <span>Nombre</span>
        </div>
        <button class="sop-td-filter-btn">Filtrar ▾</button>
        <button class="sop-td-filter-btn">Ordenar por ▾</button>
    </div>
</div>
