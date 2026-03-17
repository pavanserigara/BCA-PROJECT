<?php
$page_title = "My Attendance Report";
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
$percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

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

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Attendance Portfolio</h2>
            <p class="text-slate-500 font-medium">Detailed tracking of your classroom presence this semester.</p>
        </div>

        <div class="bg-white px-8 py-4 rounded-[2rem] border border-indigo-100 shadow-sm flex items-center space-x-4">
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</p>
                <p class="text-sm font-bold <?php echo $percentage >= 75 ? 'text-emerald-500' : 'text-rose-500'; ?>">
                    <?php echo $percentage >= 75 ? 'Eligible for Exams' : 'Shortage Alert'; ?>
                </p>
            </div>
            <div
                class="w-12 h-12 rounded-2xl <?php echo $percentage >= 75 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'; ?> flex items-center justify-center text-xl font-black">
                <?php echo $percentage >= 75 ? '✓' : '!'; ?>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <!-- Percentage Card -->
        <div
            class="bg-indigo-600 p-10 rounded-[3rem] text-white shadow-2xl shadow-indigo-100 flex flex-col justify-between relative overflow-hidden group">
            <div class="relative z-10">
                <h4 class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-2">Aggregate Percentage
                </h4>
                <div class="text-6xl font-black tracking-tight">
                    <?php echo $percentage; ?>%
                </div>
            </div>
            <div
                class="absolute -right-8 -bottom-8 w-40 h-40 bg-indigo-500 rounded-full opacity-30 group-hover:scale-150 transition-transform">
            </div>
        </div>

        <!-- Conducted Card -->
        <div
            class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm flex flex-col justify-between card-hover">
            <h4 class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Total Lectures Conducted
            </h4>
            <div class="text-4xl font-black text-slate-800 tracking-tight">
                <?php echo $total; ?>
            </div>
            <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed italic">Cumulative across all
                active subjects.</p>
        </div>

        <!-- Attended Card -->
        <div
            class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm flex flex-col justify-between card-hover">
            <h4 class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Lectures Attended</h4>
            <div class="text-4xl font-black text-emerald-600 tracking-tight">
                <?php echo $present; ?>
            </div>
            <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed italic">Including late
                entries and verified leaves.</p>
        </div>
    </div>

    <!-- Subject Breakdown -->
    <div
        class="bg-white rounded-[3rem] shadow-sm border border-indigo-100/50 overflow-hidden mb-20 animate__animated animate__fadeInUp">
        <div class="p-10 border-b border-indigo-50 bg-slate-50/50 flex items-center justify-between">
            <h4 class="text-xl font-bold text-slate-800">Subject-wise Breakdown</h4>
            <button
                class="px-6 py-3 bg-white border border-indigo-100 text-indigo-600 font-bold rounded-2xl text-xs uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">Download
                Detailed PDF</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="py-8 px-10">Subject Name</th>
                        <th class="py-8 px-10 text-center">Conducted</th>
                        <th class="py-8 px-10 text-center">Attended</th>
                        <th class="py-8 px-10 text-right">Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/20">
                    <?php if (empty($subjects_attendance)): ?>
                        <tr>
                            <td colspan="4" class="p-20 text-center italic text-slate-400">No classroom records found yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subjects_attendance as $sub):
                            $sub_total = $sub['sub_total'] ?: 0;
                            $sub_present = $sub['sub_present'] ?: 0;
                            $sub_percent = $sub_total > 0 ? round(($sub_present / $sub_total) * 100, 1) : 0;
                            ?>
                            <tr class="group hover:bg-slate-50 transition-all">
                                <td class="py-8 px-10">
                                    <div class="flex items-center space-x-5">
                                        <div
                                            class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-xs shadow-sm">
                                            <?php echo $sub['code']; ?>
                                        </div>
                                        <h6 class="text-lg font-bold text-slate-800 tracking-tight">
                                            <?php echo $sub['subject_name']; ?>
                                        </h6>
                                    </div>
                                </td>
                                <td class="py-8 px-10 text-center font-black text-slate-500">
                                    <?php echo $sub_total; ?>
                                </td>
                                <td class="py-8 px-10 text-center font-black text-indigo-600">
                                    <?php echo $sub_present; ?>
                                </td>
                                <td class="py-8 px-10 text-right">
                                    <div class="flex flex-col items-end">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div class="w-40 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full <?php echo $sub_percent >= 75 ? 'bg-emerald-400' : 'bg-rose-400'; ?> rounded-full"
                                                    style="width: <?php echo $sub_percent; ?>%"></div>
                                            </div>
                                            <span
                                                class="text-sm font-black <?php echo $sub_percent >= 75 ? 'text-emerald-500' : 'text-rose-500'; ?>">
                                                <?php echo $sub_percent; ?>%
                                            </span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                            <?php echo $sub_percent >= 75 ? 'Safe Zone' : 'Short Attendance'; ?>
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
</div>

<?php require_once 'includes/footer.php'; ?>