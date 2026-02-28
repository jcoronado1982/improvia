# IMPROVIA: Project Roadmap & AI Guidance

> [!CAUTION]
> **MANDATORY FIRST STEP FOR ANY AI:** 
> Before writing ANY code or proposing any changes, you MUST read this entire document and `PROJECT_RULES.md` to understand the established MVC architecture. DO NOT start coding blindly ("soltar código como loco"). 

This document serves as the source of truth for the project's progress and future direction. Any AI assistant starting a new session MUST review this to understand the current state and avoid "hallucinating" incorrect patterns.

---

## 1. Current Progress (V1.3 - Simulation & Cleanup)

### ✅ Core Architecture
- [x] **Architecture Pivot**: Legacy Next.js frontend deleted. All UI lives inside `sistema-pro` WordPress plugin.
- [x] **MVC Refactor**: Controllers in `includes/Controllers/`, Views in `includes/Views/`, templates in `templates/`.
- [x] **Modular CSS**: Each component/view has its own CSS file in `assets/css/components/`.
- [x] **Asset Management**: `class-ui.php` dynamically enqueues all CSS files from `assets/css/layout/` and `assets/css/components/`.

### ✅ Global Layout
- [x] **Header** (`view-global-header.php`): Logo, navigation (Athletes, Sports Specialists, Coaches, Get to Know Us), language selector (ES flag), user pill (login/name).
- [x] **Footer** (`view-global-footer.php`): Logo, 5-column grid (Policies, Contact, Services, Mas, RRSS with SVG icons), bottom bar with copyright.
- [x] **Sidebar Menu** (`view-sidebar-menu.php`): Sticky nav with icons — Perfil, Mensajes (notification dot), Entrenadores, Especialistas, Q&A, Salir.
- [x] **Generic Layout** (`[sop_layout]`): 25/75 grid with sidebar + content area.

### ✅ Authentication & Registration
- [x] **Login Page** (`view-login.php`): Form with email/password, login shortcode, validation.
- [x] **Registration Page** (`view-register.php`): Multi-field form with role selection (Entrenador/Atleta).
- [x] **Custom Roles**: `entrenador` and `atleta` roles registered in WordPress.
- [x] **WordPress Admin Bar**: Hidden on frontend for non-admin users.

### ✅ Profile Tabs System (`view-profile-tabs.php`)
- [x] **Tab Navigation**: Horizontal pill buttons — Personal Info, Professional Info, Security, Preview, Settings.
- [x] **Tab Switching**: JavaScript-driven tab toggle with fade animation.

#### Profile Tab: Personal Info (`templates/tabs/personal.php`)
- [x] 4-column grid for basic fields (Nombre, Ubicación, Nacionalidad, Nacimiento).
- [x] Idiomas section nested within the ABOUT ME panel.

#### Profile Tab: Professional Info (`templates/tabs/professional.php`)
- [x] Professional description with edit toggle.
- [x] Physical attributes, social media links, dominant leg, height, weight, level.

#### Profile Tab: Security (`templates/tabs/security.php`)
- [x] Password change form, account security options.

#### Profile Tab: Preview (`templates/tabs/preview.php`)
- [x] **Light Theme Toggle**: `sop-preview-mode` class on `<body>` switches the entire UI to light mode.
- [x] **Profile Header**: `profile1.png` image, star rating (★★★★★), name, Grupo A, Nivel/Nacionalidad/Edad/Club tags.
- [x] **QUIEN SOY**: Text card with user description.
- [x] **MI COMPOSICION**: Tags for Pierna dominante, Peso, Nivel, Altura, Cirquix, Pecho, Brazos, Pierna.
- [x] **REPORTE MEDICO / ESPECIALISTA**: Full-width text card.
- [x] **RRSS**: Social icons (YouTube, LinkedIn, Instagram, Zap) with black backgrounds.
- [x] **REVIEWS**: Full review system — star distribution bars (orange), filter pills (Todas/Positivas/Neutras/Negativas), 2-column review cards grid with avatars (`img_review.png`).
- [x] **Light Mode Overrides** (`preview.css`): White header/sidebar, navy tab buttons, footer gradient, all containers forced to `#f0f2f7`.

#### Profile Tab: Settings (`templates/tabs/settings.php`)
- [x] Account settings and preferences.

### ✅ Trainer Directory (`view-trainer-directory.php`)
- [x] **Modular Architecture**: View assembles reusable components via `include`.
- [x] **Dark Premium Card Design**: Navy gradient background, `coach.png` image, star rating, experience/idioma badges, name/role, description, Nivel/UEFA tags, Cupos (gold), Focus tags (pill-shaped).
- [x] **Pricing Card Variant**: 2x2 grid (80$ Semanal, 160$ Mensual, 540$ Trimestral, 1.100$ Anual), Añadir a favoritos + Ver perfil buttons.
- [x] **Filter Top Bar**: Results count, ★ Favoritos button, Deporte dropdown, Name search, Filtrar, Ordenar por.
- [x] **Paginator**: Numbered circular buttons with active state, arrow navigation, ellipsis for long page ranges.

### ✅ Reusable Components (`templates/components/`)
| Component | File | Description |
|---|---|---|
| Filter Bar | `filter-bar.php` | Top bar with result count, search, filters |
| Trainer Card | `trainer-card.php` | Dark premium coach card with all data |
| Pricing Card | `pricing-card.php` | 4-tier subscription pricing grid |
| Paginator | `paginator.php` | Dynamic numbered pagination |

### ✅ CSS Architecture (`assets/css/`)
| File | Purpose |
|---|---|
| `base.css` | Global variables, resets, body styles, button classes |
| `components/header.css` | Global header (CSS Grid layout) |
| `components/footer.css` | Global footer (grid columns) |
| `components/sidebar.css` | Sidebar menu (sticky, dark navy) |
| `components/tabs.css` | Tab navigation + tab panel styling |
| `components/forms.css` | Input/label/button predefined styles |
| `components/trainer-card.css` | Trainer cards, pricing card, filter bar, paginator |
| `components/preview.css` | Preview tab + light theme overrides |
| `components/security.css` | Security tab specific styles |
| `components/settings.css` | Settings tab specific styles |

### ✅ Landing Page
- [x] **Hero Section** (`view-hero-landing.php`): Landing hero content.

### ✅ Mock Financial Simulation
- [x] **Checkout Simulado** (`view-mock-checkout.php`): AJAX-based plan preparation and secure redirection.
- [x] **Transaction Logs** (`sop_mock_transactions_log`): Persistent mock logs stored in `wp_options`.
- [x] **Subscription CPT**: Automated creation of `subscription` post type for administrative visibility.
- [x] **Solicitude Panel** (`view-solicitudes.php`): Full Accept/Reject flow with funds release simulation and meta-data synchronization.

### ✅ Maintenance & Documentation
- [x] **Code Cleanup**: Removed all debugging remnants (`console.log`, `var_dump`), commented-out blocks, and redundant files (`preview-backup.css`).
- [x] **File Inventory** (`FILE_INVENTORY.md`): Centralized documentation of all plugin files and their purposes.
- [x] **Standardization**: Formalized placeholder messages and i18n usage across views.

---

## 2. Core Architecture Reference
### Fullstack Application (WordPress 6.7)
- **Primary Plugin**: `wp-content/plugins/sistema-pro/` handles all visual layouts, shortcodes, and frontend CSS rendering using an MVC pattern.
- **Backend Logic**: MU-Plugins (`wp-content/mu-plugins/modules/`) are used strictly for heavy backend-only logic (like Traceability/Auditing).
- **Data**: ACF for custom fields and native WordPress Taxonomies.
- **Roles**: Custom roles for `entrenador` and `atleta`.

---

## 3. Immediate Roadmap (Next Phases)

### Phase 1: Data Integration & Dynamic Content
- [ ] **Connect Profile Tabs to Real Data**: Replace hardcoded values with `get_user_meta()` for all profile fields.
- [ ] **Trainer Card Dynamic Data**: Fetch real experience, idioma, level, cupos from user meta/taxonomies.
- [ ] **Pagination Logic**: Implement real WP_Query pagination for the trainer directory.
- [ ] **Search & Filters**: Wire up the filter bar to actual query parameters.

### Phase 2: Refinement & Advanced Logic
- [x] **Checkout Simulado**: Completed logic for plan selection and mock payment.
- [x] **Solicitude Flow**: Completed logic for trainer approval.
- [ ] **Single Coach View**: Implement the dynamic view for visiting a single coach's profile.
- [ ] **Media Handling**: Video/image content from WordPress inside the coach profile.

### Phase 3: Messaging & Q&A
- [ ] **Message Center**: Interactive messaging between athletes and coaches.
- [ ] **Q&A Section**: Community Q&A feature.

### Phase 4: Reviews System
- [ ] **Dynamic Reviews**: Connect reviews section to actual stored review data.
- [ ] **Review Submission**: Allow athletes to submit reviews for coaches.

---

## 4. Anti-Hallucination Checklist for AI
1. **Never** guess a path. Check `includes/Views/` and `templates/` first.
2. **Never** use Tailwind. Check the CSS in `assets/css/base.css` and `assets/css/components/`.
3. **Always** check `sistema-pro` plugin structure. All UI is PHP with modular CSS.
4. **Never** generate images. The user has all assets; always ask for them.
5. **Always** modularize: New UI sections go in `templates/components/` or `templates/tabs/`.
6. **Always** use predefined CSS classes (`.sop-label`, `.sop-input`, `.sop-btn-blue`, etc.) from `forms.css`.
