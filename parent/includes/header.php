<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('parent')) {
    header("Location: ../login.php");
    exit();
}

$settings = get_college_settings($pdo);
$page_title = isset($page_title) ? $page_title : 'Parent Portal';
$current_page = basename($_SERVER['PHP_SELF']);
$profile_pic = $_SESSION['profile_pic'] ?? '';
$profile_pic_url = '../assets/images/default_profile.svg';

if (!empty($profile_pic)) {
    if (is_file(__DIR__ . '/../../uploads/profiles/' . $profile_pic)) {
        $profile_pic_url = '../uploads/profiles/' . $profile_pic;
    } elseif (is_file(__DIR__ . '/../../assets/images/' . $profile_pic)) {
        $profile_pic_url = '../assets/images/' . $profile_pic;
    }
}

// Fetch the linked student for this parent
$stmt = $pdo->prepare("SELECT s.*, u.full_name, u.profile_pic as student_pic 
                        FROM parents p 
                        JOIN students s ON p.student_id = s.user_id 
                        JOIN users u ON s.user_id = u.id 
                        WHERE p.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student_data = $stmt->fetch();

if (!$student_data) {
    // Set defaults so the page still renders
    $student_data = ['user_id' => 0, 'full_name' => 'Not Linked', 'roll_no' => 'N/A', 'student_pic' => 'default_profile.svg'];
}

$_SESSION['linked_student_id'] = $student_data['user_id'];
$_SESSION['linked_student_name'] = $student_data['full_name'];
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
                        primary: { 50:'#f0fdf4', 100:'#dcfce7', 200:'#bbf7d0', 300:'#86efac', 400:'#4ade80', 500:'#22c55e', 600:'#16a34a', 700:'#15803d', 800:'#166534', 900:'#14532d' },
                        slate: { 950: '#020617' }
                    },
                    boxShadow: {
                        'premium': '0 10px 40px -10px rgba(0,0,0,0.08), 0 20px 25px -5px rgba(0,0,0,0.03)',
                        'soft': '0 2px 15px -3px rgba(0,0,0,0.07), 0 4px 6px -2px rgba(0,0,0,0.05)',
                    }
                }
            }
        }

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
                    <div class="w-10 h-10 bg-gradient-to-tr from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 font-black text-xl italic">P</div>
                    <div>
                        <h1 class="text-lg font-extrabold text-slate-800 dark:text-white leading-none tracking-tight">Vidya<span class="text-emerald-600">Setu</span></h1>
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">Parent Portal</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 pb-8 space-y-8 custom-sidebar-scroll">
                <div>
                    <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Observation Deck</p>
                    <div class="space-y-1">
                        <?php
                        $main_links = [
                            ['dashboard.php', 'fa-grip-vertical', 'Health Check'],
                            ['attendance.php', 'fa-calendar-check', 'Attendance'],
                            ['results.php', 'fa-award', 'Performance'],
                            ['fees.php', 'fa-receipt', 'Treasury'],
                            ['notices.php', 'fa-bullhorn', 'Notice Board'],
                            ['messaging.php', 'fa-message', 'Direct Box'],
                        ];
                        foreach ($main_links as $link):
                            $active = ($current_page === $link[0]);
                        ?>
                            <a href="<?php echo $link[0]; ?>" 
                               class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition-all duration-200 group <?php echo $active ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-emerald-600'; ?>">
                                <div class="w-8 flex justify-center">
                                    <i class="fas <?php echo $link[1]; ?> text-sm"></i>
                                </div>
                                <span class="font-bold text-sm"><?php echo $link[2]; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <p class="px-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Settings</p>
                    <div class="space-y-1">
                        <a href="profile.php" class="flex items-center space-x-3 px-4 py-3 rounded-2xl transition-all duration-200 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-emerald-600">
                            <div class="w-8 flex justify-center">
                                <i class="fas fa-user-gear text-sm"></i>
                            </div>
                            <span class="font-bold text-sm">Profile</span>
                        </a>
                    </div>
                </div>

                <!-- Linked Student Widget -->
                <div class="mx-4 mt-8 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-20 h-20 bg-emerald-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <p class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4">Linked Student</p>
                    <div class="flex items-center space-x-3">
                    <?php 
                        $s_pic = $student_data['student_pic'];
                        $s_pic_url = '../assets/images/default_profile.svg';
                        if (!empty($s_pic)) {
                            if (is_file(__DIR__ . '/../../uploads/profiles/' . $s_pic)) {
                                $s_pic_url = '../uploads/profiles/' . $s_pic;
                            } elseif (is_file(__DIR__ . '/../../assets/images/' . $s_pic)) {
                                $s_pic_url = '../assets/images/' . $s_pic;
                            }
                        }
                    ?>
                    <img src="<?php echo $s_pic_url; ?>" class="w-10 h-10 rounded-xl object-cover border border-white dark:border-slate-700 shadow-sm" alt="">
                        <div>
                            <p class="text-[10px] font-black text-slate-800 dark:text-white uppercase italic leading-none mb-1"><?php echo $student_data['full_name']; ?></p>
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest"><?php echo $student_data['roll_no']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-slate-100 dark:border-slate-800">
                <a href="../logout.php" class="flex items-center justify-between p-3 rounded-2xl hover:bg-rose-50 dark:hover:bg-rose-500/10 group transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-500/20 text-rose-600 flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-xs"></i>
                        </div>
                        <span class="font-bold text-sm text-slate-600 dark:text-slate-400 group-hover:text-rose-600">Logout</span>
                    </div>
                </a>
            </div>
        </aside>

        <div class="flex-1 min-w-0 flex flex-col relative overflow-y-auto">
            <header id="top-nav" class="sticky top-0 z-50 glass-navbar border-b border-slate-100 dark:border-slate-800 px-6 lg:px-10 h-20 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button id="toggle-sidebar" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-800 text-slate-500 shadow-soft border border-slate-100 dark:border-slate-700">
                        <i class="fas fa-bars-staggered"></i>
                    </button>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider"><?php echo $page_title; ?></h2>
                        <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest">Guardian Access</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex flex-col items-end">
                        <p class="text-xs font-black text-slate-800 dark:text-white"><?php echo $_SESSION['full_name']; ?></p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Parent Account</p>
                    </div>
                    <img src="../assets/images/<?php echo htmlspecialchars($profile_pic_url); ?>" class="w-10 h-10 rounded-xl object-cover border-2 border-white dark:border-slate-800 shadow-premium" alt="">
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-10">
