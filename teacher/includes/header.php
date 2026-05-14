<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$settings = get_college_settings($pdo);
$page_title = isset($page_title) ? $page_title : 'Faculty Dashboard';
$current_page = basename($_SERVER['PHP_SELF']);
$profile_pic = $_SESSION['profile_pic'] ?? '';
$profile_pic_url = 'default_profile.svg';

if (!empty($profile_pic)) {
    if (is_file(__DIR__ . '/../../uploads/profiles/' . $profile_pic)) {
        $profile_pic_url = '../uploads/profiles/' . $profile_pic;
    } elseif (is_file(__DIR__ . '/../../assets/images/' . $profile_pic)) {
        $profile_pic_url = '../assets/images/' . $profile_pic;
    }
} else {
    $profile_pic_url = '../assets/images/default_profile.svg';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Faculty Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Dark mode logic
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 'inter': ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#EEF2FF',100:'#E0E7FF',200:'#C7D2FE',300:'#A5B4FC',400:'#818CF8',500:'#6366F1',600:'#4F46E5',700:'#4338CA',800:'#3730A3',900:'#312E81' },
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0,0,0,0.05)',
                        'premium': '0 10px 30px -5px rgba(0,0,0,0.1)',
                    }
                }
            }
        }
    </script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .dark .glass { background: rgba(15, 23, 42, 0.7); }
        .sidebar-item-active { @apply bg-primary-600 text-white shadow-lg shadow-primary-500/30; }
        .sidebar-item { @apply flex items-center space-x-3 px-4 py-3 rounded-2xl transition-all duration-200; }
    </style>
</head>

<body class="bg-[#F8FAFC] dark:bg-[#0F172A] text-slate-700 dark:text-slate-300 antialiased">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:sticky inset-y-0 left-0 bg-white dark:bg-[#1E293B] w-[280px] z-50 transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out flex flex-col border-r border-slate-100 dark:border-slate-800 h-screen">
            
            <div class="p-8 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-primary-500/40 italic">V</div>
                    <span class="font-bold text-xl tracking-tight text-slate-800 dark:text-white">VidyaSetu</span>
                </div>
                <button id="close-sidebar" class="lg:hidden text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="px-6 mb-8">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                    <div class="flex items-center space-x-3">
                        <img src="<?php echo htmlspecialchars($profile_pic_url); ?>" class="w-10 h-10 rounded-xl object-cover" alt="Faculty">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white truncate"><?php echo explode(' ', $_SESSION['full_name'])[0]; ?></p>
                            <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest">Faculty</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto px-4 pb-8 space-y-8 custom-sidebar-scroll">
                <div>
                    <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Main Menu</p>
                    <div class="space-y-1">
                        <?php
                        $teach_links = [
                            ['dashboard.php', 'fa-grip-vertical', 'Overview'],
                            ['attendance-take.php', 'fa-calendar-check', 'Attendance'],
                            ['attendance-report.php', 'fa-chart-pie', 'Reports'],
                            ['materials.php', 'fa-book-open-reader', 'Study Vault'],
                            ['subjects.php', 'fa-book-bookmark', 'My Subjects'],
                            ['assignments.php', 'fa-cloud-arrow-up', 'Assignments'],
                            ['marks-entry.php', 'fa-chart-simple', 'Gradebook'],
                        ];
                        foreach ($teach_links as $link):
                            $active = ($current_page === $link[0]);
                        ?>
                            <a href="<?php echo $link[0]; ?>" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition-all duration-200 group <?php echo $active ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary-600'; ?>">
                                <div class="w-8 flex justify-center">
                                    <i class="fas <?php echo $link[1]; ?> text-sm"></i>
                                </div>
                                <span class="font-bold text-sm"><?php echo $link[2]; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Organization</p>
                    <div class="space-y-1">
                        <?php
                        $campus_links = [
                            ['timetable.php', 'fa-calendar-days', 'Schedules'],
                            ['notices.php', 'fa-bullhorn', 'Notice Board'],
                            ['leave-apply.php', 'fa-paper-plane', 'Absence Request'],
                            ['messaging.php', 'fa-message', 'In-Box'],
                            ['profile.php', 'fa-circle-user', 'My Settings'],
                        ];
                        foreach ($campus_links as $link):
                            $active = ($current_page === $link[0]);
                        ?>
                            <a href="<?php echo $link[0]; ?>" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition-all duration-200 group <?php echo $active ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary-600'; ?>">
                                <div class="w-8 flex justify-center">
                                    <i class="fas <?php echo $link[1]; ?> text-sm"></i>
                                </div>
                                <span class="font-bold text-sm"><?php echo $link[2]; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="p-6 mt-auto border-t border-slate-100 dark:border-slate-800/50">
                <a href="../logout.php" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 active:scale-95 transition-all">
                    <div class="w-8 flex justify-center">
                        <i class="fas fa-arrow-right-from-bracket text-sm"></i>
                    </div>
                    <span class="font-bold text-sm">Log Out</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen min-w-0">

            <!-- Top Navbar -->
            <header class="h-16 lg:h-20 flex items-center justify-between px-6 lg:px-10 sticky top-0 z-30 transition-all duration-300 bg-white/80 dark:bg-[#0F172A]/80 backdrop-blur-xl border-b border-slate-100 dark:border-slate-800/50" id="top-nav">
                <div class="flex items-center space-x-4">
                    <button id="toggle-sidebar" class="lg:hidden w-10 h-10 bg-white dark:bg-slate-800 rounded-xl shadow-soft flex items-center justify-center text-slate-500 border border-slate-100 dark:border-slate-700">
                        <i class="fas fa-bars-staggered text-sm"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-bold text-slate-800 dark:text-white leading-tight"><?php echo $page_title; ?></h1>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mt-1">Faculty Hub</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="w-9 h-9 sm:w-10 sm:h-10 bg-white dark:bg-slate-800 rounded-xl shadow-soft text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all flex items-center justify-center border border-slate-100 dark:border-slate-700">
                        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-sm"></i>
                        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-sm"></i>
                    </button>

                    <div class="h-5 w-px bg-slate-200 dark:bg-slate-700"></div>

                    <a href="profile.php" class="flex items-center space-x-3 group active:scale-95 transition-transform">
                        <div class="text-right hidden md:block">
                            <p class="text-xs font-bold text-slate-800 dark:text-white group-hover:text-primary-600 transition-colors uppercase tracking-tight"><?php echo explode(' ', $_SESSION['full_name'])[0]; ?></p>
                            <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500">VIEW PROFILE</p>
                        </div>
                        <div class="relative">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl overflow-hidden ring-2 ring-primary-500/10 group-hover:ring-primary-500 transition-all">
                                <img src="<?php echo htmlspecialchars($profile_pic_url); ?>" class="w-full h-full object-cover" alt="Profile">
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-[#0F172A] rounded-full"></div>
                        </div>
                    </a>
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-10">