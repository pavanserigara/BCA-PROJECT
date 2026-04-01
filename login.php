<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// If already logged in, redirect to dashboard
redirect_if_logged_in();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        if (authenticate($pdo, $username, $password)) {
            if ($remember) {
                setcookie('remember_user', base64_encode($username), time() + (86400 * 30), "/");
            } else {
                setcookie('remember_user', '', time() - 3600, "/");
            }
            redirect_if_logged_in();
        } else {
            $error_message = "Invalid username/email or password.";
        }
    }
}

// Pre-fill username if cookie exists
$saved_username = isset($_COOKIE['remember_user']) ? base64_decode($_COOKIE['remember_user']) : '';

$settings = get_college_settings($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo $settings['college_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .login-bg {
            background: radial-gradient(1200px 600px at 15% 20%, rgba(99, 102, 241, 0.18), transparent 55%),
                radial-gradient(900px 500px at 85% 30%, rgba(244, 63, 94, 0.12), transparent 55%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 60%, #f8fafc 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(148, 163, 184, 0.25);
        }

        .glow-indigo {
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.10), 0 8px 22px rgba(79, 70, 229, 0.10);
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .float-animation-delay {
            animation: float 6s ease-in-out 2s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(3deg);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                opacity: 0.4;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .input-focus {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-focus:focus {
            background: #ffffff;
            border-color: rgba(79, 70, 229, 0.55);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-login:hover {
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4), 0 0 60px rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }

        .btn-login:active {
            transform: translateY(0) scale(0.98);
        }

        .grid-bg {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.06) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .orb {
            border-radius: 50%;
            filter: blur(80px);
            animation: pulse-glow 4s ease-in-out infinite;
        }
    </style>
</head>

<body class="login-bg min-h-screen flex items-center justify-center p-4 relative grid-bg text-slate-800 antialiased overflow-x-hidden">

    <!-- Ambient Orbs -->
    <div class="orb absolute w-96 h-96 bg-indigo-600/20 top-[-10%] left-[-5%] -z-10"></div>
    <div class="orb absolute w-80 h-80 bg-violet-600/15 bottom-[-10%] right-[-5%] -z-10" style="animation-delay: 2s;"></div>
    <div class="orb absolute w-64 h-64 bg-blue-600/10 top-[40%] right-[20%] -z-10" style="animation-delay: 4s;"></div>

    <div class="flex w-full max-w-6xl relative z-10">

        <!-- Left Side: Premium Branding -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-16 relative">

            <!-- Floating decorative elements -->
            <div
                class="absolute top-20 right-20 w-20 h-20 glass-card rounded-3xl flex items-center justify-center float-animation">
                <i class="fas fa-graduation-cap text-indigo-600 text-2xl"></i>
            </div>
            <div
                class="absolute bottom-40 left-10 w-16 h-16 glass-card rounded-2xl flex items-center justify-center float-animation-delay">
                <i class="fas fa-book-open text-violet-400 text-xl"></i>
            </div>
            <div class="absolute top-1/2 right-8 w-14 h-14 glass-card rounded-2xl flex items-center justify-center float-animation"
                style="animation-delay: 1s;">
                <i class="fas fa-chart-line text-blue-400 text-lg"></i>
            </div>

            <!-- Main branding -->
            <div>
                <div class="flex items-center space-x-4 mb-20">
                    <div
                        class="w-16 h-16 bg-indigo-600 rounded-3xl flex items-center justify-center text-white font-black text-3xl italic shadow-2xl shadow-indigo-600/30 glow-indigo">
                        V</div>
                    <div>
                        <h1 class="text-4xl font-black text-slate-900 tracking-tight italic">VidyaSetu</h1>
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em]">College Management
                            System</p>
                    </div>
                </div>

                <h2 class="text-6xl font-black text-slate-900 leading-[1.1] tracking-tight mb-8">
                    The Bridge<br>
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-violet-400 to-purple-400">Between
                        Learning</span><br>
                    & Excellence.
                </h2>

                <p class="text-slate-600 text-lg font-medium leading-relaxed max-w-md">
                    A unified platform to manage academic operations, student performance, and institutional workflow.
                </p>
            </div>

            <!-- Feature pills -->
            <div class="space-y-5">
                <div class="flex items-center space-x-5">
                    <div class="w-12 h-12 glass-card rounded-2xl flex items-center justify-center">
                        <i class="fas fa-shield-halved text-indigo-400"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Role-Based Access Control</h4>
                        <p class="text-xs text-slate-600 font-medium">Admin, Faculty, and Student portals with dedicated
                            dashboards</p>
                    </div>
                </div>
                <div class="flex items-center space-x-5">
                    <div class="w-12 h-12 glass-card rounded-2xl flex items-center justify-center">
                        <i class="fas fa-bolt text-amber-400"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Real-Time Analytics</h4>
                        <p class="text-xs text-slate-600 font-medium">Live attendance tracking, financial treasury, and
                            performance metrics</p>
                    </div>
                </div>
                <div class="flex items-center space-x-5">
                    <div class="w-12 h-12 glass-card rounded-2xl flex items-center justify-center">
                        <i class="fas fa-calendar-check text-emerald-400"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Academic Scheduling</h4>
                        <p class="text-xs text-slate-600 font-medium">Timetable management, exam scheduling, and
                            curriculum flow</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 font-bold">© <?php echo date('Y'); ?>
                    <?php echo $settings['college_name']; ?></p>
                <div class="flex items-center space-x-4">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">System Online</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center">
            <div class="glass-card rounded-[2.5rem] p-10 md:p-12 w-full max-w-md glow-indigo relative overflow-hidden">

                <!-- Decorative corner -->
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-indigo-600/10 rounded-full"></div>
                <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-violet-600/10 rounded-full"></div>

                <div class="relative z-10">
                    <!-- Mobile logo -->
                    <div class="lg:hidden flex items-center justify-center mb-10">
                        <div
                            class="w-16 h-16 bg-indigo-600 rounded-3xl flex items-center justify-center text-white font-black text-3xl italic shadow-2xl shadow-indigo-600/30">
                            V</div>
                    </div>

                    <div class="mb-12">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-3">Authentication
                            Portal</p>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Welcome Back</h2>
                        <p class="text-slate-600 font-medium text-sm">Sign in to access your dashboard</p>
                    </div>

                    <?php if ($error_message): ?>
                        <div
                            class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-5 rounded-2xl mb-8 flex items-center backdrop-blur-sm">
                            <i class="fas fa-exclamation-circle text-lg mr-4"></i>
                            <p class="text-sm font-bold"><?php echo $error_message; ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="space-y-7">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3"
                                for="username">
                                Username or Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <i class="fas fa-user text-sm"></i>
                                </span>
                                <input type="text" id="username" name="username"
                                    class="input-focus w-full !pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none font-bold text-sm"
                                    placeholder="Enter your username" value="<?php echo $saved_username; ?>" required>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest"
                                    for="password">
                                    Password
                                </label>
                                <a href="forgot-password.php"
                                    class="text-[10px] font-black text-indigo-600 hover:text-indigo-500 uppercase tracking-widest transition-colors">Forgot?</a>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                <input type="password" id="password" name="password"
                                    class="input-focus w-full !pl-12 !pr-12 py-4 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none font-bold text-sm"
                                    placeholder="••••••••" required>
                                <button type="button" onclick="togglePassword()"
                                    class="absolute inset-y-0 right-0 flex items-center pr-5 text-slate-400 hover:text-slate-600 transition-colors">
                                    <i class="fas fa-eye text-sm" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" id="remember" name="remember"
                                    class="w-4 h-4 rounded-md bg-white border-slate-300 text-indigo-600 focus:ring-indigo-500/30 cursor-pointer">
                                <span
                                    class="ml-3 text-xs text-slate-600 font-bold group-hover:text-slate-700 transition-colors">Remember
                                    session</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="btn-login w-full py-5 text-white rounded-2xl font-black text-xs tracking-[0.2em] uppercase flex items-center justify-center space-x-3">
                            <span>Authorize Access</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </button>
                    </form>

                    <div class="mt-10 pt-8 border-t border-white/5">
                        <p class="text-center text-xs text-slate-600 font-bold">
                            Need help? Contact <span
                                class="text-indigo-600 cursor-pointer hover:text-indigo-500">administration@vidyasetu.ac.in</span>
                        </p>
                    </div>

                    <!-- Quick access role indicators -->
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <div class="glass-card px-4 py-2 rounded-xl flex items-center space-x-2 cursor-pointer hover:bg-white transition-all group"
                            title="Admin Portal">
                            <i class="fas fa-user-shield text-[10px] text-indigo-600 group-hover:text-indigo-600"></i>
                            <span
                                class="text-[9px] font-black text-slate-400 uppercase tracking-wider group-hover:text-slate-700">Admin</span>
                        </div>
                        <div class="glass-card px-4 py-2 rounded-xl flex items-center space-x-2 cursor-pointer hover:bg-white transition-all group"
                            title="Faculty Portal">
                            <i
                                class="fas fa-chalkboard-teacher text-[10px] text-violet-600 group-hover:text-violet-600"></i>
                            <span
                                class="text-[9px] font-black text-slate-400 uppercase tracking-wider group-hover:text-slate-700">Faculty</span>
                        </div>
                        <div class="glass-card px-4 py-2 rounded-xl flex items-center space-x-2 cursor-pointer hover:bg-white transition-all group"
                            title="Student Portal">
                            <i
                                class="fas fa-user-graduate text-[10px] text-emerald-600 group-hover:text-emerald-600"></i>
                            <span
                                class="text-[9px] font-black text-slate-400 uppercase tracking-wider group-hover:text-slate-700">Student</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>