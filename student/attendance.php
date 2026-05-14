<?php
$page_title = "Attendance Analysis";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Get Student info
$stmt_std = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt_std->execute([$student_id]);
$student = $stmt_std->fetch();

// Fetch overall attendance stats
$stmt_stats = $pdo->prepare("SELECT 
    COUNT(*) as total_lectures,
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present_count,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_count
    FROM attendance WHERE student_id = ?");
$stmt_stats->execute([$student_id]);
$stats = $stmt_stats->fetch();

$total = $stats['total_lectures'] ?: 0;
$present = $stats['present_count'] ?: 0;
$absent = $stats['absent_count'] ?: 0;
$percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

// Fetch subject-wise attendance
$stmt_sub_stats = $pdo->prepare("SELECT 
    s.name as subject_name, s.code,
    COUNT(a.id) as sub_total,
    SUM(CASE WHEN a.status = 'Present' OR a.status = 'Late' THEN 1 ELSE 0 END) as sub_present
    FROM subjects s
    LEFT JOIN attendance a ON s.id = a.subject_id AND a.student_id = ?
    WHERE s.course_id = ? AND s.semester = ?
    GROUP BY s.id");
$stmt_sub_stats->execute([$student_id, $student['course_id'], $student['semester']]);
$subjects_attendance = $stmt_sub_stats->fetchAll();

/**
 * Calculate lectures needed to reach 75% threshold
 */
function lectures_needed_for_75($total, $present) {
    if ($total === 0) return 0;
    $current_percent = ($present / $total) * 100;
    if ($current_percent >= 75) return 0;
    
    // Formula: 0.75 * (total + X) = present + X
    // 0.75 * total + 0.75X = present + X
    // 0.75 * total - present = 0.25X
    // X = (0.75 * total - present) / 0.25
    return ceil((0.75 * $total - $present) / 0.25);
}
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Attendance Portfolio</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Real-time tracking of your classroom engagement.</p>
    </div>
    
    <div class="flex items-center space-x-3 bg-white dark:bg-slate-800 p-3 rounded-[2rem] shadow-soft border border-slate-100 dark:border-slate-700/50">
        <div class="w-12 h-12 rounded-2xl <?php echo $percentage >= 75 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600'; ?> flex items-center justify-center text-xl">
            <i class="fas <?php echo $percentage >= 75 ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
        </div>
        <div class="pr-6">
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Eligibility</p>
            <p class="text-sm font-black <?php echo $percentage >= 75 ? 'text-emerald-600' : 'text-rose-600'; ?>">
                <?php echo $percentage >= 75 ? 'Exam Ready' : 'Low Attendance'; ?>
            </p>
        </div>
    </div>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-gradient-to-br from-primary-600 to-indigo-700 p-10 rounded-[3rem] text-white shadow-premium relative overflow-hidden group">
        <div class="relative z-10">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-80 mb-2">Total Average</p>
            <h3 class="text-6xl font-black tracking-tight"><?php echo $percentage; ?>%</h3>
        </div>
        <i class="fas fa-percent absolute -bottom-8 -right-8 text-9xl opacity-10 group-hover:scale-110 transition-transform duration-700"></i>
    </div>

    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3rem] shadow-premium border border-slate-100 dark:border-slate-700/50">
        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Conducted Lectures</p>
        <h3 class="text-5xl font-black text-slate-800 dark:text-white"><?php echo $total; ?></h3>
        <p class="text-xs font-medium text-slate-500 mt-6">Across all enrolled subjects this semester.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3rem] shadow-premium border border-slate-100 dark:border-slate-700/50">
        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2">Present Sessions</p>
        <h3 class="text-5xl font-black text-emerald-600"><?php echo $present; ?></h3>
        <p class="text-xs font-medium text-slate-500 mt-6 italic">Includes verified late arrivals.</p>
    </div>
</div>

<!-- Subject Table -->
<div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-premium border border-slate-100 dark:border-slate-700/50 overflow-hidden mb-20 animate-in fade-in slide-in-from-bottom-6 duration-700">
    <div class="p-8 border-b border-slate-50 dark:border-slate-700/50 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
        <h4 class="text-xl font-extrabold text-slate-800 dark:text-white leading-none">Subject Breakdown</h4>
        <button class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-primary-600 text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-primary-600 hover:text-white transition-all shadow-soft">
            <i class="fas fa-file-export mr-2"></i>Export Report
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-50 dark:border-slate-700/50">
                    <th class="py-6 px-10">Academic Subject</th>
                    <th class="py-6 px-10 text-center">Conducted</th>
                    <th class="py-6 px-10 text-center">Attended</th>
                    <th class="py-6 px-10 text-right pr-10">Relative Performance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                <?php if (empty($subjects_attendance)): ?>
                    <tr>
                        <td colspan="4" class="p-20 text-center italic text-slate-400">Academic journey in progress... No data yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subjects_attendance as $sub):
                        $sub_total = $sub['sub_total'] ?: 0;
                        $sub_present = $sub['sub_present'] ?: 0;
                        $sub_percent = $sub_total > 0 ? round(($sub_present / $sub_total) * 100, 1) : 0;
                        ?>
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-all duration-300">
                            <td class="py-8 px-10">
                                <div class="flex items-center space-x-5">
                                    <div class="w-12 h-12 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-2xl flex items-center justify-center text-primary-600 font-black text-xs shadow-soft group-hover:scale-110 transition-transform">
                                        <?php echo $sub['code']; ?>
                                    </div>
                                    <div>
                                        <h6 class="text-sm font-black text-slate-800 dark:text-white tracking-tight"><?php echo $sub['subject_name']; ?></h6>
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">Core Subject</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-8 px-10 text-center text-sm font-black text-slate-500 dark:text-slate-400"><?php echo $sub_total; ?></td>
                            <td class="py-8 px-10 text-center text-sm font-black text-primary-600"><?php echo $sub_present; ?></td>
                            <td class="py-8 px-10 text-right pr-10">
                                <div class="flex flex-col items-end">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <div class="w-32 md:w-48 h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                                            <div class="h-full <?php echo $sub_percent >= 75 ? 'bg-emerald-500' : 'bg-rose-500'; ?> rounded-full" style="width: <?php echo $sub_percent; ?>%"></div>
                                        </div>
                                        <span class="text-sm font-black <?php echo $sub_percent >= 75 ? 'text-emerald-500' : 'text-rose-500'; ?>"><?php echo $sub_percent; ?>%</span>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                        <?php 
                                        if ($sub_percent >= 75) {
                                            echo 'Threshold Met';
                                        } else {
                                            $needed = lectures_needed_for_75($sub_total, $sub_present);
                                            echo "Need $needed more lectures";
                                        }
                                        ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>