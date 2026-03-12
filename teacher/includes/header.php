<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$settings = get_college_settings($pdo);
$page_title = isset($page_title) ? $page_title : 'Faculty Dashboard';
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-indigo-50/20 font-inter text-slate-800">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside
            class="sidebar-transition fixed md:static inset-y-0 left-0 bg-white w-72 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex md:flex-col shadow-2xl border-r border-indigo-50">
            <div class="p-8 flex items-center space-x-3 text-slate-800 border-b border-slate-50 mb-6">
                <div
                    class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/20">
                    V</div>
                <span class="font-bold text-2xl tracking-tight italic">VidyaSetu</span>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Teaching Workspace
                </p>

                <a href="dashboard.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all duration-200 <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'; ?>">
                    <i class="fas fa-desktop w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="attendance-take.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-clipboard-user w-5"></i>
                    <span class="font-medium">Mark Attendance</span>
                </a>

                <a href="subjects.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-book w-5"></i>
                    <span class="font-medium">My Subjects</span>
                </a>

                <a href="assignments.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-file-arrow-up w-5"></i>
                    <span class="font-medium">Assignments</span>
                </a>

                <a href="marks-entry.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-spell-check w-5"></i>
                    <span class="font-medium">Exam Marks</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-8 mb-4">Communications
                </p>

                <a href="timetable.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-calendar-alt w-5"></i>
                    <span class="font-medium">Timetable</span>
                </a>

                <a href="notices.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-bullhorn w-5"></i>
                    <span class="font-medium">Notice Board</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-8 mb-4">Institutional
                    Hub
                </p>

                <a href="../admin/library.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-book-open w-5"></i>
                    <span class="font-medium">Library Catalog</span>
                </a>

                <a href="../admin/events.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-indigo-600 transition-all">
                    <i class="fas fa-calendar-star w-5"></i>
                    <span class="font-medium">Events Gallery</span>
                </a>

                <div class="mt-20 p-4 border-t border-slate-50">
                    <a href="profile.php"
                        class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-indigo-600 hover:bg-indigo-50 transition-all mb-2">
                        <i class="fas fa-user-circle w-5"></i>
                        <span class="font-medium">Faculty Profile</span>
                    </a>
                    <a href="../logout.php"
                        class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-rose-500 hover:bg-rose-50 transition-all">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="font-medium">Logout Portal</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-h-screen">

            <!-- Top Navigation -->
            <header
                class="h-20 bg-white border-b border-indigo-50/50 flex items-center justify-between px-8 sticky top-0 z-30">
                <button class="md:hidden text-slate-600">
                    <i class="fas fa-bars-staggered"></i>
                </button>

                <div
                    class="hidden md:flex items-center bg-slate-50 px-4 py-2.5 rounded-2xl w-96 border border-slate-100">
                    <i class="fas fa-search text-slate-400"></i>
                    <input type="text" placeholder="Search my class records..."
                        class="bg-transparent border-none focus:ring-0 ml-2 w-full text-sm font-medium">
                </div>

                <div class="flex items-center space-x-6">
                    <a href="profile.php" class="flex items-center space-x-3 ml-2 pl-6 border-l border-slate-50">
                        <div class="text-right hidden sm:block mr-3">
                            <p class="text-sm font-bold text-slate-700 leading-tight">
                                <?php echo $_SESSION['full_name']; ?>
                            </p>
                            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Faculty Member</p>
                        </div>
                        <img src="../assets/images/<?php echo $_SESSION['profile_pic']; ?>"
                            class="w-11 h-11 rounded-xl object-cover ring-4 ring-indigo-50 hover:ring-indigo-500 transition-all"
                            alt="Profile">
                    </a>
                </div>
            </header>

            <div class="p-8 flex-1">