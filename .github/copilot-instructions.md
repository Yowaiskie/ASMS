# ASMS Copilot Instructions

## Build, test, lint
- Build Tailwind CSS: `npm run build`
- Watch Tailwind CSS: `npm run watch`
- Tests: no automated test runner is configured (`npm test` exits with "no test specified"); there is no single-test command.
- Lint: no lint script is configured in package.json.

## High-level architecture
- Entry point is `public/index.php`: loads config/helpers, sets up the autoloader, instantiates `App\Core\Router`, loads `app/routes.php`, then calls `resolve()`.
- Routes are defined in `app/routes.php` using `$router->get()` / `$router->post()` and map to controller actions; dynamic params use `:id`-style segments handled in `App\Core\Router`.
- Controllers in `app/controllers/` extend `App\Core\Controller`, which renders pages from `app/views/pages/` inside the shared layout `app/views/layouts/main.php` and injects global system settings.
- Data access goes through repositories in `app/repositories/` implementing `App\Interfaces\RepositoryInterface`, backed by the `App\Core\Database` singleton (PDO).
- Domain entities live in `app/models/`; views are plain PHP templates with partials (e.g., sidebar) and Tailwind-driven UI.

## Key conventions
- Use `h()` from `app/helpers/functions.php` to escape user-controlled output.
- Include CSRF protection on POST forms using `csrf_field()` and validate with `verify_csrf()` (Controller has `verifyCsrf()` helper).
- Use `setFlash()` / `flash()` for user-facing messages and `logAction()` for audit logging.
- Use `URLROOT` for all asset and link paths; it is dynamically derived in `app/config/config.php`.
