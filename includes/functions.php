<?php
session_start();

/**
 * Generate a CSRF token and store it in the session if not already present.
 */
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token against the session.
 */
function verify_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * CSRF protection middleware for POST requests.
 */
function csrf_guard()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            die("CSRF validation failed. Possible cross-site request forgery.");
        }
    }
}


// Define the base path for the project. 
// If running in a subdirectory like /bca-project/, this should be /bca-project/
if (!defined('BASE_URL')) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_dir = str_replace('\\', '/', dirname(dirname($script_name)));
    define('BASE_URL', rtrim($base_dir, '/') . '/');
}


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
            case 'parent':
                header("Location: parent/dashboard.php");
                break;
            default:
                header("Location: dashboard.php");
                break;
        }
        exit();
    }
}

/**
 * Require login to access a page.
 */
function require_login($role = null)
{
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "login.php");
        exit();
    }

    if ($role && $_SESSION['role'] !== $role) {
        set_flash_message('error', 'Access Denied: You do not have permission to view this page.');
        header("Location: " . BASE_URL . "login.php");
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
    return BASE_URL . "assets/" . ltrim($path, '/');
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
        $type = $flash['type'];
        $message = $flash['message'];
        $bg = $type === 'success' ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-500/20 text-emerald-600' : 'bg-rose-50 dark:bg-rose-500/10 border-rose-500/20 text-rose-600';
        $icon = $type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';

        echo "<div class='flex items-center p-4 mb-6 rounded-2xl border {$bg} transition-all duration-500 transform animate-in fade-in slide-in-from-top-4' role='alert'>
                <i class='fas {$icon} mr-3 text-lg'></i>
                <span class='text-xs font-bold uppercase tracking-widest'>{$message}</span>
              </div>";
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