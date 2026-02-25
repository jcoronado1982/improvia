<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php $is_provider = current_user_can( 'entrenador' ) || current_user_can( 'especialista' ); ?>
<div class="sop-tabs-container" data-is-provider="<?php echo $is_provider ? '1' : '0'; ?>">
    <div class="sop-tab-nav">
        <button class="sop-tab-btn active" data-tab="personal"><img src="<?php echo esc_url( SOP_URL . 'assets/images/user_blue.png' ); ?>" alt=""> <?php esc_html_e( 'Información personal', 'sistema-pro' ); ?></button>
        <button class="sop-tab-btn" data-tab="professional"><img src="<?php echo esc_url( SOP_URL . 'assets/images/mortarboard.png' ); ?>" alt=""> <?php esc_html_e( 'Información profesional', 'sistema-pro' ); ?></button>
        <button class="sop-tab-btn" data-tab="security"><img src="<?php echo esc_url( SOP_URL . 'assets/images/shield.png' ); ?>" alt=""> <?php esc_html_e( 'Seguridad y acceso', 'sistema-pro' ); ?></button>
        <button class="sop-tab-btn" data-tab="preview"><img src="<?php echo esc_url( SOP_URL . 'assets/images/eye.png' ); ?>" alt=""> <?php esc_html_e( 'Previsualizar', 'sistema-pro' ); ?></button>
        <button class="sop-tab-btn" data-tab="settings"><img src="<?php echo esc_url( SOP_URL . 'assets/images/settings.png' ); ?>" alt=""> <?php esc_html_e( 'Ajustes', 'sistema-pro' ); ?></button>
    </div>

    <!-- Carga Modular de Pestañas -->
    <div id="personal" class="sop-tab-content active">
        <?php include SOP_PATH . 'templates/tabs/personal.php'; ?>
    </div>

    <div id="professional" class="sop-tab-content">
        <form id="sop-professional-form" class="sop_professional_tab_form">
            <?php include SOP_PATH . 'templates/tabs/professional.php'; ?>
            <div style="margin-top: 40px; text-align: right;">
                <span id="sop-prof-msg" style="margin-right: 20px; font-size: 0.9rem;"></span>
                <button type="submit" class="sop-btn-white"><?php esc_html_e( 'Guardar Cambios', 'sistema-pro' ); ?></button>
            </div>
        </form>
    </div>

    <div id="security" class="sop-tab-content">
        <?php include SOP_PATH . 'templates/tabs/security.php'; ?>
    </div>

    <div id="preview" class="sop-tab-content">
        <?php include SOP_PATH . 'templates/tabs/preview.php'; ?>
    </div>

    <div id="settings" class="sop-tab-content">
        <?php include SOP_PATH . 'templates/tabs/settings.php'; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejo de TABS
    const btns = document.querySelectorAll('.sop-tab-btn');
    const contents = document.querySelectorAll('.sop-tab-content');

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab');
            btns.forEach(b => b.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(target).classList.add('active');
            
            // Toggle Theme for Preview Tab
            const isProvider = document.querySelector('.sop-tabs-container').getAttribute('data-is-provider') === '1';
            
            if (target === 'preview') {
                document.body.classList.add('sop-preview-mode');
                // Al entrar en Preview, quitamos el tema claro si es proveedor para ver el fondo azul
                if (isProvider) {
                    document.body.classList.remove('sop-provider-theme-light');
                }
            } else {
                document.body.classList.remove('sop-preview-mode');
                // Al salir de Preview, restauramos el tema claro si es proveedor
                if (isProvider) {
                    document.body.classList.add('sop-provider-theme-light');
                }
            }
        });
    });

    // --- Lógica Pestaña Personal (Idiomas) ---
    let languages = <?php echo json_encode(get_user_meta($user->ID, 'sop_idiomas_data', true) ?: []); ?>;
    const langList = document.getElementById('sop-languages-list');
    const addLangBtn = document.getElementById('sop-add-lang');

    function renderLanguages() {
        if(!langList) return;
        langList.innerHTML = '';
        languages.forEach((item, index) => {
            const tag = document.createElement('div');
            tag.className = 'sop-tab-badge';
            tag.innerHTML = `<span>${item.lang_name} - ${item.level_name}</span><span style="cursor: pointer; opacity: 0.5;" onclick="removeLang(${index})">✕</span>`;
            langList.appendChild(tag);
        });
    }

    window.removeLang = (index) => {
        languages.splice(index, 1);
        renderLanguages();
    };

    if(addLangBtn) {
        addLangBtn.addEventListener('click', () => {
            const langId = document.getElementById('new-lang-id');
            const levelId = document.getElementById('new-lang-level');
            languages.push({
                lang_id: langId.value,
                lang_name: langId.options[langId.selectedIndex].text,
                level_id: levelId.value,
                level_name: levelId.options[levelId.selectedIndex].text
            });
            renderLanguages();
        });
    }
    renderLanguages();

    // --- Lógica Pestaña Profesional (RRSS) ---
    let rrss = <?php echo json_encode(get_user_meta($user->ID, 'sop_rrss_data', true) ?: []); ?>;
    const rrssList = document.getElementById('sop-rrss-list');
    const addRrssBtn = document.getElementById('sop-add-rrss');

    function renderRrss() {
        if(!rrssList) return;
        rrssList.innerHTML = '';
        rrss.forEach((item, index) => {
            const tag = document.createElement('div');
            tag.className = 'sop-tab-badge';
            tag.innerHTML = `<span>${item.type_name}: ${item.value}</span><span style="cursor: pointer; opacity: 0.5;" onclick="removeRrss(${index})">✕</span>`;
            rrssList.appendChild(tag);
        });
    }

    window.removeRrss = (index) => {
        rrss.splice(index, 1);
        renderRrss();
    };

    if(addRrssBtn) {
        addRrssBtn.addEventListener('click', () => {
            const typeId = document.getElementById('sop-rrss-type');
            const val = document.getElementById('sop-rrss-value');
            if(!val.value) return;
            rrss.push({
                type_id: typeId.value,
                type_name: typeId.options[typeId.selectedIndex].text,
                value: val.value
            });
            val.value = '';
            renderRrss();
        });
    }
    renderRrss();

    // -- Lógica Editar Descripción Profesional --
    const editDescBtn = document.getElementById('sop-edit-prof-desc-btn');
    const descDisplay = document.getElementById('sop-prof-desc-display');
    const descInput = document.getElementById('sop-prof-desc-input');

    if (editDescBtn && descDisplay && descInput) {
        editDescBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (descInput.style.display === 'none') {
                descInput.style.display = 'block';
                descDisplay.style.display = 'none';
                descInput.focus();
                
                // Add active state to icon
                this.style.background = 'rgba(255,255,255,0.1)';
            } else {
                descInput.style.display = 'none';
                descDisplay.style.display = 'block';
                
                // Remove active state
                this.style.background = 'transparent';
                
                // Update display preview instantly
                let newText = descInput.value.trim();
                if (newText) {
                    descDisplay.innerHTML = newText.replace(/\n/g, '<br>');
                } else {
                    descDisplay.textContent = 'Lorem ipsum dolor sit amet consectetur. Pretium at libero fermentum in vulputate. Viverra cum non ultricies tempor arcu in accumsan eu. Fringilla ut nulla neque leo phasellus tellus. Dignissim ante pulvinar purus in non tristique sed cursus. Ac sapien amet tellus quam pulvinar. Ac ipsum rutrum ac gravida massa iaculis sociis etiam. Facilisis augue auctor risus elementum. Aenean duis egestas amet urna viverra vitae bibendum blandit gravida.';
                }
            }
        });
    }

    // AJAX SAVING
    const forms = [
        { id: 'sop-profile-form', msgId: 'sop-profile-msg', extra: { key: 'sop_idiomas', val: () => languages } },
        { id: 'sop-professional-form', msgId: 'sop-prof-msg', extra: { key: 'sop_rrss', val: () => rrss } }
    ];

    forms.forEach(f => {
        const el = document.getElementById(f.id);
        if(!el) return;
        el.addEventListener('submit', (e) => {
            e.preventDefault();
            const msgEl = document.getElementById(f.msgId);
            msgEl.textContent = 'Guardando...';
            msgEl.style.color = '#fff';

            const formData = new FormData(el);
            formData.append('action', 'sop_update_profile');
            if(f.extra) {
                formData.append(f.extra.key, JSON.stringify(f.extra.val()));
            }

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msgEl.textContent = '✓ Guardado correctamente';
                    msgEl.style.color = '#ffde00';
                    setTimeout(() => { msgEl.textContent = ''; }, 3000);
                } else {
                    msgEl.textContent = data.data || 'Error al guardar';
                    msgEl.style.color = '#ff4b4b';
                }
            });
        });
    });
});
</script>
