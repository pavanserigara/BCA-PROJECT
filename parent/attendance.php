<?php
$page_title = "Attendance Analysis";
require_once 'includes/header.php';

$student_id = $_SESSION['linked_student_id'];

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
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Institutional Presence</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Verified attendance logs for <?php echo $_SESSION['linked_student_name']; ?>.</p>
    </div>
    
    <div class="flex items-center space-x-3 bg-white dark:bg-slate-800 p-3 rounded-[2rem] shadow-soft border border-slate-100 dark:border-slate-800">
        <div class="w-12 h-12 rounded-2xl <?php echo $percentage >= 75 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600'; ?> flex items-center justify-center text-xl">
            <i class="fas <?php echo $percentage >= 75 ? 'fa-check-double' : 'fa-circle-exclamation'; ?>"></i>
        </div>
        <div class="pr-6">
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none mb-1">Status</p>
            <p class="text-sm font-black <?php echo $percentage >= 75 ? 'text-emerald-600' : 'text-rose-600'; ?> uppercase italic tracking-tight">
                <?php echo $percentage >= 75 ? 'Eligibility Met' : 'Action Required'; ?>
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-emerald-600 p-10 rounded-[3.5rem] text-white shadow-premium relative overflow-hidden group">
        <div class="relative z-10">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2 italic">Cumulative Index</p>
            <h3 class="text-6xl font-black tracking-tight italic"><?php echo $percentage; ?>%</h3>
        </div>
        <i class="fas fa-chart-line absolute -bottom-8 -right-8 text-9xl opacity-10 group-hover:scale-110 transition-transform duration-700"></i>
    </div>

    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800">
        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 italic">Faculty Sessions</p>
        <h3 class="text-5xl font-black text-slate-800 dark:text-white italic"><?php echo $total; ?></h3>
        <p class="text-xs font-bold text-slate-500 mt-6 italic">Total lectures synchronized.</p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800">
        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-2 italic">Confirmed Presence</p>
        <h3 class="text-5xl font-black text-emerald-600 italic"><?php echo $present; ?></h3>
        <p class="text-xs font-bold text-slate-500 mt-6 italic">Sessions successfully attended.</p>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-[4rem] shadow-premium border border-slate-100 dark:border-slate-800 overflow-hidden mb-20">
    <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
        <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase italic leading-none">Curriculum Breakdown</h4>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-50 dark:border-slate-700/50">
                    <th class="py-8 px-10">Academic Subject</th>
                    <th class="py-8 px-10 text-center">Conducted</th>
                    <th class="py-8 px-10 text-center">Attended</th>
                    <th class="py-8 px-10 text-right pr-10">Trajectory</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                <?php if (empty($subjects_attendance)): ?>
                    <tr>
                        <td colspan="4" class="p-20 text-center italic text-slate-400 font-bold">No academic logs synchronized for this cycle.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subjects_attendance as $sub):
                        $sub_total = $sub['sub_total'] ?: 0;
                        $sub_present = $sub['sub_present'] ?: 0;
                        $sub_percent = $sub_total > 0 ? round(($sub_present / $sub_total) * 100, 1) : 0;
                        ?>
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-all duration-300">
                            <td class="py-10 px-10">
                                <div class="flex items-center space-x-6">
                                    <div class="w-14 h-14 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl flex items-center justify-center text-emerald-600 font-black text-xs shadow-soft group-hover:scale-110 transition-transform italic">
                                        <?php echo $sub['code']; ?>
                                    </div>
                                    <div>
                                        <h6 class="text-sm font-black text-slate-800 dark:text-white uppercase italic leading-none mb-2 tracking-tight"><?php echo $sub['subject_name']; ?></h6>
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Pedagogical Unit</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-10 px-10 text-center text-sm font-black text-slate-500 dark:text-slate-400 italic"><?php echo $sub_total; ?></td>
                            <td class="py-10 px-10 text-center text-sm font-black text-emerald-600 italic"><?php echo $sub_present; ?></td>
                            <td class="py-10 px-10 text-right pr-10">
                                <div class="flex flex-col items-end">
                                    <div class="flex items-center space-x-4 mb-3">
                                        <div class="w-32 md:w-48 h-2.5 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden border border-slate-100 dark:border-slate-800">
                                            <div class="h-full <?php echo $sub_percent >= 75 ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]' : 'bg-rose-500'; ?> rounded-full transition-all duration-1000" style="width: <?php echo $sub_percent; ?>%"></div>
                                        </div>
                                        <span class="text-sm font-black <?php echo $sub_percent >= 75 ? 'text-emerald-500' : 'text-rose-500'; ?> italic"><?php echo $sub_percent; ?>%</span>
                                    </div>
                                    <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">
                                        <?php echo $sub_percent >= 75 ? 'Authorized Level' : 'Below Threshold'; ?>
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
