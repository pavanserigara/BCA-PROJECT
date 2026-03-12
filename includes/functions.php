<?php
session_start();

/**
 * Check if the user is logged in.
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Verify if the logged-in user has the required role.
 */
function has_role($role)
{
    return is_logged_in() && $_SESSION['role'] === $role;
}

/**
 * Redirect based on user role if logged in.
 */
function redirect_if_logged_in()
{
    if (is_logged_in()) {
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'student':
                header("Location: student/dashboard.php");
                break;
            case 'teacher':
                header("Location: teacher/dashboard.php");
                break;
            default:
                header("Location: dashboard.php");
                break;
        }
        exit();
    }
}

/**
 * Sanitize user input.
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input));
}

/**
 * Get the full path for assets.
 */
function asset($path)
{
    return "/assets/" . ltrim($path, '/');
}

/**
 * Flash messages helper.
 */
function set_flash_message($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function display_flash_message()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo "<div class='p-4 mb-4 text-sm rounded-lg " .
            ($flash['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') .
            "' role='alert'>{$flash['message']}</div>";
    }
}

/**
 * Function to get the current college settings.
 */
function get_college_settings($pdo)
{
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    return $stmt->fetch();
}

/**
 * Simple authentication function.
 */
function authenticate($pdo, $username, $password)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'active') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_pic'] = $user['profile_pic'];
            return true;
        }
    }
    return false;
}
?>