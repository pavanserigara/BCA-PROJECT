<?php
$page_title = "Academic Results";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Fetch latest Exam info
$stmt_res = $pdo->prepare("SELECT m.*, s.name as subject_name, s.code, e.name as exam_name, e.type as exam_type, e.exam_date
                           FROM marks m
                           JOIN subjects s ON m.subject_id = s.id
                           JOIN exams e ON m.exam_id = e.id
                           WHERE m.student_id = ?
                           ORDER BY e.exam_date DESC, s.name ASC");
$stmt_res->execute([$student_id]);
$results = $stmt_res->fetchAll();

// Group results by exam
$grouped_results = [];
foreach ($results as $res) {
    if (!isset($grouped_results[$res['exam_id']])) {
        $grouped_results[$res['exam_id']] = [
            'exam_name' => $res['exam_name'],
            'exam_type' => $res['exam_type'],
            'exam_date' => $res['exam_date'],
            'marks' => []
        ];
    }
    $grouped_results[$res['exam_id']]['marks'][] = $res;
}
?>

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight">Academic Performance</h2>
            <p class="text-slate-500 font-medium">View and download your examination and assessment reports.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-indigo-600 px-8 py-4 rounded-[2rem] text-white shadow-xl shadow-indigo-100 flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1">Status</p>
                    <p class="text-base font-bold text-white tracking-tight">Active Enrollment</p>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center text-sm font-black italic">
                    V</div>
            </div>
        </div>
    </div>

    <!-- Exam Performance Cards -->
    <div class="space-y-12 mb-20">
        <?php if (empty($grouped_results)): ?>
            <div
                class="bg-white p-20 rounded-[3rem] text-center border border-indigo-50 shadow-sm animate__animated animate__fadeIn">
                <div
                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                    <i class="fas fa-graduation-cap text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800">Results Pending Publication</h3>
                <p class="text-slate-500 mt-2">Check back after evaluations are finalized by faculty.</p>
            </div>
        <?php else: ?>
            <?php foreach ($grouped_results as $exam_id => $exam): ?>
                <div
                    class="bg-white rounded-[3rem] shadow-sm border border-indigo-100/50 overflow-hidden animate__animated animate__fadeInUp">
                    <div
                        class="p-10 border-b border-indigo-50 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div class="flex items-center space-x-4 mb-2">
                                <span
                                    class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg">
                                    <?php echo $exam['exam_type']; ?> Evaluation
                                </span>
                                <span class="text-xs font-bold text-slate-400"><i class="fas fa-calendar-day mr-2"></i>
                                    <?php echo date('d M, Y', strtotime($exam['exam_date'])); ?>
                                </span>
                            </div>
                            <h4 class="text-2xl font-black text-slate-800 tracking-tight">
                                <?php echo $exam['exam_name']; ?>
                            </h4>
                        </div>

                        <button
                            class="px-8 py-4 bg-white border border-indigo-100 text-indigo-600 font-black rounded-2xl text-xs uppercase tracking-widest shadow-sm hover:shadow-indigo-50 hover:bg-indigo-600 hover:text-white transition-all transform active:scale-95">
                            <i class="fas fa-file-pdf mr-2 text-sm"></i> Download Marksheet
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                                    <th class="py-8 px-10">Subject Code</th>
                                    <th class="py-8 px-10">Subject Name</th>
                                    <th class="py-8 px-10 text-center">Marks Obtained</th>
                                    <th class="py-8 px-10 text-center">Percentage</th>
                                    <th class="py-8 px-10 text-right">Result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-indigo-50/20">
                                <?php
                                $total_obtain = 0;
                                $total_max = 0;
                                foreach ($exam['marks'] as $mks):
                                    $total_obtain += $mks['marks_obtained'];
                                    $total_max += $mks['max_marks'];
                                    $percent = round(($mks['marks_obtained'] / $mks['max_marks']) * 100, 1);
                                    ?>
                                    <tr class="group hover:bg-slate-50 transition-all">
                                        <td class="py-8 px-10">
                                            <span
                                                class="text-xs font-black text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
                                                <?php echo $mks['code']; ?>
                                            </span>
                                        </td>
                                        <td class="py-8 px-10">
                                            <h6 class="text-lg font-bold text-slate-800 tracking-tight">
                                                <?php echo $mks['subject_name']; ?>
                                            </h6>
                                        </td>
                                        <td class="py-8 px-10 text-center">
                                            <span class="text-xl font-black text-slate-800 whitespace-nowrap">
                                                <?php echo $mks['marks_obtained']; ?> <span
                                                    class="text-xs font-bold text-slate-400">/
                                                    <?php echo $mks['max_marks']; ?>
                                                </span>
                                            </span>
                                        </td>
                                        <td class="py-8 px-10 text-center font-black text-slate-500">
                                            <?php echo $percent; ?>%
                                        </td>
                                        <td class="py-8 px-10 text-right">
                                            <?php if ($percent >= 40): ?>
                                                <span
                                                    class="text-emerald-500 font-black text-[11px] uppercase tracking-widest italic flex items-center justify-end">
                                                    <i class="fas fa-check-circle mr-2 text-[10px]"></i> Qualified
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="text-rose-500 font-black text-[11px] uppercase tracking-widest italic flex items-center justify-end">
                                                    <i class="fas fa-times-circle mr-2 text-[10px]"></i> Backlog
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-900 text-white">
                                    <td colspan="2" class="py-10 px-10">
                                        <h5 class="text-xl font-black uppercase tracking-widest leading-none">Aggregate Profile
                                        </h5>
                                        <p class="text-slate-400 text-xs mt-2 uppercase font-bold">Calculation based on
                                            published marks above.</p>
                                    </td>
                                    <td class="py-10 px-10 text-center">
                                        <div class="text-2xl font-black whitespace-nowrap">
                                            <?php echo $total_obtain; ?> <span
                                                class="text-[10px] text-slate-500 uppercase tracking-widest">/
                                                <?php echo $total_max; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-10 px-10 text-center">
                                        <div class="text-2xl font-black">
                                            <?php echo round(($total_obtain / $total_max) * 100, 2); ?>%
                                        </div>
                                    </td>
                                    <td class="py-10 px-10 text-right">
                                        <div class="flex flex-col items-end">
                                            <div class="w-32 h-2 bg-slate-800 rounded-full overflow-hidden mb-2">
                                                <div class="h-full bg-indigo-500 rounded-full"
                                                    style="width: <?php echo round(($total_obtain / $total_max) * 100); ?>%">
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Total
                                                GPA Performance</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>