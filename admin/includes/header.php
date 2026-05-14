<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('admin')) {
    header("Location: ../login.php");
    exit();
}

$settings = get_college_settings($pdo);
$page_title = isset($page_title) ? $page_title : 'Admin Dashboard';
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> | VidyaSetu Admin</title>
    <meta name="description" content="VidyaSetu College Management System - Admin Dashboard">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-50/80 font-inter text-slate-700 antialiased text-[13px] admin-compact">

    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0 lg:hidden"></div>

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed lg:sticky inset-y-0 left-0 top-0 bg-white w-[230px] z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col border-r border-slate-100 h-screen overflow-hidden">

            <!-- Logo -->
            <div class="h-[52px] flex items-center px-4 border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 bg-gradient-to-br from-primary-600 to-primary-700 rounded-lg flex items-center justify-center text-white font-bold text-[11px] shadow-md">V</div>
                    <span class="font-bold text-base tracking-tight text-slate-800">Vidya<span class="text-primary-600">Setu</span></span>
                </div>
                <button id="close-sidebar" class="lg:hidden ml-auto text-slate-400 hover:text-slate-600 p-0.5">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- User Card -->
            <div class="px-3 py-2.5 border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center space-x-2 p-2 bg-slate-50 rounded-lg">
                    <img src="<?php echo htmlspecialchars($profile_pic_url); ?>" class="w-8 h-8 rounded-lg object-cover ring-1 ring-white shadow-sm" alt="Admin">
                    <div class="min-w-0">
                        <p class="text-[12px] font-semibold text-slate-700 truncate leading-tight"><?php echo $_SESSION['full_name']; ?></p>
                        <p class="text-[10px] font-medium text-primary-600 capitalize"><?php echo $_SESSION['role']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto py-2 px-2 sidebar-scroll">

                <p class="px-2.5 text-[9px] font-semibold text-slate-400 uppercase tracking-[0.1em] mb-1.5 mt-1">Overview</p>

                <a href="dashboard.php"
                    class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg mb-px transition-all duration-150 <?php echo $current_page === 'dashboard.php' ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center <?php echo $current_page === 'dashboard.php' ? 'bg-white/20' : 'bg-slate-100'; ?>">
                        <i class="fas fa-grip text-[10px]"></i>
                    </div>
                    <span class="font-medium text-[12px]">Dashboard</span>
                </a>

                <!-- Students -->
                <div class="sidebar-submenu mb-px">
                    <button class="submenu-toggle w-full flex items-center justify-between px-2.5 py-[7px] rounded-lg text-slate-600 hover:bg-slate-50 transition-all duration-150 <?php echo in_array($current_page, ['students-add.php','students-list.php','student-view.php']) ? 'bg-primary-50 text-primary-700' : ''; ?>">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center <?php echo in_array($current_page, ['students-add.php','students-list.php','student-view.php']) ? 'bg-primary-100' : 'bg-slate-100'; ?>">
                                <i class="fas fa-user-graduate text-[10px]"></i>
                            </div>
                            <span class="font-medium text-[12px]">Students</span>
                        </div>
                        <i class="fas fa-chevron-right text-[8px] submenu-arrow transition-transform duration-150"></i>
                    </button>
                    <div class="submenu-content hidden pl-[42px] mt-0.5 space-y-px">
                        <a href="students-add.php" class="block py-1.5 px-2.5 text-[11px] rounded-md transition-all <?php echo $current_page === 'students-add.php' ? 'text-primary-600 font-semibold bg-primary-50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'; ?>">
                            <i class="fas fa-plus-circle text-[8px] mr-1.5"></i>Add Student
                        </a>
                        <a href="students-list.php" class="block py-1.5 px-2.5 text-[11px] rounded-md transition-all <?php echo $current_page === 'students-list.php' ? 'text-primary-600 font-semibold bg-primary-50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'; ?>">
                            <i class="fas fa-list text-[8px] mr-1.5"></i>Student List
                        </a>
                        <a href="verify-documents.php" class="block py-1.5 px-2.5 text-[11px] rounded-md transition-all <?php echo $current_page === 'verify-documents.php' ? 'text-primary-600 font-semibold bg-primary-50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'; ?>">
                            <i class="fas fa-shield-check text-[8px] mr-1.5"></i>Verify Documents
                        </a>
                    </div>
                </div>

                <!-- Faculty -->
                <div class="sidebar-submenu mb-px">
                    <button class="submenu-toggle w-full flex items-center justify-between px-2.5 py-[7px] rounded-lg text-slate-600 hover:bg-slate-50 transition-all duration-150 <?php echo in_array($current_page, ['faculty-add.php','faculty-list.php','faculty-view.php']) ? 'bg-primary-50 text-primary-700' : ''; ?>">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center <?php echo in_array($current_page, ['faculty-add.php','faculty-list.php','faculty-view.php']) ? 'bg-primary-100' : 'bg-slate-100'; ?>">
                                <i class="fas fa-chalkboard-teacher text-[10px]"></i>
                            </div>
                            <span class="font-medium text-[12px]">Faculty</span>
                        </div>
                        <i class="fas fa-chevron-right text-[8px] submenu-arrow transition-transform duration-150"></i>
                    </button>
                    <div class="submenu-content hidden pl-[42px] mt-0.5 space-y-px">
                        <a href="faculty-add.php" class="block py-1.5 px-2.5 text-[11px] rounded-md transition-all <?php echo $current_page === 'faculty-add.php' ? 'text-primary-600 font-semibold bg-primary-50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'; ?>">
                            <i class="fas fa-plus-circle text-[8px] mr-1.5"></i>Add Faculty
                        </a>
                        <a href="faculty-list.php" class="block py-1.5 px-2.5 text-[11px] rounded-md transition-all <?php echo $current_page === 'faculty-list.php' ? 'text-primary-600 font-semibold bg-primary-50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'; ?>">
                            <i class="fas fa-list text-[8px] mr-1.5"></i>Faculty List
                        </a>
                    </div>
                </div>

                <a href="staff-list.php"
                    class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg mb-px transition-all duration-150 <?php echo $current_page === 'staff-list.php' ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center <?php echo $current_page === 'staff-list.php' ? 'bg-white/20' : 'bg-slate-100'; ?>">
                        <i class="fas fa-id-badge text-[10px]"></i>
                    </div>
                    <span class="font-medium text-[12px]">Staff Roster</span>
                </a>

                <p class="px-2.5 text-[9px] font-semibold text-slate-400 uppercase tracking-[0.1em] mt-4 mb-1.5">Academics</p>

                <?php
                $academic_links = [
                    ['departments.php', 'fa-building', 'Departments'],
                    ['courses.php', 'fa-laptop-code', 'Courses'],
                    ['subjects.php', 'fa-book', 'Subjects'],
                    ['timetable.php', 'fa-calendar-alt', 'Timetable'],
                    ['exams.php', 'fa-scroll', 'Examinations'],
                    ['fees.php', 'fa-wallet', 'Fee Management'],
                ];
                foreach ($academic_links as $link):
                ?>
                <a href="<?php echo $link[0]; ?>"
                    class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg mb-px transition-all duration-150 <?php echo $current_page === $link[0] ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center <?php echo $current_page === $link[0] ? 'bg-white/20' : 'bg-slate-100'; ?>">
                        <i class="fas <?php echo $link[1]; ?> text-[10px]"></i>
                    </div>
                    <span class="font-medium text-[12px]"><?php echo $link[2]; ?></span>
                </a>
                <?php endforeach; ?>

                <p class="px-2.5 text-[9px] font-semibold text-slate-400 uppercase tracking-[0.1em] mt-4 mb-1.5">Institutional</p>

                <?php
                $inst_links = [
                    ['library.php', 'fa-book-open', 'Library'],
                    ['placements.php', 'fa-briefcase', 'Placements'],
                    ['parents.php', 'fa-user-group', 'Guardians'],
                    ['events.php', 'fa-calendar-days', 'Events'],
                    ['id-cards.php', 'fa-id-card', 'Identity Protocol'],
                    ['logistics.php', 'fa-truck-ramp-box', 'Logistics'],
                    ['leave-approvals.php', 'fa-hand-holding-heart', 'Leave Approvals'],
                    ['complaints.php', 'fa-hand-holding-hand', 'Grievances'],
                ];
                foreach ($inst_links as $link):
                ?>
                <a href="<?php echo $link[0]; ?>"
                    class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg mb-px transition-all duration-150 <?php echo $current_page === $link[0] ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center <?php echo $current_page === $link[0] ? 'bg-white/20' : 'bg-slate-100'; ?>">
                        <i class="fas <?php echo $link[1]; ?> text-[10px]"></i>
                    </div>
                    <span class="font-medium text-[12px]"><?php echo $link[2]; ?></span>
                </a>
                <?php endforeach; ?>

                <p class="px-2.5 text-[9px] font-semibold text-slate-400 uppercase tracking-[0.1em] mt-4 mb-1.5">Communication</p>

                <?php
                $comm_links = [
                    ['notices.php', 'fa-bullhorn', 'Notices'],
                    ['messaging.php', 'fa-comment-dots', 'Messages'],
                    ['settings.php', 'fa-cog', 'Settings'],
                ];
                foreach ($comm_links as $link):
                ?>
                <a href="<?php echo $link[0]; ?>"
                    class="flex items-center space-x-2.5 px-2.5 py-[7px] rounded-lg mb-px transition-all duration-150 <?php echo $current_page === $link[0] ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'text-slate-600 hover:bg-slate-50'; ?>">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center <?php echo $current_page === $link[0] ? 'bg-white/20' : 'bg-slate-100'; ?>">
                        <i class="fas <?php echo $link[1]; ?> text-[10px]"></i>
                    </div>
                    <span class="font-medium text-[12px]"><?php echo $link[2]; ?></span>
                </a>
                <?php endforeach; ?>

            </nav>

            <!-- Logout -->
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

        <!-- MAIN -->
        <main class="flex-1 flex flex-col min-h-screen min-w-0">

            <!-- Top Bar -->
            <header class="h-[52px] bg-white border-b border-slate-100 flex items-center justify-between px-3 sm:px-4 lg:px-6 sticky top-0 z-30">
                <div class="flex items-center space-x-3">
                    <button id="toggle-sidebar" class="lg:hidden text-slate-500 hover:text-slate-700 p-1.5 -ml-1 rounded-md hover:bg-slate-50 transition-colors">
                        <i class="fas fa-bars text-sm"></i>
                    </button>
                    <div class="hidden sm:flex items-center space-x-1.5 text-[12px]">
                        <a href="dashboard.php" class="text-slate-400 hover:text-primary-600 transition-colors"><i class="fas fa-home text-[10px]"></i></a>
                        <i class="fas fa-chevron-right text-[7px] text-slate-300"></i>
                        <span class="font-medium text-slate-600"><?php echo htmlspecialchars($page_title); ?></span>
                    </div>
                </div>

                <div class="hidden md:flex items-center flex-1 max-w-sm mx-6">
                    <div class="relative w-full">
                        <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" id="global-search" placeholder="Search..."
                            class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[12px] placeholder-slate-400 focus:bg-white focus:border-primary-300 focus:ring-1 focus:ring-primary-100 transition-all outline-none">
                    </div>
                </div>

                <div class="flex items-center space-x-1.5">
                    <button id="mobile-search-toggle" class="md:hidden text-slate-500 hover:text-slate-700 p-1.5 rounded-md hover:bg-slate-50 transition-colors">
                        <i class="fas fa-search text-[11px]"></i>
                    </button>
                    <button class="relative text-slate-500 hover:text-slate-700 p-1.5 rounded-md hover:bg-slate-50 transition-colors">
                        <i class="fas fa-bell text-[11px]"></i>
                        <span class="absolute top-1 right-1 w-1.5 h-1.5 bg-red-500 rounded-full ring-1 ring-white"></span>
                    </button>
                    <div class="h-6 w-px bg-slate-200 hidden sm:block mx-1"></div>
                    <div class="relative" id="profile-dropdown">
                        <button class="flex items-center space-x-2 p-1 rounded-lg hover:bg-slate-50 transition-all" id="profile-btn">
                            <div class="text-right hidden sm:block">
                                <p class="text-[11px] font-semibold text-slate-700 leading-tight"><?php echo $_SESSION['full_name']; ?></p>
                                <p class="text-[9px] font-medium text-primary-600 capitalize"><?php echo $_SESSION['role']; ?></p>
                            </div>
                            <img src="<?php echo htmlspecialchars($profile_pic_url); ?>"
                                class="w-7 h-7 rounded-lg object-cover ring-1 ring-slate-100" alt="Profile">
                        </button>
                        <div id="profile-menu" class="absolute right-0 top-full mt-1 w-44 bg-white rounded-lg shadow-lg border border-slate-100 opacity-0 invisible transition-all duration-150 transform translate-y-1 py-1 z-50">
                            <div class="px-3 py-2 border-b border-slate-100">
                                <p class="text-[11px] font-semibold text-slate-700"><?php echo $_SESSION['full_name']; ?></p>
                                <p class="text-[10px] text-slate-400"><?php echo $_SESSION['role']; ?></p>
                            </div>
                            <a href="profile.php" class="flex items-center space-x-2 px-3 py-1.5 text-[11px] text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors"><i class="fas fa-user text-[9px] w-3"></i><span>My Profile</span></a>
                            <a href="settings.php" class="flex items-center space-x-2 px-3 py-1.5 text-[11px] text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors"><i class="fas fa-cog text-[9px] w-3"></i><span>Settings</span></a>
                            <hr class="my-1 border-slate-100">
                            <a href="../logout.php" class="flex items-center space-x-2 px-3 py-1.5 text-[11px] text-red-600 hover:bg-red-50 transition-colors"><i class="fas fa-sign-out-alt text-[9px] w-3"></i><span>Logout</span></a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Mobile Search -->
            <div id="mobile-search-bar" class="hidden md:hidden bg-white border-b border-slate-100 px-3 py-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                    <input type="text" placeholder="Search..."
                        class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[12px] placeholder-slate-400 focus:bg-white focus:border-primary-300 focus:ring-1 focus:ring-primary-100 transition-all outline-none">
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 p-3 sm:p-4 lg:p-6">