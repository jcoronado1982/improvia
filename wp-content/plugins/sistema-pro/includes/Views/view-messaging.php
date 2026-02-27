<?php
/**
 * Vista: Mensajería
 * Muestra la interfaz de mensajes con lista lateral y detalle.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="sop-messaging-container">
    <div class="sop-messaging-header">
        Tienes <strong>2 mensajes nuevos</strong>
    </div>

    <div class="sop-messaging-main">
        <!-- Message List (Left) -->
        <div class="sop-msg-list">
            
            <!-- Message 1 (Active) -->
            <div class="sop-msg-item active">
                <div class="sop-msg-unread-dot"></div>
                <div class="sop-msg-item-top">
                    <input type="checkbox" checked>
                    <span>22 Julio 2025</span>
                </div>
                <div class="sop-msg-item-name">Carles Alonso</div>
                <div class="sop-msg-item-subject">Curva de análisis posición delantera</div>
                <p class="sop-msg-item-excerpt">Lorem ipsum dolor sit amet consectetur. Ultricies sed mattis ullamcorper...</p>
            </div>

            <!-- Message 2 -->
            <div class="sop-msg-item">
                <div class="sop-msg-unread-dot"></div>
                <div class="sop-msg-item-top">
                    <input type="checkbox">
                    <span>22 Julio 2025</span>
                </div>
                <div class="sop-msg-item-name">Carles Alonso</div>
                <div class="sop-msg-item-subject">Curva de análisis posición delantera</div>
                <p class="sop-msg-item-excerpt">Lorem ipsum dolor sit amet consectetur. Ultricies sed mattis ullamcorper...</p>
            </div>

            <!-- Message 3 -->
            <div class="sop-msg-item">
                <div class="sop-msg-item-top">
                    <input type="checkbox">
                    <span>22 Julio 2025</span>
                </div>
                <div class="sop-msg-item-name">Carles Alonso</div>
                <div class="sop-msg-item-subject">Curva de análisis posición delantera</div>
                <p class="sop-msg-item-excerpt">Lorem ipsum dolor sit amet consectetur. Ultricies sed mattis ullamcorper...</p>
            </div>

        </div>

        <!-- Message Detail (Right) -->
        <div class="sop-msg-detail-card">
            
            <div class="sop-msg-detail-tabs">
                <button class="sop-msg-tab active">
                    Bandeja de entrada
                    <span class="unread-count"></span>
                </button>
                <button class="sop-msg-tab">Enviados</button>
                <button class="sop-msg-tab">Papelera</button>
            </div>

            <h1 class="sop-msg-detail-title">Curva de análisis posición delantera</h1>
            <div class="sop-msg-detail-sender">Carles Alonso</div>

            <div class="sop-msg-attachments-header">
                <span class="dashicons dashicons-video-alt3" style="font-size: 18px; width: 18px; height: 18px; color: #111827;"></span>
                2 videos adjuntos
            </div>

            <div class="sop-msg-attachments-list">
                <a href="#" class="sop-msg-attachment-box">
                    <span class="attachment-name">Video 1</span>
                    <span class="dashicons dashicons-download" style="font-size: 14px; width: 14px; height: 14px;"></span>
                </a>
                <a href="#" class="sop-msg-attachment-box">
                    <span class="attachment-name">Video 2</span>
                    <span class="dashicons dashicons-download" style="font-size: 14px; width: 14px; height: 14px;"></span>
                </a>
            </div>

            <div class="sop-msg-content">
                <p>Lorem ipsum dolor sit amet consectetur. Praesent nulla velit ornare imperdiet malesuada amet phasellus. Mi egestas ut quis blandit nunc porttitor sit aliquam consequat. Ut magna maecenas sed sed eu lectus. Vitae lobortis aenean aliquam ut. Sed ultrices venenatis vitae aliquam habitasse non eget. Nulla lectus fermentum pellentesque aenean cursus. Dolor venenatis sit ac sit.</p>
            </div>

            <div class="sop-msg-detail-footer">
                <button class="sop-msg-reply-btn">Responder</button>
                <div class="sop-msg-attach-icon">
                    <span class="dashicons dashicons-paperclip" style="color: #6b7280;"></span>
                </div>
                <div class="sop-msg-more-btn">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</div>
