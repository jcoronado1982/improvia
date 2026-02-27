jQuery(document).ready(function ($) {
    // Escuchar cambios en el selector de idioma
    $('#sop_lang_pref').on('change', function () {
        var selectedLang = $(this).val();

        // Ignorar si seleccionan la opción "Cambiar" (vacía/placeholder)
        if (!selectedLang || selectedLang === 'Cambiar') {
            return;
        }

        // Mostrar un estado de carga opcional aquí (ej. opacar el select)
        $(this).css('opacity', '0.5');

        $.ajax({
            url: sop_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'sop_save_language_preference',
                nonce: sop_ajax.nonce,
                lang: selectedLang
            },
            success: function (response) {
                if (response.success) {
                    // Recargar la página para aplicar el nuevo idioma
                    window.location.reload();
                } else {
                    alert('Error al guardar el idioma: ' + response.data);
                    $('#sop_lang_pref').css('opacity', '1');
                }
            },
            error: function () {
                alert('Ocurrió un error en la conexión.');
                $('#sop_lang_pref').css('opacity', '1');
            }
        });
    });
});

/** 
 * PRICING CARD INTERACTION LOGIC (Directory & Detail View)
 */

window.sopSwitchMode = function (userId, mode) {
    // 1. Toggle UI Buttons
    var wrapper = document.getElementById('pc-wrapper-' + userId);
    if (!wrapper) return;

    var tabs = wrapper.querySelectorAll('.sop-pc-tab');
    tabs.forEach(t => t.classList.remove('active'));

    var activeTab = wrapper.querySelector('.sop-pc-tab[data-mode="' + mode + '"]');
    if (activeTab) activeTab.classList.add('active');

    // 2. Change Title
    var title = document.getElementById('pc-title-' + userId);
    if (title) title.textContent = mode === 'periodo' ? 'Periodo' : 'Sesiones';

    // 3. Toggle Grids
    var gridPeriodo = document.getElementById('pc-grid-periodo-' + userId);
    var gridSesiones = document.getElementById('pc-grid-sesiones-' + userId);

    if (gridPeriodo) gridPeriodo.style.display = mode === 'periodo' ? 'grid' : 'none';
    if (gridSesiones) gridSesiones.style.display = mode === 'sesiones' ? 'flex' : 'none';

    // 4. Auto-select first option in the new active grid
    var activeGrid = mode === 'periodo' ? gridPeriodo : gridSesiones;
    if (activeGrid) {
        var firstBtn = activeGrid.querySelector('.sop-pc-opt-btn:not([disabled])');
        if (firstBtn) firstBtn.click();
    }
};

window.sopSelectPricingOption = function (userId, btnElement) {
    if (btnElement.hasAttribute('disabled')) return;

    var wrapper = document.getElementById('pc-wrapper-' + userId);
    if (!wrapper) return;

    // 1. Remove active state from ALL buttons in this specific card
    var allBtns = wrapper.querySelectorAll('.sop-pc-opt-btn');
    allBtns.forEach(b => b.classList.remove('active'));

    // 2. Set current as active
    btnElement.classList.add('active');

    // 3. Update Large Price Display
    var price = btnElement.getAttribute('data-price');
    var display = document.getElementById('pc-main-price-' + userId);
    if (display) {
        display.textContent = price + '$';
    }

    // 4. Store selected option in the wrapper dataset for the checkout button to read
    wrapper.dataset.selectedPrice = price;
    wrapper.dataset.selectedLabel = btnElement.getAttribute('data-label');
};

window.sopProcessMockCheckout = function (userId) {
    var wrapper = document.getElementById('pc-wrapper-' + userId);
    if (!wrapper) return;

    var price = wrapper.dataset.selectedPrice;
    var label = wrapper.dataset.selectedLabel;

    if (!price || price === "0") {
        alert("Por favor selecciona un plan o paquete válido primero.");
        return;
    }

    // In Fase 1, redirect to our Mock Checkout page passing params via GET
    window.location.href = '/checkout-simulado?trainer_id=' + userId + '&plan_label=' + encodeURIComponent(label) + '&plan_price=' + encodeURIComponent(price);
};
