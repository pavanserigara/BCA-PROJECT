<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!has_role('admin')) {
    header("Location: ../login.php");
    exit();
}

$settings = get_college_settings($pdo);
$page_title = isset($page_title) ? $page_title : 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $page_title; ?> | VidyaSetu Admin
    </title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#6366F1',
                        accent: '#F43F5E',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-indigo-50/30 font-inter text-slate-800">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop"
        class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed md:static inset-y-0 left-0 bg-slate-900 w-72 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex md:flex-col shadow-2xl overflow-y-auto scrollbar-hide">
            <div class="p-8 flex items-center space-x-3 text-white border-b border-slate-800 mb-6">
                <div
                    class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/20">
                    V</div>
                <span class="font-bold text-2xl tracking-tight">Vidya<span class="text-indigo-400">Setu</span></span>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">Main Navigation</p>

                <a href="dashboard.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all duration-200 <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-th-large w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <div class="group">
                    <button
                        class="w-full flex items-center justify-between space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-user-graduate w-5"></i>
                            <span class="font-medium">Students</span>
                        </div>
                        <i
                            class="fas fa-chevron-right text-[10px] transform group-hover:rotate-90 transition-transform"></i>
                    </button>
                    <div class="hidden group-hover:block pl-12 space-y-1 mt-1">
                        <a href="students-add.php" class="block py-2 text-sm text-slate-400 hover:text-white italic">Add
                            Student</a>
                        <a href="students-list.php"
                            class="block py-2 text-sm text-slate-400 hover:text-white italic">Student List</a>
                    </div>
                </div>

                <div class="group">
                    <button
                        class="w-full flex items-center justify-between space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-chalkboard-teacher w-5"></i>
                            <span class="font-medium">Faculty</span>
                        </div>
                        <i
                            class="fas fa-chevron-right text-[10px] transform group-hover:rotate-90 transition-transform"></i>
                    </button>
                    <div class="hidden group-hover:block pl-12 space-y-1 mt-1">
                        <a href="faculty-add.php" class="block py-2 text-sm text-slate-400 hover:text-white italic">Add
                            Teacher</a>
                        <a href="faculty-list.php"
                            class="block py-2 text-sm text-slate-400 hover:text-white italic">Faculty List</a>
                    </div>
                </div>

                <a href="staff-list.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all <?php echo strpos($_SERVER['PHP_SELF'], 'staff-list') !== false ? 'bg-indigo-600 text-white' : ''; ?>">
                    <i class="fas fa-id-badge w-5"></i>
                    <span class="font-medium">Staff Roster</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-8 mb-4">Operations &
                    Academic</p>

                <a href="departments.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-building w-5"></i>
                    <span class="font-medium">Departments</span>
                </a>

                <a href="courses.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-laptop-code w-5"></i>
                    <span class="font-medium">Curriculum</span>
                </a>

                <a href="exams.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-scroll w-5"></i>
                    <span class="font-medium">Examination Cell</span>
                </a>

                <a href="fees.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-wallet w-5"></i>
                    <span class="font-medium">Financial Treasury</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-8 mb-4">Institutional
                    Hub</p>

                <a href="library.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-book-open w-5 text-sm"></i>
                    <span class="font-medium">Library Catalog</span>
                </a>

                <a href="placements.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-briefcase w-5 text-sm"></i>
                    <span class="font-medium">Placement Cell</span>
                </a>

                <a href="events.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-calendar-star w-5 text-sm"></i>
                    <span class="font-medium">Manage Events</span>
                </a>

                <a href="complaints.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-hand-holding-hand w-5 text-sm"></i>
                    <span class="font-medium">Grievance Board</span>
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-8 mb-4">Broadcast
                    Center
                </p>

                <a href="notices.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-bullhorn w-5"></i>
                    <span class="font-medium">Notice Board</span>
                </a>

                <a href="messaging.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-comment-dots w-5 text-sm"></i>
                    <span class="font-medium">Messaging Hub</span>
                </a>

                <a href="settings.php"
                    class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all">
                    <i class="fas fa-cog w-5"></i>
                    <span class="font-medium">Settings</span>
                </a>

                <div class="mt-20 p-4 border-t border-slate-800">
                    <a href="../logout.php"
                        class="flex items-center space-x-3 px-4 py-3.5 rounded-2xl text-rose-400 hover:bg-rose-500/10 transition-all">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="font-medium">Logout Admin</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-h-screen">

            <!-- Top Navigation -->
            <header
                class="h-20 bg-white border-b border-indigo-100 flex items-center justify-between px-8 sticky top-0 z-30">
                <button id="toggle-sidebar" class="md:hidden text-slate-600 hover:text-slate-900 transition-colors">
                    <i class="fas fa-bars-staggered text-xl"></i>
                </button>

                <div
                    class="hidden md:flex items-center bg-slate-100 px-4 py-2.5 rounded-2xl w-96 group focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all border border-transparent focus-within:border-indigo-500/30">
                    <i class="fas fa-search text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    <input type="text" placeholder="Search student records..."
                        class="bg-transparent border-none focus:ring-0 ml-2 w-full text-slate-800 placeholder-slate-400">
                </div>

                <div class="flex items-center space-x-6">
                    <button
                        class="relative text-slate-500 hover:text-slate-800 transition-colors p-2.5 bg-slate-50 rounded-xl">
                        <i class="fas fa-bell"></i>
                        <span
                            class="absolute top-2 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                    </button>

                    <div class="flex items-center space-x-3 ml-2 pl-6 border-l border-slate-100">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-800 leading-tight">
                                <?php echo $_SESSION['full_name']; ?>
                            </p>
                            <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-widest">
                                <?php echo $_SESSION['role']; ?>
                            </p>
                        </div>
                        <div class="relative group">
                            <img src="../assets/images/<?php echo $_SESSION['profile_pic']; ?>"
                                class="w-11 h-11 rounded-xl object-cover ring-2 ring-indigo-100 active:scale-95 transition-all"
                                alt="Profile">
                            <div
                                class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all py-2 z-50">
                                <a href="profile.php"
                                    class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors">My
                                    Profile</a>
                                <a href="settings.php"
                                    class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-colors">Settings</a>
                                <hr class="my-2 border-slate-100">
                                <a href="../logout.php"
                                    class="block px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content Injection Point -->
            <div class="p-8 flex-1">