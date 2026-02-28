# Inventario de Archivos - Plugin Sistema Pro

Este archivo contiene la explicación detallada de cada componente del plugin, sirviendo como mapa de referencia para futuros desarrollos.

## 1. Archivos Raíz

### [sistema-pro.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/sistema-pro.php)
*   **Propósito:** Punto de entrada principal del plugin.
*   **Funciones clave:** Cabecera de metadatos de WordPress, carga de módulos y constantes globales (`SOP_PATH`, `SOP_URL`). Inicializa las clases principales en el hook `plugins_loaded`.

---

## 2. Lógica de Negocio (`includes/`)

### [includes/class-db-setup.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/includes/class-db-setup.php)
*   **Propósito:** El "arquitecto" de los datos y el sistema.
*   **Funciones clave:** Crea roles (`entrenador`, `atleta`), páginas necesarias y registra taxonomías. Implementa un sistema de **siembra de datos optimizado** mediante JSON y control de versiones para evitar sobrecarga del servidor.

### [includes/class-auth.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/includes/class-auth.php)
*   **Propósito:** Gestor de autenticación y seguridad.
*   **Funciones clave:** Maneja el registro, login y redirecciones inteligentes basadas en el rol y estado (`sop_user_status`) del usuario.

### [includes/class-ui.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/includes/class-ui.php)
*   **Propósito:** El "diseñador" y motor de la interfaz.
*   **Funciones clave:** Carga modular de CSS/JS, inyecta el Header y Footer global, gestiona las clases del `body` según el rol y procesa todas las acciones de perfil y suscripción vía AJAX.

### [includes/class-i18n.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/includes/class-i18n.php)
*   **Propósito:** El "traductor" multilingüe.
*   **Funciones clave:** Detecta y aplica el idioma preferido del usuario. Utiliza un diccionario interno para traducciones rápidas y gestiona la carga de archivos `.mo`/`.po`.

### [includes/class-logger.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/includes/class-logger.php)
*   **Propósito:** Trazabilidad y depuración (`SOP_Debug`).
*   **Funciones clave:** Proporciona un método estático `log()` para registrar eventos críticos del sistema en `debug.log`, facilitando el soporte y mantenimiento.

### [includes/class-router.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/includes/class-router.php)
*   **Propósito:** El "GPS" y guardia de seguridad de la navegación.
*   **Funciones clave:** Controla quién puede ver qué. Redirige a los visitantes no logueados fuera de las zonas privadas, bloquea el acceso al panel de administración (`wp-admin`) para entrenadores y atletas, y decide a qué página enviarte según tu rol al iniciar sesión.

---

## 3. Controladores y Vistas (`includes/Controllers/` & `includes/Views/`)

### [includes/Controllers/class-shortcodes-controller.php](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/includes/Controllers/class-shortcodes-controller.php)
*   **Propósito:** El "Director de Orquesta" (Controller).
*   **Funciones clave:** Registra todos los shortcodes de WordPress. Prepara los datos necesarios y llama a los archivos de `Views/`.

### Directorio: `includes/Views/`
*   **Propósito:** El "Cuerpo" visual del sistema (Templates de visualización).
*   **Componentes Clave:** marcos globales, navegación lateral, gestión de suscripciones, solicitudes y directorios de entrenadores.

---

## 4. Plantillas de Contenido (`templates/`)

### Directorio: `templates/tabs/`
*   **Propósito:** Estructura modular del perfil de usuario.
*   **Funciones clave:** Contiene la interfaz de cada pestaña del perfil (`personal.php`, `professional.php`, `preview.php`, etc.).

### Directorio: `templates/components/`
*   **Propósito:** Piezas de interfaz reutilizables.
*   **Funciones clave:** Trainer Cards, Paginadores, Sidebars y micro-componentes profesionales.

---

## 5. Recursos y Activos (`assets/`)

### Directorio: `assets/css/`
*   **Propósito:** El "Armario" de estilos del plugin.
*   **Estructura Modular:**
    *   `base.css`: Estilos globales, variables de color y limpieza de elementos por defecto del tema de WordPress.
    *   `layout/`:
        *   `layout.css`: Define la estructura de columnas de la aplicación y el diseño responsivo.
        *   `provider-theme.css`: El "Modo Claro" dinámico que se activa solo para entrenadores y especialistas.
    *   `components/`: Un archivo `.css` dedicado para cada pieza de interfaz (ej. `header.css`, `trainer-card.css`, `tabs.css`).

### Directorio: `assets/js/`
*   **Propósito:** El "Sistema Nervioso" de la interfaz.
*   **Funciones clave:**
    *   `settings.js`: Gestiona las interacciones de los formularios, la carga de selectores dinámicos y las llamadas AJAX para guardar el perfil sin recargar la página.

### [assets/data/seed-data.json](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA/wp-content/plugins/sistema-pro/assets/data/seed-data.json)
*   **Propósito:** El "Almacén" de información estática.
*   **Funciones clave:** Contiene todas las listas (países, idiomas, posiciones, etc.) que alimentan los selectores del plugin. Es la fuente de verdad del sistema de siembra de datos.

---
