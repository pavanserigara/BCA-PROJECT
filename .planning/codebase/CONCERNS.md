# Concerns

## Security
- **CSRF**: No clear CSRF protection mechanism (tokens) found in form handling logic.
- **Direct Access**: While role guards are present, some files might be accessible if guards are missing or inconsistent.
- **SQL Injection**: Prepared statements are used in `functions.php`, but consistency across all modules needs verification.

## Architecture
- **CDN Dependency**: Relying on CDNs (Tailwind, Font Awesome) might cause issues in offline or high-latency environments.
- **Logic Duplication**: Some UI logic (like the sidebar) might be duplicated across different roles' `header.php` files instead of being shared.
- **Scalability**: Multi-page application structure can become harder to manage as the number of modules grows.

## Maintainability
- **Hardcoded Paths**: Some paths are hardcoded or rely on `BASE_URL` which needs careful configuration.
- **Documentation**: Limited inline documentation in complex modules.
