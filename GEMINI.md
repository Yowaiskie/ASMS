# ASMS Project Context & Rules

> [!IMPORTANT]
> **STRICT ADHERENCE & PROCESS INTEGRITY:** 
> Do NOT modify the established MVC architecture, Repository pattern, core folder structure, or the existing database schema unless explicitly and specifically directed by the user for a feature update. Always follow the conventions defined in this document without deviation.

This document provides essential information for working on the Altar Servers Management System (ASMS), with a focus on integrating Figma designs using the Model Context Protocol (MCP).

## 🚀 Project Overview

ASMS is a custom PHP MVC application designed for managing Parish Altar Server Ministries. It features multi-role authentication, mass scheduling, attendance tracking, and reporting.

- **Backend:** PHP 8.x (Custom OOP MVC Framework)
- **Frontend:** Tailwind CSS v4
- **Database:** MySQL (Singleton PDO)
- **Icons:** Phosphor Icons & HeroIcons
- **Build System:** npm (Tailwind CLI)

---

## 🎨 Design System Structure

### 1. Token Definitions
- **Colors:**
  - Primary color is primarily defined as `--color-primary: #1e63d4` in `src/input.css` and `tailwind.config.js`.
  - However, `app/views/layouts/main.php` overrides this with `--primary: #2563eb`. **Always prefer the CSS variable `--primary` for consistent theme matching.**
  - Backgrounds typically use `slate` shades (e.g., `bg-slate-50`, `bg-slate-900/60`).
- **Typography:**
  - Main font is `Inter` (weights 400-900), loaded via Google Fonts in `main.php`.
- **Spacing:** Standard Tailwind spacing scales are used throughout the project.

### 2. Component Library
- **Architecture:** The project uses a "Layout + Views + Partials" architecture.
- **Key Files:**
  - `app/views/layouts/main.php`: The global wrapper containing `<head>`, sidebar, modals, and loaders.
  - `app/views/partials/sidebar.php`: Navigation structure with role-based visibility.
  - `app/views/pages/`: Individual view templates (e.g., `dashboard.php`, `attendance/index.php`).
- **Documentation:** No formal Storybook; refer to `app/views/layouts/main.php` for global modal and loader patterns.

### 3. Frameworks & Libraries
- **Tailwind CSS v4:** Uses the new CSS-first configuration in `src/input.css`.
- **Phosphor Icons:** Loaded via CDN in `main.php`. Use `<i class="ph-bold ph-..."></i>`.
- **HeroIcons:** Used as inline SVG paths within PHP logic (e.g., `sidebar.php`).
- **Chart.js:** Used for analytics in dashboard and reports.

### 4. Asset Management
- **Pathing:** Use the `URLROOT` constant for all asset references.
  - Images: `<?= URLROOT ?>/images/parish-logo.png`
  - Uploads: `<?= URLROOT ?>/uploads/profiles/...`
- **Directories:**
  - `public/images/`: Static UI assets.
  - `public/uploads/`: Dynamic user-generated content.

### 5. Icon System
- **Phosphor Icons (Preferred):** Use the `ph-` class prefix.
- **HeroIcons:** Used for sidebar navigation icons as raw SVGs.
- **Naming:** Follow standard Phosphor/HeroIcon naming conventions.

### 6. Styling Approach
- **Utility-First:** Use Tailwind CSS classes for almost all styling.
- **Custom CSS:** Found in `<style>` blocks within `app/views/layouts/main.php` for complex animations (loaders, spinners) and global overrides (scrollbars).
- **Responsive Design:** Mobile-first approach. Desktop sidebar is hidden on small screens (`md:flex`), replaced by a mobile-specific sidebar and overlay in `main.php`.

---

## 🛠️ Development Workflow

### Building Assets
```bash
# Install dependencies
npm install

# Build Tailwind CSS once
npm run build

# Watch for changes during development
npm run watch
```

### Routing & Controllers
- Routes are defined/handled by `app/core/Router.php`.
- Controllers reside in `app/controllers/`.
- Repositories handle data access in `app/repositories/`.

### Security Conventions
- **XSS Protection:** Always use the `h()` helper function when echoing user-controlled data: `<?= h($user->name) ?>`.
- **CSRF Protection:** Include `<?php csrf_field(); ?>` in every POST form.
- **Flash Messages:** Use `setFlash('name', 'message')` and `flash('name')` for UI feedback.
- **Confirmation Modals:** Use the global JS function `showConfirm(message, title, callback)` defined in `main.php`.

---

## 📐 Figma Integration Rules (MCP)

When translating Figma designs to code:
1. **Map to Tailwind v4:** Convert design tokens directly to Tailwind utility classes.
2. **Use URLROOT:** Ensure all `src` attributes for images use the `<?= URLROOT ?>` constant.
3. **Responsive Mapping:** Map Figma layouts to Tailwind's `md:` breakpoint (768px) to align with the existing sidebar logic.
4. **Interactive States:** Use `transition-all` and `active:scale-[0.98]` for buttons to match existing UI patterns.
5. **Icon Consistency:** Prefer Phosphor Icons unless the design explicitly uses HeroIcons.
