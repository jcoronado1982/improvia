# IMPROVIA - Base de Conocimiento (Knowledge Base)

Este documento sirve como memoria persistente para el desarrollo del proyecto IMPROVIA. Aquí se registran errores complejos, decisiones de arquitectura y soluciones técnicas para evitar redundancia en el procesamiento y facilitar la continuidad del desarrollo.

---

## 🛡️ Validación de Fechas (Flatpickr)
**Error:** Al intentar guardar el perfil, el navegador mostraba "Este campo es obligatorio" en el campo de nacimiento, incluso si ya estaba lleno.
**Causa:** El plugin `flatpickr` utiliza un `altInput` (visible) mientras mantiene el original oculto. La validación del navegador se quedaba "atrapada" en el campo oculto porque no se limpiaba el estado de validez al escribir en el campo visible.
**Solución:** 
- En `view-profile-tabs.php`, se añadieron hooks `onReady` y `onChange`.
- Se vinculó un event listener al `altInput` para ejecutar `setCustomValidity('')` sobre el input real cada vez que el usuario interactúa.
- **Archivo clave:** `wp-content/plugins/sistema-pro/includes/Views/view-profile-tabs.php`

---

## 🔗 Redirecciones y Slugs de Suscripción
**Error:** Al iniciar sesión, los entrenadores eran llevados a una página de suscripción que no funcionaba o mostraba contenido estático.
**Causa:** Discrepancia de nombres (Slugs). Existía la página `/suscripcion` (singular) y `/suscripciones` (plural). El menú lateral y la lógica de negocio real usaban la versión plural, pero el código de login apuntaba a la versión singular.
**Solución:** 
- Se unificaron todas las referencias a `/suscripciones?tab=prices`.
- Se verificó que el shortcode `[sop_suscripciones]` esté presente en la página correcta.
- **Archivo clave:** `wp-content/plugins/sistema-pro/includes/class-auth.php`

---

## 📑 Activación Dinámica de Pestañas (Tabs)
**Error:** Al redirigir con el parámetro `?tab=prices`, la página cargaba pero no abría la pestaña de precios por defecto.
**Causa:** El JavaScript original de las pestañas solo respondía a eventos de "click" manuales y no comprobaba la URL al cargar la página.
**Solución:** 
- Se implementó un listener de `DOMContentLoaded` en `view-subscriptions.php` que lee `URLSearchParams`.
- Si existe el parámetro `tab`, el script busca el botón correspondiente y dispara la función de cambio de pestaña automáticamente.
- **Archivo clave:** `wp-content/plugins/sistema-pro/includes/Views/view-subscriptions.php`

---

## 🔄 Flujo de Estatus de Usuario
**Definición:** El sistema utiliza 3 estados obligatorios:
1. **Inscrito (1):** Registro inicial, solo acceso a `/perfil`.
2. **Aprobado en proceso (2):** Perfil completado, falta configurar precios/servicios. Redirigir a `/suscripciones?tab=prices`.
3. **Aprobado (3):** Acceso total tras guardar al menos un plan activo.
- **Lógica de Downgrade:** Si un usuario nivel 3 borra todos sus planes (entrenador) o se queda sin suscripciones activas (atleta), el sistema lo baja automáticamente a nivel 2.
- **Diferencia de Etiquetas:** En el admin, el Nivel 3 se muestra como "Aprobado" para entrenadores y "Suscrito" para atletas.
- **Archivo clave:** `wp-content/plugins/sistema-pro/includes/class-ui.php` (funciones `handle_profile_update` y `handle_solicitude_approval`)

---

## 🔒 Bloqueo de Conexión (GCP Instancia Caída o Sin SSH)
**Error:** La instancia de Google Cloud Platform (GCP) no responde al tráfico HTTP (puerto 80) y la conexión SSH (puerto 22) lanza un error de "Connection timed out" tanto desde la terminal local como web.
**Causa:** El firewall interno del sistema operativo Ubuntu (UFW - Uncomplicated Firewall) se ha activado y configurado de forma restrictiva, bloqueando el acceso a puertos externos, ignorando las reglas de red generales permitidas en GCP.
**Solución:** 
- Inyectar un script de inicio de emergencia desde la consola de GCP para desactivar el firewall interno al reiniciar la máquina.
1. Ir a GCP Console -> Compute Engine -> VM instances.
2. Hacer clic en la instancia afectada (ej. `improvia-staging`) y darle a **Editar** en la barra superior.
3. Bajar hasta la sección **Automatización (Automation)**.
4. En la caja de texto **Script de inicio (Startup script)**, ingresar exactamente lo siguiente:
   ```bash
   #!/bin/bash
   ufw disable
   iptables -F
   systemctl restart docker
   ```
5. Guardar los cambios al final de la página.
6. Hacer clic en **Restablecer (Reset)** en la barra superior. Esto reinicia la máquina y fuerza la ejecución del script.
- **Nota:** Una vez recuperado el acceso (HTTP/SSH), verifica la configuración de UFW o Docker para prevenir que vuelva a sobreescribirse de forma errónea.
