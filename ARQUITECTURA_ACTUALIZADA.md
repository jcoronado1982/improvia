# ARQUITECTURA DE LA PLATAFORMA (ACTUALIZADA)

Plataforma de Suscripciones para Creadores de Contenido

**WordPress · Plugin a Medida (Sistema Pro) · PHP · CSS/JS Nativo · Docker · Caddy · Terraform**

---

## 1. Descripción del Negocio
La plataforma conecta dos tipos de usuario con roles y permisos distintos:
*   **Creador / Entrenador / Especialista:** Publica contenido, define configuración de perfil y gestiona sus servicios de suscripción o entrenamiento.
*   **Suscriptor / Atleta:** Se registra, visualiza los perfiles de los creadores y consume el contenido o servicios ofrecidos.
*   **Administrador:** Gestiona la plataforma y supervisa el sistema a nivel general.

Toda la lógica de acceso, visualización de perfiles y roles gira en torno a esta relación, facilitando un entorno donde los profesionales pueden monetizar su conocimiento y los usuarios acceder a contenido exclusivo.

---

## 2. Stack Tecnológico Actual

La arquitectura ha evolucionado hacia un sistema monolítico optimizado y empaquetado, centralizando todo el desarrollo visual y logístico en el backend de WordPress, eliminando dependencias externas complejas (como frameworks Headless) para mayor control y facilidad de despliegue.

| Tecnología | Rol en la Plataforma |
| :--- | :--- |
| **WordPress 6.x** | CMS y motor central. Gestiona la base de datos de usuarios, sesiones y roles de forma nativa. |
| **Plugin "Sistema Pro"** | Plugin desarrollado a medida (`wp-content/plugins/sistema-pro/`) que contiene **absolutamente toda la lógica de negocio, rutas y la interfaz de usuario (UI)**. |
| **MySQL 8.0** | Motor de base de datos relacional para guardar todo el contenido de WP. |
| **Docker & Docker Compose** | Contenerización total del proyecto (WordPress + Base de Datos + Servidor Web), asegurando que el entorno local sea idéntico al de producción. |
| **Terraform** | Herramienta de Infraestructura como Código (IaC) utilizada para automatizar el despliegue del proyecto hacia Google Cloud Platform (GCP). |
| **Caddy Server** | Servidor web y proxy inverso que maneja automáticamente los certificados SSL/HTTPS y enruta el tráfico hacia el contenedor de WordPress. |
| **PHP 8+** | Lenguaje de backend que procesa las plantillas de vistas, controladores y lógica de acceso. |
| **Vanilla CSS & JS** | Desarrollo de Frontend sin frameworks pesados. CSS modular basado en componentes BEM (`assets/css/`) y JavaScript limpio para llamadas AJAX y dinamismo (`assets/js/`). |

---

## 3. Plugins Utilizados

1.  **Sistema Pro (Desarrollo Propio):** El núcleo de la aplicación. Reemplaza la necesidad de un framework frontend (Anteriormente Next.js/React). Administra la visualización de los perfiles, dashboards de atletas y entrenadores, enrutamiento seguro y registro/login.
2.  **Advanced Custom Fields (ACF) PRO:** Extiende la base de datos de WordPress proporcionando relaciones complejas y campos personalizados dinámicos para los perfiles de los creadores y los programas de entrenamiento.
3.  **Akismet Anti-Spam:** Sistema de protección contra correo no deseado y registros maliciosos en los formularios de WordPress.

*(Nota: Anteriormente se consideraban WPGraphQL o pasarelas de pago directamente acopladas al código; actualmente el sistema maneja la persistencia y vistas directamente).*

---

## 4. Arquitectura del Plugin "Sistema Pro" (Módulos)

Toda la lógica custom vive en este plugin, estructurado bajo el paradigma MVC (Modelo-Vista-Controlador) adaptado a WordPress:

*   **Lógica Core (`includes/`):**
    *   `class-db-setup.php`: Siembra de datos iniciales optimizada mediante JSON (`seed-data.json`), creación de roles (`entrenador`, `atleta`) y páginas dinámicas necesarias.
    *   `class-auth.php`: Gestiona el flujo de registro, acceso e inicio de sesión.
    *   `class-router.php`: Protege las rutas de la aplicación. Bloquea el acceso a `/wp-admin` a usuarios no administradores y gestiona redirecciones según el rol.
    *   `class-ui.php`: Renderiza componentes visuales globales (Headers, Footers) y encola los assets (CSS/JS).
*   **Controladores y Vistas (`includes/Controllers/` y `includes/Views/`):**
    *   Se encargan de procesar peticiones y devolver los componentes de la interfaz de la plataforma mediante el uso de Shortcodes integrados en páginas vacías de WP.
*   **Plantillas (`templates/`):**
    *   Contiene el marcado HTML (mezclado con PHP) de cada sección de la plataforma (`tabs/personal.php`, `tabs/professional.php`, `components/trainer-card.php`, etc.).
*   **Assets (`assets/`):**
    *   Archivos estáticos segregados por dominios (ej: `provider-theme.css` para el modo claro de los creadores, scripts para validación y peticiones asíncronas de guardado de perfil).

---

## 5. Control de Acceso y Seguridad

El control de acceso es validado nativamente en PHP (`class-router.php`) antes de renderizar cualquier vista.
1.  **No autenticado:** Solo puede ver páginas de aterrizaje, listado general de creadores y formularios de inicio de sesión/registro.
2.  **Logueado (Atleta):** Ver su Dashboard, editar su perfil deportivo, consultar entrenadores. No tiene acceso al Backend (WP-Admin).
3.  **Logueado (Entrenador/Profesional):** Ver su panel de control, gestionar sus programas, información profesional y métodos de contacto. Limitado estrictamente a sus propios datos.
4.  **Backend Validation:** Toda petición AJAX (guardado de perfil, subida de archivos) que provenga del JS, es validada en el servidor mediante *Nonces* de seguridad de WordPress comprobando de nuevo la sesión activa.

---

## 6. Despliegue e Infraestructura (DevOps)

La plataforma cuenta con un pipeline de infraestructura listo par escalar:
1.  **Contenedores Inmutables:** El código fuente, el core de WP y el plugin `sistema-pro` se empaquetan en una imagen de Docker.
2.  **Base de Datos Persistente:** Se utiliza un volumen de Docker (`db_data`) y se pueden inyectar volcados `.sql` automáticamente en el arranque para migraciones ágiles.
3.  **Despliegue a la Nube (GCP):** Todo el entorno se levanta en un servidor virtual mediante comandos de Terraform contenidos en la carpeta `/terraform`, garantizando que todos los recursos en la nube sean declarativos y versionables.
4.  **HTTPS Inteligente:** El servidor Caddy genera y renueva los certificados Let's Encrypt de manera transparente al iniciar el entorno o configurar nuevos dominios de la plataforma.
