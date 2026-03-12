<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// If already logged in, redirect to dashboard
redirect_if_logged_in();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        if (authenticate($pdo, $username, $password)) {
            redirect_if_logged_in();
        } else {
            $error_message = "Invalid username/email or password.";
        }
    }
}

$settings = get_college_settings($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login |
        <?php echo $settings['college_name']; ?>
    </title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-gradient {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div
        class="flex w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden animate__animated animate__fadeInUp">

        <!-- Left Side: Branding/Info (Hidden on small screens) -->
        <div class="hidden md:flex md:w-1/2 login-gradient p-12 text-white flex-col justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-4">VidyaSetu</h1>
                <p class="text-blue-100 text-lg leading-relaxed">
                    The bridge between learning and excellence. Manage your college operations with ease and efficiency.
                </p>
            </div>

            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Secure Access</h4>
                        <p class="text-sm text-blue-100">Role-based security system</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Real-time Analytics</h4>
                        <p class="text-sm text-blue-100">Tracking every student's progress</p>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-sm text-blue-200">©
                    <?php echo date('Y'); ?>
                    <?php echo $settings['college_name']; ?>
                </p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-12">
            <div class="mb-10 text-center md:text-left">
                <h2 class="text-3xl font-bold text-slate-800 mb-2">Welcome Back!</h2>
                <p class="text-slate-500">Please sign in to your account</p>
            </div>

            <?php if ($error_message): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="text-sm">
                        <?php echo $error_message; ?>
                    </p>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2" for="username">
                        Username or Email
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username"
                            class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                            placeholder="Enter your username" required>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-semibold text-slate-700" for="password">
                            Password
                        </label>
                        <a href="forgot-password.php"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Forgot?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password"
                            class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                            placeholder="********" required>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember"
                        class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="remember" class="ml-2 text-sm text-slate-600">Remember me</label>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all shadow-lg hover:shadow-indigo-200">
                    Sign In
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100">
                <p class="text-center text-sm text-slate-500">
                    Need help? Contact college administration.
                </p>
            </div>
        </div>
    </div>

</body>

</html>