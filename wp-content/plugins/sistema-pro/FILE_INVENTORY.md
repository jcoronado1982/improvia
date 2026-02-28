# Inventario de Archivos - Sistema PRO

Este documento contiene una lista estructurada de los archivos que componen el plugin **Sistema PRO**, organizada por su función dentro de la arquitectura MVC y el ecosistema de WordPress.

## 📂 Archivos Principales
*   `sistema-pro.php`: Punto de entrada del plugin. Define constantes globales e inicializa el plugin.

## 📁 Lógica y Núcleo (`includes/`)
*   `class-ui.php`: Clase maestra que maneja los hooks de WordPress (CSS, Header, Footer) y procesa las peticiones AJAX.
*   `class-db-setup.php`: Gestiona la creación de roles, páginas programáticas y el sembrado de datos en taxonomías.
*   `class-auth.php`: Controla la lógica de autenticación, registro de usuarios y redirecciones por rol.
*   `class-router.php`: Gestiona las reglas de reescritura de URL y la carga de vistas personalizadas.
*   `class-logger.php`: Utilidad `SOP_Debug` para trazabilidad en `debug.log`.
*   `class-i18n.php`: Configuración de internacionalización y carga de archivos `.mo`/`.po`.

### 🎮 Controladores
*   `Controllers/class-shortcodes-controller.php`: Define y procesa todos los shortcodes (`[sop_layout]`, `[sop_detalle_entrenador]`, etc.).

## 📁 Vistas del Sistema (`includes/Views/`)
Archivos que estructuran las páginas de alto nivel:
*   `view-profile-tabs.php`: Interfaz central del perfil con pestañas dinámicas.
*   `view-subscriptions.php`: Gestión de planes, precios y visualización de suscriptores para entrenadores.
*   `view-solicitudes.php`: Panel de gestión para aceptar/rechazar suscripciones.
*   `view-trainer-directory.php`: Buscador de deportistas y especialistas.
*   `view-trainer-detail.php`: Ficha pública del profesional.
*   `view-messaging.php`: Bandeja de entrada y chat interno.
*   `view-login.php` / `view-register.php`: Vistas de autenticación adaptadas al diseño.
*   `view-global-header.php` / `view-global-footer.php`: Componentes globales consistentes.

## 📁 Plantillas y Componentes (`templates/`)
Fragmentos de código reutilizables:
*   **Pestañas del Perfil (`templates/tabs/`):** `personal.php`, `professional.php`, `settings.php`, `security.php`, `preview.php`, `sesiones.php`.
*   **Componentes UI (`templates/components/`):**
    *   `trainer-card.php`: Tarjeta de presentación en el directorio.
    *   `pricing-card.php`: Visualización de planes de suscripción.
    *   `filter-bar.php`: Filtros avanzados para el directorio.
    *   `reviews.php`: Sistema de reseñas y valoraciones.
    *   `rrss.php`: Iconos y enlaces a redes sociales.

## 📁 Activos Estáticos (`assets/`)
*   **CSS modulares:** Ubicados en `assets/css/components/` para mantener estilos aislados (tabs, header, sidebar, etc.).
*   **JavaScript:** `assets/js/settings.js` maneja la interactividad AJAX de todo el perfil.
*   **Imágenes:** Localizadas en `assets/images/` para consistencia visual del diseño (logos, iconos, banderas).

---
*Última revisión: 2026-02-27 (Post-limpieza de basura)*
