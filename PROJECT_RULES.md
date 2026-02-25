# IMPROVIA: Project Rules & Architecture

> [!CAUTION]
> **MANDATORY FIRST STEP FOR ANY AI:** 
> Before writing ANY code, you MUST read this entire document and `ROADMAP.md` to understand the architecture. DO NOT stark writing code "like crazy" ("soltar código como loco"). If you ignore these rules, you will break the system.

> [!IMPORTANT]
> **ROADMAP & PROGRESS**: Refer to [ROADMAP.md](file:///home/jcoronado/Desktop/work/marketers/IMPROVIA2/ROADMAP.md) for the current state of the project, completed tasks, and upcoming goals to avoid hallucinations.

## 1. Project Overview
IMPROVIA is a platform for content creator subscriptions, built entirely within WordPress.
- **Backend & Frontend**: WordPress CMS using a centralized custom plugin architecture (`sistema-pro`).
- **Data**: Handled via WordPress native capabilities (CPTs, Taxonomies, Users).
- **Note**: The project previously used a Next.js headless frontend with WPGraphQL, but this was DELETED and ABANDONED in favor of a pure PHP WordPress plugin approach. Do NOT look for or write React/Next.js/GraphQL code.

---

## 2. Architecture Rules (WordPress MVC)
- **Primary Plugin**: All core platform UI, logic, and routing must be built inside the `wp-content/plugins/sistema-pro/` plugin.
- **MVC Structure**: The plugin follows an MVC (Model-View-Controller) structure for maintainability.
  - **Controllers**: Logic for handling requests and shortcodes resides in `includes/Controllers/` (e.g., `class-shortcodes-controller.php`).
  - **Views**: All HTML output is cleanly separated into view templates in `includes/Views/` (e.g., `view-trainer-directory.php`, `view-profile-tabs.php`).
  - **Initiator**: `class-ui.php` acts solely to hook into WordPress actions, enqueue assets, and handle AJAX endpoints. Do NOT hardcode HTML strings inside PHP classes.
- **Routing & Redirection Rules**:
  - `class-router.php` and `class-auth.php` are responsible for handling frontend access.
  - **Providers (Coaches/Specialists)**: Upon login or trying to access login-related pages while authenticated, MUST be redirected to the `/mensajes` (Messages session).
  - **Athletes (Deportistas/Atletas)**: Upon login, MUST be redirected to the `/entrenadores` directory to begin browsing coaches.
- **MU-Plugins**: Legacy backend-only logic (like the Traceability module) resides in `wp-content/mu-plugins/modules/`. Do not mix UI logic here.

---

## 3. Frontend Rules (PHP Templates & Shortcodes)
- **Architecture**: 
  - The UI is generated using WordPress Shortcodes handled by controllers (e.g., `[sop_layout]`, `[sop_lista_entrenadores]`).
  - Shortcodes include their respective View files to render HTML directly on WordPress Pages.
- **Styling & CSS Guidelines (2026 Standard & Modular)**: 
  - **Modular Architecture**: Do not use a single monolithic CSS file. `assets/css/main.css` (or `base.css`) should only contain global `:root` variables, resets, and typography. Specific components and views MUST have their own CSS files (e.g., `assets/css/components/trainer-card.css`) enqueued properly.
  - The design is strictly **Dark Mode** with Navy (`#0a0e1a`) and Gold/Yellow (`#ffde00`) accents.
  - **Main Layout**: Exclusively use CSS Grid Layout. Prioritize `grid-template-columns: repeat(auto-fit, minmax(value, 1fr))` to achieve automatic responsive designs without abusing Media Queries.
  - **Component Alignment**: For cards or list items, use CSS Subgrid (`grid-template-rows: subgrid`). The goal is for internal elements (titles, descriptions, buttons) of different cards in the same row to be perfectly aligned with each other.
  - **Smart Components**: Use Container Queries (`@container`) instead of Media Queries whenever possible so that application modules are independent of the WordPress layout.
  - **Modernity**: Do NOT use heavy frameworks (like Bootstrap) or old tricks like floats. Use modern native CSS and variables (`--custom-properties`) for the design system.
  - **Flexbox**: Use it only for simple one-dimensional alignments (icons with text or button groups).
  - **Predefined UI Styles (STRICT)**: Always use predefined CSS classes for common UI elements (Labels: `.sop-label`, Inputs: `.sop-input`, Buttons: `.sop-btn-blue` / `.sop-btn-white`, Titles: `.sop-title-with-line` or flat `h3`/`h4` with `var(--sop-font-main)`). **Do NOT invent new styles** when creating new forms or pages. These classes are defined globally in `assets/css/components/forms.css` and guarantee design consistency across the front-end (excluding WP Admin).
  - **Component Modularity (STRICT RULES)**: If a UI block (e.g., a subscription sidebar, a reviews section, a specific widget) is distinct and could be reused, it MUST be created as a separate entity from the start.
    1. Create a dedicated PHP file for the HTML in `templates/components/`.
    2. Create a dedicated CSS file for its styles in `assets/css/components/`.
    3. Include the component dynamically in the main view using PHP `require` or `get_template_part`.
    **NEVER** hardcode massive HTML blocks or mix component CSS into global layout files (like `preview.php` or `preview.css`). Think modular BEFORE drafting the code.
  - **Internationalization (i18n) & No Hardcoded Labels (STRICT)**: You CANNOT hardcode ("quemar") labels or texts directly into the HTML of PHP files. The system MUST support multiple languages (primarily Spanish and English) across ALL components. 
    1. **NO HARDCODING:** All user-facing text strings MUST be wrapped in WordPress translation functions (e.g., `esc_html_e( 'Texto', 'sistema-pro' )`). Example: Instead of `<button>Guardar</button>`, use `<button><?php esc_html_e( 'Guardar', 'sistema-pro' ); ?></button>`. 
    2. The **Base Language source code MUST BE SPANISH**. Ensure the literal strings in your code are in Spanish (e.g. `esc_html_e('Ajustes', 'sistema-pro')` instead of 'Settings'). The global `class-i18n.php` translation dictionary handles mapping the Spanish base strings into English when the user switches the language in the frontend.

---

## 4. General Guidelines
- **Images (STRICT)**: 
  - **DO NOT** generate images using AI tools. 
  - The user has all the necessary images. Always **ask the user** for image assets when needed.
- **No Headless**: Again, **DO NOT** write or reference Next.js, React, GraphQL (WPGraphQL), or Tailwind code. Focus strictly on standard HTML/CSS and PHP. Data fetching must use native WordPress PHP functions (e.g., `WP_Query`, `get_users`).

---

## 5. Development Philosophy
- "Don't code like crazy" (no crear código como loco).
- Check existing shortcodes and views in `sistema-pro` before creating new ones.
- When the USER asks to modify a screen, locate the corresponding Shortcode Controller and modify the associated View file in `includes/Views/`.
