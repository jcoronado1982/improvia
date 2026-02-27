<?php
/**
 * Vista: FAQ / QA
 * Muestra un acordeón de preguntas y respuestas.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$faqs = array(
    array(
        'q' => '¿QUÉ ES IMPROVIA Y CÓMO FUNCIONA PARA LOS ENTRENADORES?',
        'a' => 'Improvia es una plataforma diseñada para conectar entrenadores y especialistas deportivos con atletas. Como entrenador, puedes ofrecer tus servicios, gestionar suscripciones y recibir feedback de tus jugadores de forma centralizada.'
    ),
    array(
        'q' => '¿QUÉ TIPO DE SERVICIOS PUEDE OFRECER UN ENTRENADOR EN IMPROVIA?',
        'a' => 'Puedes ofrecer planes de entrenamiento personalizados, sesiones individuales, análisis de video, asesoría nutricional y cualquier servicio especializado que ayude al desarrollo del atleta.'
    ),
    array(
        'q' => '¿CÓMO RECIBE EL ENTRENADOR EL MATERIAL DEL JUGADOR?',
        'a' => 'Los jugadores pueden adjuntar videos y documentos directamente en la plataforma. Recibirás una notificación en tu panel de mensajes cuando un atleta comparta nuevo material contigo.'
    ),
    array(
        'q' => '¿CUÁNTO TIEMPO TIENE EL ENTRENADOR PARA ENTREGAR SU FEEDBACK?',
        'a' => 'El tiempo de entrega depende del plan contratado. Recomendamos establecer tiempos claros en la descripción de tus servicios para mantener una buena relación con tus suscriptores.'
    ),
    array(
        'q' => '¿CÓMO SE COBRA EL SERVICIO?',
        'a' => 'Los cobros se gestionan a través de la plataforma. Improvia retiene una comisión del 10% por el uso de la infraestructura y el resto se transfiere a tu cuenta configurada.'
    ),
    array(
        'q' => '¿PUEDO TRABAJAR CON VARIOS JUGADORES AL MISMO TIEMPO?',
        'a' => 'Sí, puedes gestionar múltiples suscripciones simultáneamente. Cada atleta tendrá su propio canal de comunicación y seguimiento independiente.'
    ),
    array(
        'q' => '¿NECESITO ESTAR VINCULADO A UN CLUB PARA TRABAJAR COMO ENTRENADOR EN IMPROVIA?',
        'a' => 'No. Puedes trabajar de forma independiente, desde cualquier lugar del mundo. Solo necesitas conexión a internet, tus conocimientos futbolísticos y ganas de ayudar a los jugadores a mejorar. A más calidad, claridad y constancia en tus análisis, mayor será tu visibilidad y tus beneficios dentro de Improvia.',
        'open' => true
    ),
);
?>

<div class="sop-qa-container">
    <h1 class="sop-qa-title">Estamos para ayudarte</h1>

    <div class="sop-qa-list">
        <?php foreach ( $faqs as $index => $faq ) : 
            $is_open = isset($faq['open']) && $faq['open'];
            $item_class = $is_open ? 'sop-qa-item open' : 'sop-qa-item';
            $icon = $is_open ? '-' : '+';
        ?>
            <div class="<?php echo esc_attr($item_class); ?>">
                <button class="sop-qa-trigger" onclick="toggleQaItem(this)">
                    <span class="sop-qa-question"><?php echo esc_html($faq['q']); ?></span>
                    <span class="sop-qa-icon"><?php echo $icon; ?></span>
                </button>
                <div class="sop-qa-content">
                    <div class="sop-qa-answer">
                        <p><?php echo nl2br(esc_html($faq['a'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function toggleQaItem(btn) {
    const item = btn.closest('.sop-qa-item');
    const icon = btn.querySelector('.sop-qa-icon');
    const isOpen = item.classList.contains('open');

    // Cerrar otros (opcional, pero suele ser mejor)
    /*
    document.querySelectorAll('.sop-qa-item').forEach(other => {
        if (other !== item) {
            other.classList.remove('open');
            other.querySelector('.sop-qa-icon').textContent = '+';
        }
    });
    */

    if (isOpen) {
        item.classList.remove('open');
        icon.textContent = '+';
    } else {
        item.classList.add('open');
        icon.textContent = '-';
    }
}
</script>
