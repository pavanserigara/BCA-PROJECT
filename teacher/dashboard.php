<?php
$page_title = "Academic Overview";
require_once 'includes/header.php';

// Fetch teacher specific stats
$teacher_id = $_SESSION['user_id'];
$subjects_count = $pdo->prepare("SELECT COUNT(*) FROM teacher_subjects WHERE teacher_id = ?");
$subjects_count->execute([$teacher_id]);
$total_subs = $subjects_count->fetchColumn();

// Fetch assigned subjects
$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

// Today's attendance %
$stmt_today = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present
    FROM attendance
    WHERE marked_by = ? AND date = ?");
$stmt_today->execute([$teacher_id, date('Y-m-d')]);
$today_stats = $stmt_today->fetch();
$today_total = (int) ($today_stats['total'] ?? 0);
$today_present = (int) ($today_stats['present'] ?? 0);
$avg_today = $today_total > 0 ? round(($today_present / $today_total) * 100, 1) : 0;

// Real pending submissions count (not yet graded)
$stmt_pending = $pdo->prepare("SELECT COUNT(*) FROM submissions s 
                               JOIN assignments a ON s.assignment_id = a.id 
                               WHERE a.teacher_id = ? AND s.grade IS NULL");
$stmt_pending->execute([$teacher_id]);
$pending_submissions = $stmt_pending->fetchColumn();
?>

<!-- Welcome Section -->
<div class="mb-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight leading-tight">
                Welcome back, <span class="text-primary-600"><?php echo explode(' ', $_SESSION['full_name'])[0]; ?></span> 👋
            </h2>
            <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Here's an overview of your classes for today.</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="bg-white dark:bg-slate-800 p-3 rounded-2xl shadow-soft border border-slate-100 dark:border-slate-700 flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="pr-2">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Today is</p>
                    <p class="text-sm font-bold text-slate-700 dark:text-white"><?php echo date('D, M d, Y'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Dashboard -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    
    <!-- Academic Load -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 group hover:border-primary-500 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                <i class="fas fa-book-open"></i>
            </div>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Load</span>
        </div>
        <h3 class="text-4xl font-extrabold text-slate-800 dark:text-white mb-1"><?php echo $total_subs; ?></h3>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Assigned Subjects</p>
    </div>

    <!-- Attendance -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 group hover:border-emerald-500 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                <i class="fas fa-user-check"></i>
            </div>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Attendance</span>
        </div>
        <h3 class="text-4xl font-extrabold text-slate-800 dark:text-white mb-1"><?php echo $today_total > 0 ? ($avg_today . '%') : '—'; ?></h3>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Average Presence Today</p>
    </div>

    <!-- Assignments -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 group hover:border-amber-500 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all">
                <i class="fas fa-file-signature"></i>
            </div>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Tasks</span>
        </div>
        <h3 class="text-4xl font-extrabold text-slate-800 dark:text-white mb-1"><?php echo $pending_submissions; ?></h3>
        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Pending Review</p>
    </div>

    <!-- Quick Tool -->
    <a href="attendance-take.php" class="bg-primary-600 p-6 rounded-3xl shadow-premium text-white flex flex-col justify-between hover:scale-[1.02] transition-transform active:scale-100">
        <div class="flex items-center justify-between">
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white">
                <i class="fas fa-plus"></i>
            </div>
            <i class="fas fa-arrow-right-long opacity-50"></i>
        </div>
        <div class="mt-4">
            <h4 class="font-extrabold text-lg tracking-tight">Mark Attendance</h4>
            <p class="text-[10px] font-bold text-white/70 uppercase tracking-widest">Start Daily Roll Call</p>
        </div>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- My Courses List -->
    <div class="lg:col-span-7">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">My Subjects</h4>
            <a href="subjects.php" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors uppercase tracking-widest">View All</a>
        </div>
        <div class="space-y-4">
            <?php if (empty($my_subjects)): ?>
                <div class="p-10 bg-white dark:bg-slate-800 rounded-[2.5rem] text-center border border-dashed border-slate-200 dark:border-slate-700">
                    <p class="text-slate-500 dark:text-slate-400 font-medium">No subjects assigned yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($my_subjects as $sub): ?>
                    <div class="p-5 bg-white dark:bg-slate-800 rounded-[2rem] shadow-soft border border-slate-100 dark:border-slate-700 flex items-center justify-between group hover:shadow-premium transition-all duration-300">
                        <div class="flex items-center space-x-5">
                            <div class="w-14 h-14 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center text-primary-600 font-extrabold text-xs shadow-inner">
                                <?php echo $sub['code']; ?>
                            </div>
                            <div>
                                <h6 class="font-extrabold text-slate-800 dark:text-white group-hover:text-primary-600 transition-colors leading-tight"><?php echo $sub['name']; ?></h6>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">
                                    <?php echo $sub['course_name']; ?> • Sem <?php echo $sub['semester']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="attendance-take.php?subject_id=<?php echo $sub['id']; ?>" class="w-10 h-10 bg-slate-50 dark:bg-slate-900 rounded-xl flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all shadow-sm" title="Take Attendance">
                                <i class="fas fa-calendar-check text-xs"></i>
                            </a>
                            <a href="marks-entry.php?subject_id=<?php echo $sub['id']; ?>" class="w-10 h-10 bg-slate-50 dark:bg-slate-900 rounded-xl flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all shadow-sm" title="Enter Marks">
                                <i class="fas fa-pen-to-square text-xs"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div class="lg:col-span-5">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">Quick Operations</h4>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <a href="assignments.php" class="p-6 bg-indigo-50/50 dark:bg-indigo-500/5 rounded-3xl border border-indigo-100 dark:border-indigo-500/10 group hover:border-indigo-500 transition-all">
                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-indigo-600 shadow-soft mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-arrow-up"></i>
                </div>
                <h5 class="font-extrabold text-slate-800 dark:text-white text-sm">Assignments</h5>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Post new tasks & grade</p>
            </a>

            <a href="notices.php" class="p-6 bg-rose-50/50 dark:bg-rose-500/5 rounded-3xl border border-rose-100 dark:border-rose-500/10 group hover:border-rose-500 transition-all">
                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-rose-600 shadow-soft mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h5 class="font-extrabold text-slate-800 dark:text-white text-sm">Announcements</h5>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Broadcast to students</p>
            </a>

            <a href="messaging.php" class="p-6 bg-emerald-50/50 dark:bg-emerald-500/5 rounded-3xl border border-emerald-100 dark:border-emerald-500/10 group hover:border-emerald-500 transition-all">
                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-emerald-600 shadow-soft mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-comments"></i>
                </div>
                <h5 class="font-extrabold text-slate-800 dark:text-white text-sm">Direct Messages</h5>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Chat with students/staff</p>
            </a>

            <a href="timetable.php" class="p-6 bg-amber-50/50 dark:bg-amber-500/5 rounded-3xl border border-amber-100 dark:border-amber-500/10 group hover:border-amber-500 transition-all">
                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-amber-600 shadow-soft mb-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h5 class="font-extrabold text-slate-800 dark:text-white text-sm">Class Schedule</h5>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">View your weekly plan</p>
            </a>

        </div>

        <!-- Latest Message Preview -->
        <div class="mt-8 p-6 bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-bold text-slate-800 dark:text-white text-sm italic">Recent Communication</h5>
                <span class="w-2 h-2 bg-primary-600 rounded-full animate-pulse"></span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center text-slate-400">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800 dark:text-white truncate">Aniket Sharma (BCA - III)</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">Please check my assignment submission...</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>