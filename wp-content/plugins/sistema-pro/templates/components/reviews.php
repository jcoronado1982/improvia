<?php
/**
 * Componente Reutilizable: Sección de Reseñas (Reviews)
 * Se muestra en el perfil público del entrenador y en la previsualización.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- Reviews -->
<div class="sop-preview-card sop-full-width">
    <h3 class="sop-preview-card-title"><?php esc_html_e( 'REVIEWS', 'sistema-pro' ); ?></h3>
    <div class="sop-reviews-header">
        <div class="sop-reviews-summary">
            <p class="sop-reviews-subtitle"><strong><?php esc_html_e( 'Reseñas y valoraciones de clientes', 'sistema-pro' ); ?></strong></p>
            <div class="sop-reviews-avg">
                <span class="sop-stars">★★★★☆</span>
                <span class="sop-reviews-avg-text"><?php echo sprintf( esc_html__( '(%s sobre %s)', 'sistema-pro' ), '4,7', '5' ); ?></span>
            </div>
            <p class="sop-reviews-count"><?php echo sprintf( esc_html__( 'Basado en %s reseñas', 'sistema-pro' ), '10' ); ?></p>
        </div>
        <div class="sop-reviews-bars">
            <div class="sop-bar-row"><span>5 stars</span><div class="sop-bar-track"><div class="sop-bar-fill" style="width: 70%;"></div></div></div>
            <div class="sop-bar-row"><span>4 stars</span><div class="sop-bar-track"><div class="sop-bar-fill" style="width: 50%;"></div></div></div>
            <div class="sop-bar-row"><span>3 stars</span><div class="sop-bar-track"><div class="sop-bar-fill" style="width: 30%;"></div></div></div>
            <div class="sop-bar-row"><span>2 stars</span><div class="sop-bar-track"><div class="sop-bar-fill" style="width: 15%;"></div></div></div>
            <div class="sop-bar-row"><span>1 star</span><div class="sop-bar-track"><div class="sop-bar-fill" style="width: 5%;"></div></div></div>
        </div>
    </div>

    <div class="sop-reviews-filters">
        <button class="sop-review-filter-btn"><?php echo sprintf( esc_html__( 'Todas las opiniones (%s)', 'sistema-pro' ), '10' ); ?></button>
        <button class="sop-review-filter-btn active"><?php echo sprintf( esc_html__( 'Positivas (%s) ✕', 'sistema-pro' ), '7' ); ?></button>
        <button class="sop-review-filter-btn"><?php echo sprintf( esc_html__( 'Neutras (%s)', 'sistema-pro' ), '2' ); ?></button>
        <button class="sop-review-filter-btn"><?php echo sprintf( esc_html__( 'Negativas (%s)', 'sistema-pro' ), '1' ); ?></button>
    </div>

    <div class="sop-reviews-grid">
        <?php for($i=0; $i<4; $i++): ?>
        <div class="sop-review-card">
            <p class="sop-review-text">Lorem ipsum dolor sit amet consectetur. Amet velit aliquet adipiscing et amet consequat donec. Pharetra et venenatis cras et aliquet senectus. Diam lorem morbi sit commodo.</p>
            <div class="sop-review-author">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/img_review.png' ); ?>" alt="Devon Lane" class="sop-review-avatar">
                <div>
                    <strong>Devon Lane</strong>
                    <span>Albert Ferrer</span>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div>
