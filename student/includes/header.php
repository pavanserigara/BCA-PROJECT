<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('student')) {
    header("Location: ../login.php");
    exit();
}

$settings = get_college_settings($pdo);
$page_title = isset($page_title) ? $page_title : 'Student Portal';
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
    <title><?php echo $page_title; ?> | VidyaSetu CMS</title>
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#f0f4ff', 100:'#e0e8ff', 200:'#c7d2fe', 300:'#a5b4fc', 400:'#818cf8', 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca', 800:'#3730a3', 900:'#312e81' },
                        slate: { 950: '#020617' }
                    },
                    boxShadow: {
                        'premium': '0 10px 40px -10px rgba(0,0,0,0.08), 0 20px 25px -5px rgba(0,0,0,0.03)',
                        'soft': '0 2px 15px -3px rgba(0,0,0,0.07), 0 4px 6px -2px rgba(0,0,0,0.05)',
                    }
                }
            }
        }

        // Apply dark mode immediately
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        .custom-sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 20px; }
        .dark .custom-sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); }
        .glass-navbar { backdrop-filter: blur(12px); background-color: rgba(255, 255, 255, 0.8); }
        .dark .glass-navbar { background-color: rgba(15, 23, 42, 0.8); }
    </style>
</head>

<body class="bg-[#F8FAFC] dark:bg-[#0F172A] text-slate-700 dark:text-slate-300 antialiased font-medium text-sm">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Modern Sidebar -->
        <aside id="sidebar" class="fixed lg:sticky top-0 left-0 h-screen w-72 bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 z-[60] flex flex-col transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out shadow-2xl lg:shadow-none">
            
            <div class="h-24 flex items-center px-8 flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-tr from-primary-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary-500/30 font-black text-xl italic">V</div>
                    <div>
                        <h1 class="text-lg font-extrabold text-slate-800 dark:text-white leading-none tracking-tight">Vidya<span class="text-primary-600">Setu</span></h1>
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">Student Hub v2</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 pb-8 space-y-8 custom-sidebar-scroll">
                <div>
                    <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Core Center</p>
                    <div class="space-y-1">
                        <?php
                        $main_links = [
                            ['dashboard.php', 'fa-grip-vertical', 'Overview'],
                            ['attendance.php', 'fa-calendar-check', 'Attendance'],
                            ['materials.php', 'fa-book-open-reader', 'Study Vault'],
                            ['assignments.php', 'fa-laptop-code', 'Workspace'],
                            ['results.php', 'fa-award', 'Results'],
                        ];
                        foreach ($main_links as $link):
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
                    <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Campus Life</p>
                    <div class="space-y-1">
                        <?php
                        $campus_links = [
                            ['timetable.php', 'fa-calendar-days', 'Schedules'],
                            ['library.php', 'fa-book-bookmark', 'Library'],
                            ['placements.php', 'fa-briefcase', 'Placements'],
                            ['logistics.php', 'fa-truck-fast', 'Logistics'],
                            ['events.php', 'fa-sparkles', 'Festivals'],
                            ['fees.php', 'fa-wallet', 'Billing'],
                            ['id-card.php', 'fa-id-card', 'Digital ID'],
                            ['complaints.php', 'fa-hand-holding-heart', 'Support'],
                            ['notices.php', 'fa-bullhorn', 'Broadcasts'],
                            ['messaging.php', 'fa-message', 'In-Box'],
                            ['documents.php', 'fa-vault', 'Digital Vault'],
                            ['profile.php', 'fa-user-gear', 'My Settings'],
                        ];
                        foreach ($campus_links as $link):
                            $active = ($current_page === $link[0]);
                        ?>
                            <a href="<?php echo $link[0]; ?>" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition-all duration-200 group <?php echo $active ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary-600'; ?>">
                                <div class="w-8 flex justify-center">
                                    <i class="fas <?php echo $link[1]; ?> text-sm text-[11px]"></i>
                                </div>
                                <span class="font-bold text-sm transition-all"><?php echo $link[2]; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Profile Widget -->
            <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                <a href="../logout.php" class="flex items-center justify-between p-3 rounded-2xl hover:bg-rose-50 dark:hover:bg-rose-500/10 group transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-500/20 text-rose-600 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-xs"></i>
                        </div>
                        <span class="font-bold text-sm text-slate-600 dark:text-slate-400 group-hover:text-rose-600 transition-colors">Terminate</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] text-slate-300 dark:text-slate-700"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 min-w-0 flex flex-col relative overflow-y-auto">
            
            <header id="top-nav" class="sticky top-0 z-50 glass-navbar border-b border-slate-100 dark:border-slate-800 px-6 lg:px-10 h-20 flex items-center justify-between transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <button id="toggle-sidebar" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-500 shadow-soft border border-slate-100 dark:border-slate-700 active:scale-95 transition-all">
                        <i class="fas fa-bars-staggered"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider"><?php echo $page_title; ?></h2>
                        <div class="flex items-center space-x-2 text-[10px] font-bold text-primary-600 uppercase tracking-widest mt-0.5">
                            <span class="inline-block w-1 h-1 rounded-full bg-primary-600 animate-pulse"></span>
                            <span>Student Portal</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3 md:space-x-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="w-11 h-11 flex items-center justify-center rounded-2xl bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 shadow-soft border border-slate-100 dark:border-slate-700 hover:text-primary-600 dark:hover:text-primary-400 transition-all active:scale-90 relative">
                        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-sm"></i>
                        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-sm"></i>
                    </button>

                    <div class="hidden md:flex flex-col items-end mr-2">
                        <p class="text-xs font-black text-slate-800 dark:text-white"><?php echo $_SESSION['full_name']; ?></p>
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">Active Session</p>
                    </div>

                    <a href="profile.php" class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-tr from-primary-600 to-indigo-600 rounded-2xl opacity-20 group-hover:opacity-40 blur transition-all duration-300"></div>
                        <img src="<?php echo htmlspecialchars($profile_pic_url); ?>" class="relative w-11 h-11 rounded-2xl object-cover border-2 border-white dark:border-slate-800 ring-1 ring-slate-100 dark:ring-slate-800 shadow-premium" alt="Student Profile">
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-4 border-white dark:border-slate-900 rounded-full shadow-lg"></div>
                    </a>
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-10">