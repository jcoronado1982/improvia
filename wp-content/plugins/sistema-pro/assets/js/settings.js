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

