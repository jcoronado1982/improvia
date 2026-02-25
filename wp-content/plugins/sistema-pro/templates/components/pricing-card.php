<?php
/**
 * Component: Pricing / Subscription Card
 * Shows the 4-tier pricing grid with action buttons.
 * Reusable component for trainer/specialist directories.
 * 
 * Expected vars: none (static for now, will be dynamic later)
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="sop-trainer-card sop-tc-pricing-card">
    <div class="sop-tc-pricing-grid">
        <div class="sop-tc-pricing-item">
            <div class="sop-tc-pricing-box">
                <span class="sop-tc-price-amount">80$</span>
            </div>
            <span class="sop-tc-price-period">Semanal</span>
        </div>
        <div class="sop-tc-pricing-item">
            <div class="sop-tc-pricing-box">
                <span class="sop-tc-price-amount">160$</span>
            </div>
            <span class="sop-tc-price-period">Mensual</span>
        </div>
        <div class="sop-tc-pricing-item">
            <div class="sop-tc-pricing-box">
                <span class="sop-tc-price-amount">540$</span>
            </div>
            <span class="sop-tc-price-period">Trimestral</span>
        </div>
        <div class="sop-tc-pricing-item">
            <div class="sop-tc-pricing-box">
                <span class="sop-tc-price-amount">1.100$</span>
            </div>
            <span class="sop-tc-price-period">Anual</span>
        </div>
    </div>
    <!-- Action Buttons -->
    <div class="sop-tc-pricing-actions">
        <button class="sop-tc-action-btn sop-tc-action-fav">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
            Añadir a favoritos
        </button>
        <button class="sop-tc-action-btn sop-tc-action-ver">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Ver perfil
        </button>
    </div>
</div>
