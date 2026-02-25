<?php
/**
 * Component: Paginator
 * Reusable pagination component for directory views.
 * 
 * Expected vars: $current_page (int), $total_pages (int)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$current_page = isset($current_page) ? (int) $current_page : 1;
$total_pages  = isset($total_pages) ? (int) $total_pages : 12;

// Calculate visible pages
$visible_pages = array();
$visible_pages[] = 1;
if ( $current_page > 3 ) $visible_pages[] = '...';
for ( $i = max(2, $current_page - 1); $i <= min($total_pages - 1, $current_page + 1); $i++ ) {
    $visible_pages[] = $i;
}
if ( $current_page < $total_pages - 2 ) $visible_pages[] = '...';
if ( $total_pages > 1 ) $visible_pages[] = $total_pages;
?>
<div class="sop-paginator">
    <?php 
    $prev_url = add_query_arg( 'pag', max( 1, $current_page - 1 ) );
    $next_url = add_query_arg( 'pag', min( $total_pages, $current_page + 1 ) );
    ?>
    <a href="<?php echo esc_url( $prev_url ); ?>" class="sop-pag-btn sop-pag-arrow <?php echo ($current_page <= 1) ? 'sop-pag-disabled' : ''; ?>" aria-label="Previous Page">‹</a>
    
    <?php foreach ( $visible_pages as $page ) : ?>
        <?php if ( $page === '...' ) : ?>
            <span class="sop-pag-dots">...</span>
        <?php else : ?>
            <a href="<?php echo esc_url( add_query_arg( 'pag', $page ) ); ?>" 
               class="sop-pag-btn <?php echo ($page === $current_page) ? 'sop-pag-active' : ''; ?>">
               <?php echo esc_html($page); ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <a href="<?php echo esc_url( $next_url ); ?>" class="sop-pag-btn sop-pag-arrow <?php echo ($current_page >= $total_pages) ? 'sop-pag-disabled' : ''; ?>" aria-label="Next Page">›</a>
</div>
