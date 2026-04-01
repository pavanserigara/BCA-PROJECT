<?php
$page_title = "My Academic Journey";
require_once 'includes/header.php';

// Fetch Student Info
$student_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT s.*, c.name as course_name FROM students s JOIN courses c ON s.course_id = c.id WHERE s.user_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Attendance Stats (REAL)
$stmt_att = $pdo->prepare("SELECT 
    COUNT(*) as total_lectures,
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present_count
    FROM attendance WHERE student_id = ?");
$stmt_att->execute([$student_id]);
$att = $stmt_att->fetch();
$att_total = (int) ($att['total_lectures'] ?? 0);
$att_present = (int) ($att['present_count'] ?? 0);
$attendance_rate = $att_total > 0 ? round(($att_present / $att_total) * 100, 1) : 0;

// Fees (REAL: current semester total vs paid)
$stmt_fee = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM fees_structure WHERE course_id = ? AND semester = ?");
$stmt_fee->execute([(int) $student['course_id'], (int) $student['semester']]);
$sem_fee_total = (float) $stmt_fee->fetchColumn();

$stmt_paid = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM fee_payments WHERE student_id = ? AND status IN ('Paid','Partial')");
$stmt_paid->execute([$student_id]);
$paid_total = (float) $stmt_paid->fetchColumn();

$fee_outstanding = max(0, $sem_fee_total - $paid_total);

// GPA (REAL: derived from published marks)
$stmt_gpa = $pdo->prepare("SELECT AVG(marks_obtained / NULLIF(max_marks,0)) * 10 FROM marks WHERE student_id = ?");
$stmt_gpa->execute([$student_id]);
$gpa10 = (float) $stmt_gpa->fetchColumn();
$gpa10 = $gpa10 > 0 ? round($gpa10, 2) : null;

// My Subjects
$stmt_subs = $pdo->prepare("SELECT * FROM subjects WHERE course_id = ? AND semester = ?");
$stmt_subs->execute([$student['course_id'], $student['semester']]);
$subjects = $stmt_subs->fetchAll();

// Subject-wise attendance map (REAL)
$stmt_sub_att = $pdo->prepare("SELECT subject_id,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present
    FROM attendance
    WHERE student_id = ?
    GROUP BY subject_id");
$stmt_sub_att->execute([$student_id]);
$att_rows = $stmt_sub_att->fetchAll();
$att_by_subject = [];
foreach ($att_rows as $r) {
    $t = (int) ($r['total'] ?? 0);
    $p = (int) ($r['present'] ?? 0);
    $att_by_subject[(int) $r['subject_id']] = [
        'total' => $t,
        'present' => $p,
        'percent' => $t > 0 ? round(($p / $t) * 100, 1) : 0
    ];
}

// Recent assignments (REAL)
$stmt_asg = $pdo->prepare("SELECT a.id, a.title, a.deadline, s.name as subject_name, s.code,
    (SELECT COUNT(*) FROM submissions sub WHERE sub.assignment_id = a.id AND sub.student_id = ?) as submitted
    FROM assignments a
    JOIN subjects s ON a.subject_id = s.id
    WHERE s.course_id = ? AND s.semester = ?
    ORDER BY a.deadline ASC
    LIMIT 4");
$stmt_asg->execute([$student_id, (int) $student['course_id'], (int) $student['semester']]);
$recent_assignments = $stmt_asg->fetchAll();
?>

<!-- Hero Section -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
    <div
        class="bg-indigo-600 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-100 flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer overflow-hidden relative">
        <div
            class="absolute -right-4 -bottom-4 bg-indigo-500 w-24 h-24 rounded-full group-hover:scale-150 transition-transform flex items-center justify-center opacity-40">
            <i class="fas fa-user-graduate text-3xl"></i>
        </div>
        <div>
            <h4 class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-1">My Status</h4>
            <div class="text-xl font-bold leading-tight">Enrolled in
                <?php echo $student['course_name']; ?>
            </div>
        </div>
        <div class="flex items-center space-x-2 text-indigo-100/60 font-medium text-xs">
            <i class="fas fa-id-badge"></i>
            <span>Roll:
                <?php echo $student['roll_no']; ?>
            </span>
        </div>
    </div>

    <!-- Attendance Card -->
    <div
        class="bg-white p-8 rounded-[2rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-none">Overall Attendance
            </h4>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                <i class="fas fa-check"></i>
            </div>
        </div>
        <div>
            <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mb-1">
                <?php echo $attendance_rate; ?>%
            </div>
            <div class="w-full h-2 bg-slate-50 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-indigo-500 rounded-full group-hover:animate-pulse"
                    style="width: <?php echo $attendance_rate; ?>%"></div>
            </div>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            <?php echo $attendance_rate >= 75 ? 'On Track (≥ 75%)' : 'Below Target (< 75%)'; ?>
        </p>
    </div>

    <!-- Fee Card -->
    <div
        class="bg-white p-8 rounded-[2rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-none">Semester Fees</h4>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
        <div>
            <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mb-1">
                <?php echo $sem_fee_total <= 0 ? '—' : ($fee_outstanding <= 0 ? 'Cleared' : 'Due'); ?>
            </div>
            <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed">
                <?php if ($sem_fee_total <= 0): ?>
                    Fees not configured for this semester.
                <?php else: ?>
                    Total: ₹<?php echo number_format($sem_fee_total, 0); ?> • Paid: ₹<?php echo number_format($paid_total, 0); ?>
                <?php endif; ?>
            </p>
        </div>
        <p class="text-[10px] font-bold <?php echo ($sem_fee_total > 0 && $fee_outstanding <= 0) ? 'text-emerald-500' : 'text-slate-400'; ?> uppercase tracking-widest">
            <?php
            if ($sem_fee_total <= 0) echo 'No Fee Plan';
            elseif ($fee_outstanding <= 0) echo 'No Dues Pending';
            else echo 'Outstanding: ₹' . number_format($fee_outstanding, 0);
            ?>
        </p>
    </div>

    <!-- Results Card -->
    <div
        class="bg-white p-8 rounded-[2rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-none">GPA Performance
            </h4>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div>
            <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mb-1">
                <?php echo $gpa10 !== null ? ($gpa10 . '/10') : '—'; ?>
            </div>
            <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed">
                <?php echo $gpa10 !== null ? 'Computed from published marks.' : 'No marks published yet.'; ?>
            </p>
        </div>
        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Current Semester Profile</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
    <!-- Active Subjects Table -->
    <div class="lg:col-span-2 bg-white p-10 rounded-[2.5rem] border border-slate-50 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight">Active Subjects</h4>
            <span
                class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 uppercase tracking-widest">Semester
                <?php echo $student['semester']; ?>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="pb-6 px-4">Subject</th>
                        <th class="pb-6 px-4">Code</th>
                        <th class="pb-6 px-4">Attendance</th>
                        <th class="pb-6 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($subjects as $sub): ?>
                        <tr class="group hover:bg-slate-50 transition-all">
                            <td class="py-6 px-4">
                                <span class="text-sm font-bold text-slate-700">
                                    <?php echo $sub['name']; ?>
                                </span>
                            </td>
                            <td class="py-6 px-4">
                                <span
                                    class="text-xs font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-100">
                                    <?php echo $sub['code']; ?>
                                </span>
                            </td>
                            <td class="py-6 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <?php
                                        $sub_stats = $att_by_subject[(int) $sub['id']] ?? ['percent' => 0];
                                        $pct = (float) $sub_stats['percent'];
                                        ?>
                                        <div class="h-full <?php echo $pct >= 75 ? 'bg-emerald-400' : 'bg-rose-400'; ?>"
                                            style="width: <?php echo $pct; ?>%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500"><?php echo $pct; ?>%</span>
                                </div>
                            </td>
                            <td class="py-6 px-4 text-right">
                                <button
                                    class="w-10 h-10 rounded-xl bg-white border border-slate-100 text-slate-300 hover:text-indigo-600 transition-all">
                                    <i class="fas fa-download text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl flex flex-col">
        <div class="flex items-center justify-between mb-8">
            <h4 class="text-2xl font-black text-white tracking-tight">Assignments</h4>
            <a href="assignments.php" class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">View
                Portal</a>
        </div>

        <div class="space-y-6 flex-1 overflow-y-auto pr-2 scrollbar-hide">
            <?php if (empty($recent_assignments)): ?>
                <div class="p-6 bg-slate-800 rounded-[2rem] border border-slate-700/50 text-slate-400">
                    <p class="font-bold">No assignments posted yet.</p>
                    <p class="text-[12px] mt-1">When faculty posts assignments for your semester, they’ll appear here.</p>
                </div>
            <?php else: ?>
                <?php foreach ($recent_assignments as $a): ?>
                    <?php
                    $is_submitted = (int) $a['submitted'] > 0;
                    $deadline = $a['deadline'] ? strtotime($a['deadline']) : null;
                    $deadline_label = $deadline ? date('M d, Y', $deadline) : 'No deadline';
                    ?>
                    <div
                        class="p-6 bg-slate-800 rounded-[2rem] border border-slate-700/50 hover:border-indigo-500/50 transition-all group">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="px-3 py-1 bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-indigo-500/20">
                                <?php echo htmlspecialchars($a['code']); ?>
                            </span>
                            <span class="text-slate-500 text-[10px] font-black uppercase tracking-widest">
                                <?php echo htmlspecialchars($deadline_label); ?>
                            </span>
                        </div>
                        <h6 class="text-white font-bold leading-tight group-hover:text-indigo-400 transition-colors mb-2">
                            <?php echo htmlspecialchars($a['title']); ?>
                        </h6>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-4">
                            <?php echo htmlspecialchars($a['subject_name']); ?>
                        </p>
                        <div class="flex items-center justify-between pt-4 border-t border-slate-700/50">
                            <div class="flex items-center space-x-2 text-[10px] font-bold <?php echo $is_submitted ? 'text-emerald-500' : 'text-slate-500'; ?>">
                                <i class="fas <?php echo $is_submitted ? 'fa-check-double' : 'fa-paper-plane'; ?>"></i>
                                <span><?php echo $is_submitted ? 'Submitted' : 'Not Submitted'; ?></span>
                            </div>
                            <a href="assignments.php"
                                class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center hover:bg-indigo-600 transition-all uppercase font-black text-[10px]">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button
            class="mt-10 w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-900/40 hover:bg-indigo-700 transition-all">
            Open Submissions Dashboard
        </button>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>