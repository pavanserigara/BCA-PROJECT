<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$settings = get_college_settings($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $settings['college_name']; ?> | VidyaSetu
    </title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .hero-pattern {
            background-image: radial-gradient(circle at 2px 2px, rgba(255, 255, 255, 0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="bg-indigo-950 font-inter">

    <!-- Navigation -->
    <nav class="sticky top-4 z-50 mx-auto max-w-7xl px-4 animate__animated animate__fadeInDown">
        <div class="glass-dark px-8 py-4 rounded-3xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 bg-gradient-to-tr from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center font-bold text-white">
                    V</div>
                <span class="text-white font-bold text-2xl tracking-tight">Vidya<span
                        class="text-indigo-400">Setu</span></span>
            </div>

            <div class="hidden md:flex items-center space-x-8 text-indigo-100/70 font-medium">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#about" class="hover:text-white transition-colors">About Us</a>
                <a href="#portals" class="hover:text-white transition-colors">Portals</a>
                <a href="#contact" class="hover:text-white transition-colors">Contact</a>
            </div>

            <div>
                <a href="login.php"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-2xl font-semibold shadow-lg shadow-indigo-500/20 transition-all transform hover:scale-105">
                    Sign In
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-60 px-4 overflow-hidden hero-pattern">
        <!-- Abstract Shapes -->
        <div
            class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl animate__animated animate__pulse animate__infinite animate__slower">
        </div>
        <div
            class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl animate__animated animate__pulse animate__infinite animate__slower">
        </div>

        <div class="max-w-7xl mx-auto text-center relative z-10">
            <h1
                class="text-5xl md:text-7xl font-bold text-white mb-8 leading-[1.1] animate__animated animate__fadeInUp">
                Modern Education,<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-blue-400">Smarter
                    Management</span>
            </h1>
            <p
                class="text-xl text-indigo-100/60 max-w-3xl mx-auto mb-12 animate__animated animate__fadeInUp animate__delay-100ms">
                VidyaSetu provides a comprehensive ecosystem for students, teachers, and administrators to focus on what
                matters most: Excellence in Education.
            </p>

            <div
                class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 animate__animated animate__fadeInUp animate__delay-200ms">
                <a href="login.php"
                    class="w-full sm:w-auto px-8 py-4 bg-white text-indigo-950 rounded-2xl font-bold text-lg hover:bg-indigo-50 transition-all flex items-center justify-center group">
                    Get Started <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="#features"
                    class="w-full sm:w-auto px-8 py-4 border border-indigo-500/30 text-white rounded-2xl font-bold text-lg hover:bg-white/5 transition-all">
                    Explore Features
                </a>
            </div>
        </div>
    </section>

    <!-- Stat Cards Overlay -->
    <section class="max-w-6xl mx-auto px-4 -mt-32 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="glass-dark p-8 rounded-[2rem] text-center border-indigo-500/20 hover:border-indigo-400/40 transition-all transform hover:-translate-y-2">
                <div class="text-indigo-400 text-3xl font-bold mb-2">5000+</div>
                <div class="text-indigo-100/60 font-semibold uppercase tracking-wider text-xs">Active Students</div>
            </div>
            <div
                class="glass-dark p-8 rounded-[2rem] text-center border-indigo-500/20 hover:border-indigo-400/40 transition-all transform hover:-translate-y-2">
                <div class="text-indigo-400 text-3xl font-bold mb-2">200+</div>
                <div class="text-indigo-100/60 font-semibold uppercase tracking-wider text-xs">Expert Faculty</div>
            </div>
            <div
                class="glass-dark p-8 rounded-[2rem] text-center border-indigo-500/20 hover:border-indigo-400/40 transition-all transform hover:-translate-y-2">
                <div class="text-indigo-400 text-3xl font-bold mb-2">50+</div>
                <div class="text-indigo-100/60 font-semibold uppercase tracking-wider text-xs">Courses</div>
            </div>
            <div
                class="glass-dark p-8 rounded-[2rem] text-center border-indigo-500/20 hover:border-indigo-400/40 transition-all transform hover:-translate-y-2">
                <div class="text-indigo-400 text-3xl font-bold mb-2">98%</div>
                <div class="text-indigo-100/60 font-semibold uppercase tracking-wider text-xs">Placement Rate</div>
            </div>
        </div>
    </section>

    <!-- Role Portals -->
    <section id="portals" class="py-32 bg-slate-50 mt-10 rounded-t-[5rem]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-20">
                <span class="text-indigo-600 font-bold tracking-widest uppercase text-sm">Portals</span>
                <h2 class="text-4xl font-bold text-slate-800 mt-4">Access Your Workspace</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Admin Card -->
                <div
                    class="bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-slate-100 group hover:border-indigo-500 transition-all transform hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-600 text-2xl mb-8 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4">Admin Portal</h3>
                    <p class="text-slate-500 mb-8">Manage the entire institution, track growth, and oversee departments
                        from a single dashboard.</p>
                    <a href="login.php"
                        class="text-indigo-600 font-bold flex items-center space-x-2 group-hover:space-x-4 transition-all uppercase tracking-wider text-sm">
                        <span>Sign In</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- Teacher Card -->
                <div
                    class="bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-slate-100 group hover:border-emerald-500 transition-all transform hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-600 text-2xl mb-8 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4">Faculty Portal</h3>
                    <p class="text-slate-500 mb-8">Upload assignments, manage attendance, and track student performance
                        effortlessly.</p>
                    <a href="login.php"
                        class="text-emerald-600 font-bold flex items-center space-x-2 group-hover:space-x-4 transition-all uppercase tracking-wider text-sm">
                        <span>Sign In</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- Student Card -->
                <div
                    class="bg-white p-10 rounded-3xl shadow-xl shadow-slate-200 border border-slate-100 group hover:border-blue-500 transition-all transform hover:-translate-y-2">
                    <div
                        class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-600 text-2xl mb-8 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-4">Student Portal</h3>
                    <p class="text-slate-500 mb-8">Access study materials, check results, pay fees, and view notices
                        from your personal dashboard.</p>
                    <a href="login.php"
                        class="text-blue-600 font-bold flex items-center space-x-2 group-hover:space-x-4 transition-all uppercase tracking-wider text-sm">
                        <span>Sign In</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center space-x-3 mb-8">
                <div
                    class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center font-bold text-white text-sm">
                    V</div>
                <span class="text-white font-bold text-xl tracking-tight">VidyaSetu</span>
            </div>
            <p class="mb-8 max-w-lg mx-auto">Providing state-of-the-art college administration solutions to empower the
                next generation of educators and students.</p>
            <div class="text-sm font-medium">
                ©
                <?php echo date('Y'); ?>
                <?php echo $settings['college_name']; ?>. All rights reserved.
            </div>
        </div>
    </footer>

</body>

</html>