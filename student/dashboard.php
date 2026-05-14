<?php
$page_title = "Academic Dashboard";
require_once 'includes/header.php';

// Fetch Student Info
$student_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT s.*, c.name as course_name FROM students s JOIN courses c ON s.course_id = c.id WHERE s.user_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Attendance Stats
$stmt_att = $pdo->prepare("SELECT 
    COUNT(*) as total_lectures,
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present_count
    FROM attendance WHERE student_id = ?");
$stmt_att->execute([$student_id]);
$att = $stmt_att->fetch();
$att_total = (int) ($att['total_lectures'] ?? 0);
$att_present = (int) ($att['present_count'] ?? 0);
$attendance_rate = $att_total > 0 ? round(($att_present / $att_total) * 100, 1) : 0;

// Fees
$stmt_fee = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM fees_structure WHERE course_id = ? AND semester = ?");
$stmt_fee->execute([(int) $student['course_id'], (int) $student['semester']]);
$sem_fee_total = (float) $stmt_fee->fetchColumn();

$stmt_paid = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM fee_payments WHERE student_id = ? AND status IN ('Paid','Partial')");
$stmt_paid->execute([$student_id]);
$paid_total = (float) $stmt_paid->fetchColumn();
$fee_outstanding = max(0, $sem_fee_total - $paid_total);

// GPA
$stmt_gpa = $pdo->prepare("SELECT AVG(marks_obtained / NULLIF(max_marks,0)) * 10 FROM marks WHERE student_id = ?");
$stmt_gpa->execute([$student_id]);
$gpa10 = (float) $stmt_gpa->fetchColumn();
$gpa10 = $gpa10 > 0 ? round($gpa10, 2) : null;

// My Subjects & Attendance Map
$stmt_subs = $pdo->prepare("SELECT * FROM subjects WHERE course_id = ? AND semester = ?");
$stmt_subs->execute([$student['course_id'], $student['semester']]);
$subjects = $stmt_subs->fetchAll();

$stmt_sub_att = $pdo->prepare("SELECT subject_id,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present
    FROM attendance WHERE student_id = ? GROUP BY subject_id");
$stmt_sub_att->execute([$student_id]);
$att_by_subject = [];
foreach ($stmt_sub_att->fetchAll() as $r) {
    $t = (int) $r['total'];
    $p = (int) $r['present'];
    $att_by_subject[(int) $r['subject_id']] = ['percent' => $t > 0 ? round(($p / $t) * 100, 1) : 0];
}

// Recent Assignments
$stmt_asg = $pdo->prepare("SELECT a.id, a.title, a.deadline, s.name as subject_name, s.code,
    (SELECT COUNT(*) FROM submissions sub WHERE sub.assignment_id = a.id AND sub.student_id = ?) as submitted
    FROM assignments a JOIN subjects s ON a.subject_id = s.id
    WHERE s.course_id = ? AND s.semester = ?
    ORDER BY a.deadline ASC LIMIT 3");
$stmt_asg->execute([$student_id, (int)$student['course_id'], (int)$student['semester']]);
$recent_assignments = $stmt_asg->fetchAll();
?>

<div class="mb-10">
    <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Welcome back, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>! 👋</h2>
    <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Here's an overview of your academic progress for Semester <?php echo $student['semester']; ?>.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Profile Mini -->
    <div class="bg-gradient-to-br from-primary-600 to-indigo-700 p-6 rounded-[2.5rem] text-white shadow-premium relative overflow-hidden group">
        <div class="relative z-10">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-80 mb-4 text-primary-100">Enrollment</p>
            <h3 class="text-xl font-black leading-tight"><?php echo $student['course_name']; ?></h3>
            <div class="mt-6 flex items-center space-x-2 bg-white/10 backdrop-blur-md w-fit px-3 py-1.5 rounded-xl border border-white/10">
                <i class="fas fa-fingerprint text-[10px] text-primary-200"></i>
                <span class="text-[10px] font-black uppercase tracking-widest"><?php echo $student['roll_no']; ?></span>
            </div>
        </div>
        <i class="fas fa-graduation-cap absolute -bottom-4 -right-4 text-7xl opacity-10 group-hover:scale-125 group-hover:-rotate-12 transition-all duration-500"></i>
    </div>

    <!-- Attendance Widget -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50 group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                <i class="fas fa-calendar-check"></i>
            </div>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Attendance</span>
        </div>
        <h3 class="text-3xl font-black text-slate-800 dark:text-white"><?php echo $attendance_rate; ?>%</h3>
        <div class="mt-4 flex items-center space-x-2">
            <div class="flex-1 h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: <?php echo $attendance_rate; ?>%"></div>
            </div>
            <span class="text-[10px] font-bold text-emerald-600"><?php echo $attendance_rate >= 75 ? 'Safe' : 'Critical'; ?></span>
        </div>
    </div>

    <!-- Fees Widget -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 flex items-center justify-center">
                <i class="fas fa-wallet text-sm"></i>
            </div>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Fee Status</span>
        </div>
        <h3 class="text-2xl font-black text-slate-800 dark:text-white">
            <?php echo $fee_outstanding <= 0 ? 'Fully Paid' : '₹' . number_format($fee_outstanding, 0); ?>
        </h3>
        <p class="text-[10px] font-bold <?php echo $fee_outstanding <= 0 ? 'text-emerald-500' : 'text-rose-500'; ?> uppercase tracking-widest mt-4 flex items-center">
            <i class="fas <?php echo $fee_outstanding <= 0 ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> mr-1.5"></i>
            <?php echo $fee_outstanding <= 0 ? 'No Dues Pending' : 'Action Required'; ?>
        </p>
    </div>

    <!-- Performance Widget -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-2xl bg-primary-50 dark:bg-primary-500/10 text-primary-600 flex items-center justify-center">
                <i class="fas fa-chart-line text-sm"></i>
            </div>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Avg GPA</span>
        </div>
        <h3 class="text-3xl font-black text-slate-800 dark:text-white"><?php echo $gpa10 ?? 'N/A'; ?></h3>
        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-4">Current Progress</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Subjects List -->
    <div class="lg:col-span-2 space-y-6">
        <div class="flex items-center justify-between">
            <h4 class="text-xl font-extrabold text-slate-800 dark:text-white">Active Courses</h4>
            <a href="timetable.php" class="text-[10px] font-bold text-primary-600 uppercase tracking-widest hover:underline">Full Schedule</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($subjects as $sub): 
                $sub_pct = $att_by_subject[(int)$sub['id']]['percent'] ?? 0;
            ?>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700/50 shadow-soft hover:shadow-premium transition-all group border-l-4 <?php echo $sub_pct < 75 ? 'border-l-rose-500' : 'border-l-emerald-500'; ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.2em] mb-1 block"><?php echo $sub['code']; ?></span>
                            <h5 class="font-extrabold text-slate-800 dark:text-white text-sm group-hover:text-primary-600 transition-colors"><?php echo $sub['name']; ?></h5>
                        </div>
                        <div class="text-[10px] font-black text-slate-400 bg-slate-50 dark:bg-slate-900 px-2 py-1 rounded-lg">SEM <?php echo $sub['semester']; ?></div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                             <div class="text-[10px] font-bold text-slate-500">Attendance</div>
                             <div class="text-xs font-black text-slate-800 dark:text-white"><?php echo $sub_pct; ?>%</div>
                        </div>
                        <div class="w-20 h-1.5 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                            <div class="h-full <?php echo $sub_pct >= 75 ? 'bg-emerald-500' : 'bg-rose-500'; ?>" style="width: <?php echo $sub_pct; ?>%"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sidebar Cards -->
    <div class="space-y-8">
        <!-- Announcements / Notices -->
        <div class="bg-slate-900 dark:bg-slate-800/50 p-8 rounded-[2.5rem] text-white shadow-premium border border-slate-800 dark:border-slate-700 relative overflow-hidden">
            <h4 class="text-lg font-black mb-6 flex items-center">
                <i class="fas fa-bullhorn text-primary-400 mr-2"></i>
                Latest Tasks
            </h4>
            
            <div class="space-y-5">
                <?php if (empty($recent_assignments)): ?>
                    <div class="text-center py-6 opacity-40">
                        <i class="fas fa-check-circle text-2xl mb-2 block"></i>
                        <p class="text-[10px] font-bold uppercase tracking-widest">No pending tasks</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_assignments as $asg): 
                        $due = strtotime($asg['deadline']);
                        $is_late = $due < time() && !$asg['submitted'];
                    ?>
                        <div class="flex items-start space-x-3 p-4 bg-white/5 dark:bg-slate-900 rounded-2xl border border-white/5 hover:bg-white/10 transition-colors relative group">
                            <div class="w-10 h-10 rounded-xl bg-primary-600 flex-shrink-0 flex items-center justify-center text-xs font-black italic shadow-lg shadow-primary-500/20">
                                <?php echo date('d', $due); ?>
                            </div>
                            <div class="min-w-0">
                                <h6 class="text-xs font-extrabold truncate"><?php echo $asg['title']; ?></h6>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest"><?php echo $asg['code']; ?></span>
                                    <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                                    <span class="text-[9px] font-bold <?php echo $is_late ? 'text-rose-400' : 'text-primary-400'; ?> uppercase tracking-widest">
                                        <?php echo $asg['submitted'] ? 'Done' : 'Due ' . date('M j', $due); ?>
                                    </span>
                                </div>
                            </div>
                            <a href="assignments.php" class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-primary-600/10 rounded-2xl transition-all"></a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <a href="assignments.php" class="mt-8 w-full block py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-2xl text-center text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-primary-500/20 transition-all">
                Open Workspace
            </a>
        </div>

        <!-- Quick Links Card -->
        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700/50 shadow-premium">
             <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-6">Discovery Hub</h4>
             <div class="grid grid-cols-2 gap-3">
                 <a href="library.php" class="flex flex-col items-center p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl hover:bg-primary-500 hover:text-white transition-all group">
                     <i class="fas fa-book-open text-lg mb-2 text-primary-600 group-hover:text-white"></i>
                     <span class="text-[10px] font-black uppercase tracking-widest">Library</span>
                 </a>
                 <a href="events.php" class="flex flex-col items-center p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl hover:bg-primary-500 hover:text-white transition-all group">
                     <i class="fas fa-vr-cardboard text-lg mb-2 text-primary-600 group-hover:text-white"></i>
                     <span class="text-[10px] font-black uppercase tracking-widest">Fests</span>
                 </a>
             </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>