# Integrations

## External Services
- **Tailwind CSS CDN**: `https://cdn.tailwindcss.com` for dynamic styling.
- **Font Awesome CDN**: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css` for iconography.
- **Google Fonts**: `https://fonts.googleapis.com` for typography (Plus Jakarta Sans).

## Internal Integrations
- **Database Connection**: Managed via `includes/db.php` using PDO.
- **Core Functions**: Shared logic in `includes/functions.php`.
- **Role-based Access**: Tight integration between session roles and directory access (`admin/`, `teacher/`, `student/`).
- **College Settings**: Global settings fetched from the `settings` table in MySQL.

## Media & Assets
- **Asset Helper**: `asset()` function in PHP for generating consistent URLs.
- **Uploads**: Files stored in the `uploads/` directory, managed via PHP file system operations.
