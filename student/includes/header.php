<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('student')) {
    header("Location: ../login.php");
    exit();
}

$settings = get_college_settings($pdo);
$page_title = isset($page_title) ? $page_title : 'Student Dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title; ?> | Student Portal
    </title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-indigo-50/50 font-inter text-slate-800">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside
            class="sidebar-transition fixed md:static inset-y-0 left-0 bg-white w-72 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex md:flex-col shadow-2xl border-r border-indigo-50">
            <div class="p-8 flex items-center justify-center space-x-3 text-slate-800 mb-6">
                <div
                    class="w-12 h-12 bg-gradient-to-tr from-indigo-500 to-indigo-700 rounded-2xl flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-100 italic text-2xl">
                    V</div>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <a href="dashboard.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all duration-200 <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'; ?>">
                    <i class="fas fa-home w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Overview</span>
                </a>

                <a href="attendance.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-chart-pie w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Attendance</span>
                </a>

                <a href="timetable.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-calendar-week w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Timetable</span>
                </a>

                <a href="results.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-graduation-cap w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">My Results</span>
                </a>

                <a href="assignments.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-tasks w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Assignments</span>
                </a>

                <a href="fees.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-credit-card w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Fees Portal</span>
                </a>

                <p
                    class="px-4 text-[9px] font-black text-slate-300 uppercase tracking-widest mt-8 mb-4 italic leading-none">
                    Institutional Life</p>

                <a href="library.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-book-open w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Library Catalog</span>
                </a>

                <a href="events.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-calendar-star w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Events Gallery</span>
                </a>

                <a href="complaints.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-hand-holding-hand w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Lodge Grievance</span>
                </a>

                <a href="notices.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-bullhorn w-5 text-sm"></i>
                    <span class="font-bold text-sm tracking-tight">Notice Board</span>
                </a>

                <div class="mt-20 p-4">
                    <a href="profile.php"
                        class="flex items-center space-x-3 px-8 py-3.5 rounded-2xl bg-indigo-600 text-white font-black text-[10px] uppercase tracking-widest shadow-xl hover:bg-indigo-700 transition-all mb-4">
                        <span>My Profile</span> <i class="fas fa-user-circle ml-2 text-[10px]"></i>
                    </a>
                    <a href="../logout.php"
                        class="flex items-center space-x-3 px-8 py-3.5 rounded-2xl bg-slate-900 text-white font-black text-[10px] uppercase tracking-widest shadow-xl hover:bg-indigo-700 transition-all">
                        <span>Sign Out Portal</span> <i class="fas fa-arrow-right ml-2 text-[8px]"></i>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-h-screen">

            <!-- Top Navigation -->
            <header
                class="h-20 bg-white border-b border-indigo-50 flex items-center justify-between px-8 sticky top-0 z-30">
                <div>
                    <h2 class="font-black text-slate-800 tracking-tight">Welcome,
                        <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>!
                    </h2>
                </div>

                <div class="flex items-center space-x-6">
                    <button class="relative text-slate-400 p-2.5 bg-slate-50 rounded-xl">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-2.5 right-3 w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                    </button>

                    <a href="profile.php" class="flex items-center space-x-3 ml-2 pl-6 border-l border-slate-50">
                        <img src="../assets/images/<?php echo $_SESSION['profile_pic']; ?>"
                            class="w-11 h-11 rounded-xl object-cover ring-2 ring-indigo-50 hover:ring-indigo-500 transition-all" alt="Profile">
                    </a>
                </div>
            </header>

            <div class="p-8 flex-1">