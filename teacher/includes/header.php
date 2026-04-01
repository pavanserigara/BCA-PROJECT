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
$profile_pic_path = __DIR__ . '/../../assets/images/' . $profile_pic;
$profile_pic_url = (!empty($profile_pic) && is_file($profile_pic_path)) ? $profile_pic : 'default_profile.svg';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title; ?> | Faculty Portal
    </title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'inter': ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#EEF2FF',100:'#E0E7FF',200:'#C7D2FE',300:'#A5B4FC',400:'#818CF8',500:'#6366F1',600:'#4F46E5',700:'#4338CA',800:'#3730A3',900:'#312E81' },
                    },
                    boxShadow: {
                        'card': '0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03)',
                        'card-hover': '0 2px 12px rgba(0,0,0,0.07)',
                    }
                }
            }
        }
    </script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50/80 font-inter text-slate-700 antialiased text-[13px]">

    <div id="sidebar-backdrop"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0 lg:hidden">
    </div>

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed lg:sticky inset-y-0 left-0 top-0 bg-white w-[230px] z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col border-r border-slate-100 h-screen overflow-hidden">

            <div class="h-[52px] flex items-center px-4 border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <div
                        class="w-7 h-7 bg-gradient-to-br from-primary-600 to-primary-700 rounded-lg flex items-center justify-center text-white font-bold text-[11px] shadow-md">
                        V</div>
                    <span class="font-bold text-base tracking-tight text-slate-800">Vidya<span
                            class="text-primary-600">Setu</span></span>
                </div>
                <button id="close-sidebar" class="lg:hidden ml-auto text-slate-400 hover:text-slate-600 p-0.5">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="px-3 py-2.5 border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center space-x-2 p-2 bg-slate-50 rounded-lg">
                    <img src="../assets/images/<?php echo htmlspecialchars($profile_pic_url); ?>"
                        class="w-8 h-8 rounded-lg object-cover ring-1 ring-white shadow-sm" alt="Faculty">
                    <div class="min-w-0">
                        <p class="text-[12px] font-semibold text-slate-700 truncate leading-tight">
                            <?php echo $_SESSION['full_name']; ?>
                        </p>
                        <p class="text-[10px] font-medium text-primary-600 capitalize">Faculty</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-2 px-2 sidebar-scroll">
                <p class="px-2.5 text-[9px] font-semibold text-slate-400 uppercase tracking-[0.1em] mb-1.5 mt-1">Teaching
                </p>

                <?php
                $teach_links = [
                    ['dashboard.php', 'fa-desktop', 'Dashboard'],
                    ['attendance-take.php', 'fa-clipboard-user', 'Attendance'],
                    ['subjects.php', 'fa-book', 'My Subjects'],
                    ['assignments.php', 'fa-file-arrow-up', 'Assignments'],
                    ['marks-entry.php', 'fa-spell-check', 'Exam Marks'],
                ];
                foreach ($teach_links as $link):
                ?>
                    <a href="<?php echo $link[0]; ?>"
                        class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg mb-px transition-all duration-150 <?php echo $current_page === $link[0] ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                        <div
                            class="w-6 h-6 rounded-md flex items-center justify-center <?php echo $current_page === $link[0] ? 'bg-white/20' : 'bg-slate-100'; ?>">
                            <i class="fas <?php echo $link[1]; ?> text-[10px]"></i>
                        </div>
                        <span class="font-medium text-[12px]"><?php echo $link[2]; ?></span>
                    </a>
                <?php endforeach; ?>

                <p class="px-2.5 text-[9px] font-semibold text-slate-400 uppercase tracking-[0.1em] mt-4 mb-1.5">Campus
                </p>

                <?php
                $campus_links = [
                    ['timetable.php', 'fa-calendar-alt', 'Timetable'],
                    ['notices.php', 'fa-bullhorn', 'Notices'],
                    ['messaging.php', 'fa-comment-dots', 'Messages'],
                    ['../admin/library.php', 'fa-book-open', 'Library'],
                    ['../admin/events.php', 'fa-calendar-star', 'Events'],
                    ['profile.php', 'fa-user-circle', 'My Profile'],
                ];
                foreach ($campus_links as $link):
                ?>
                    <a href="<?php echo $link[0]; ?>"
                        class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg mb-px transition-all duration-150 <?php echo $current_page === $link[0] ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                        <div
                            class="w-6 h-6 rounded-md flex items-center justify-center <?php echo $current_page === $link[0] ? 'bg-white/20' : 'bg-slate-100'; ?>">
                            <i class="fas <?php echo $link[1]; ?> text-[10px]"></i>
                        </div>
                        <span class="font-medium text-[12px]"><?php echo $link[2]; ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="px-3 py-2.5 border-t border-slate-100 flex-shrink-0">
                <a href="../logout.php"
                    class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg text-red-500 hover:bg-red-50 transition-all duration-150">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center bg-red-50">
                        <i class="fas fa-sign-out-alt text-[10px]"></i>
                    </div>
                    <span class="font-medium text-[12px]">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-h-screen min-w-0">

            <!-- Top Navigation -->
            <header class="h-[52px] bg-white border-b border-slate-100 flex items-center justify-between px-3 sm:px-4 lg:px-6 sticky top-0 z-30">
                <div class="flex items-center space-x-3 min-w-0">
                    <button id="toggle-sidebar"
                        class="lg:hidden text-slate-500 hover:text-slate-700 p-1.5 -ml-1 rounded-md hover:bg-slate-50 transition-colors">
                        <i class="fas fa-bars text-sm"></i>
                    </button>
                    <div class="min-w-0">
                        <p class="text-[12px] font-semibold text-slate-700 truncate leading-tight">
                            <?php echo htmlspecialchars($page_title); ?>
                        </p>
                        <p class="text-[10px] text-slate-400 hidden sm:block">Faculty Portal</p>
                    </div>
                </div>

                <a href="profile.php" class="flex items-center space-x-2 ml-1 pl-3 border-l border-slate-100">
                    <img src="../assets/images/<?php echo htmlspecialchars($profile_pic_url); ?>"
                        class="w-7 h-7 rounded-lg object-cover ring-1 ring-slate-100" alt="Profile">
                </a>
            </header>

            <div class="flex-1 p-3 sm:p-4 lg:p-6">