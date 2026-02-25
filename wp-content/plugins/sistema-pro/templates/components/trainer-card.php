<?php
/**
 * Component: Trainer Card
 * Reusable card for displaying a trainer's profile summary.
 * 
 * Expected vars: $name (string) - trainer display name
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$name = isset($name) ? $name : 'ENTRENADOR';
?>
<div class="sop-trainer-card">
    <!-- Card Top: Image + Badges (Side-by-side) -->
    <div class="sop-tc-header">
        <div class="sop-tc-image-wrapper">
            <img src="<?php echo esc_url( SOP_URL . 'assets/images/coach.png' ); ?>" alt="<?php echo esc_attr($name); ?>" class="sop-tc-image">
            <button class="sop-tc-icon-btn">⚡</button>
        </div>
        <div class="sop-tc-header-right">
            <div class="sop-tc-header-top-row">
                <button class="sop-tc-menu-btn">⋮</button>
            </div>
            <div class="sop-tc-badges">
                <div class="sop-tc-rating" style="margin-bottom: 5px;">
                    <span class="sop-stars-sm">★★★★★</span> <span class="sop-tc-rating-count">(10)</span>
                </div>
                <div class="sop-tc-badge-row">
                    <span class="sop-tc-badge-label">Experience</span>
                    <div class="sop-tc-badge-box">6 Years</div>
                </div>
                <div class="sop-tc-badge-row">
                    <span class="sop-tc-badge-label">Idioma</span>
                    <div class="sop-tc-badge-box">es-en</div>
                </div>
                <div class="sop-tc-badge-row">
                     <span class="sop-tc-flag-icon" style="font-size: 1.2rem; line-height: 1;">🇦🇷</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Body -->
    <div class="sop-tc-body">
        <h3 class="sop-tc-name"><?php echo esc_html($name); ?></h3>
        <span class="sop-tc-role">FOOTBALL COACH</span>
        <p class="sop-tc-desc">Lorem ipsum dolor sit amet consectetur. Amet vestibulum nulla lorem quam augue vel ipsum viverra semper. quam augue vel...</p>
        
        <div class="sop-tc-tags-row">
            <span class="sop-tc-tag-label">Nivel</span>
            <span class="sop-tc-tag-val">UEFA A</span>
            <span class="sop-tc-tag-label" style="margin-left: 10px;">Cupos</span>
            <span class="sop-tc-tag-val sop-tc-tag-cupos">8/10</span>
        </div>

        <div class="sop-tc-focus">
            <span class="sop-tc-focus-label">Focus</span>
            <div class="sop-tc-focus-tags">
                <span class="sop-tc-focus-tag">Delantero</span>
                <span class="sop-tc-focus-tag">Porteros</span>
                <span class="sop-tc-focus-tag">Carrilleros</span>
            </div>
            <div class="sop-tc-focus-tags" style="margin-top: 8px;">
                <span class="sop-tc-focus-tag">Transición defensa/ataque</span>
                <span class="sop-tc-focus-more">...</span>
            </div>
        </div>
    </div>

    <!-- Card Footer: Red Theme -->
    <div class="sop-tc-footer">
        <span class="sop-tc-desde">Desde</span>
        <div class="sop-tc-price-pill">
            <strong>60$</strong> <span>2 sesiones</span>
        </div>
    </div>
</div>
