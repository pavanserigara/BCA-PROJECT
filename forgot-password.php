<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// If already logged in, redirect to dashboard
redirect_if_logged_in();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);

    if (empty($email)) {
        $error_message = "Please enter your email address.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // In a real system, we would send an email here.
            // For this project, we'll simulate it by showing a success message.
            $success_message = "A password reset link has been sent to your email address.";
        } else {
            $error_message = "No account found with that email address.";
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
    <title>Forgot Password | <?php echo $settings['college_name']; ?></title>
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

        @keyframes pulse-glow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
    </style>
</head>

<body class="login-bg min-h-screen flex items-center justify-center p-4 relative grid-bg text-slate-800 antialiased">

    <!-- Ambient Orbs -->
    <div class="orb absolute w-96 h-96 bg-indigo-600/20 top-[-10%] left-[-5%] -z-10"></div>
    <div class="orb absolute w-80 h-80 bg-violet-600/15 bottom-[-10%] right-[-5%] -z-10" style="animation-delay: 2s;"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="glass-card rounded-[2.5rem] p-10 md:p-12 w-full glow-indigo relative overflow-hidden">
            
            <!-- Decorative corner -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-indigo-600/10 rounded-full"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-center mb-10">
                    <div class="w-16 h-16 bg-indigo-600 rounded-3xl flex items-center justify-center text-white font-black text-3xl italic shadow-2xl shadow-indigo-600/30">V</div>
                </div>

                <div class="mb-12 text-center">
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-3">Recovery Portal</p>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Forgot Password?</h2>
                    <p class="text-slate-600 font-medium text-sm">Enter your email and we'll send you recovery instructions.</p>
                </div>

                <?php if ($error_message): ?>
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-5 rounded-2xl mb-8 flex items-center backdrop-blur-sm">
                        <i class="fas fa-exclamation-circle text-lg mr-4"></i>
                        <p class="text-sm font-bold"><?php echo $error_message; ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 p-5 rounded-2xl mb-8 flex items-center backdrop-blur-sm">
                        <i class="fas fa-check-circle text-lg mr-4"></i>
                        <p class="text-sm font-bold"><?php echo $success_message; ?></p>
                    </div>
                <?php endif; ?>

                <form action="forgot-password.php" method="POST" class="space-y-7">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3" for="email">
                            Registered Email Address
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input type="email" id="email" name="email"
                                class="input-focus w-full !pl-12 pr-6 py-4 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none font-bold text-sm"
                                placeholder="Enter your email" required>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn-login w-full py-5 text-white rounded-2xl font-black text-xs tracking-[0.2em] uppercase flex items-center justify-center space-x-3">
                        <span>Send Reset Link</span>
                        <i class="fas fa-paper-plane text-[10px]"></i>
                    </button>
                    
                    <div class="text-center mt-6">
                        <a href="login.php" class="text-[10px] font-black text-indigo-600 hover:text-indigo-500 uppercase tracking-widest transition-colors flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
