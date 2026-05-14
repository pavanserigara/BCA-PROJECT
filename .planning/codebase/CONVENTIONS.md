# Conventions

## PHP Style
- **Naming**: Functions use `snake_case`. Variables use `snake_case`.
- **Database**: Use PDO with prepared statements for security.
- **Inclusions**: `require_once` is preferred for core files; `include` for UI components.

## HTML/CSS Style
- **Styling**: Tailwind CSS utility classes are heavily used.
- **Layout**: Flexbox and Grid based layouts.
- **Theme**: Dark mode supported via `dark:` variant classes and class-based toggle on `<html>`.

## Logic Patterns
- **Role Guarding**: Always check roles at the start of role-specific pages.
- **Feedback**: Use `set_flash_message()` and `display_flash_message()` for user notifications.
- **Sanitization**: Use `sanitize()` helper for user-provided data.

## Directory Structure
- Follows a "Module per Folder" approach for complex features.
