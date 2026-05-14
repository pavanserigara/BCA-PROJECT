<?php
$page_title = "Evaluation Portfolio";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Fetch mark records
$stmt_res = $pdo->prepare("SELECT m.*, s.name as subject_name, s.code, e.name as exam_name, e.type as exam_type, e.exam_date
                           FROM marks m
                           JOIN subjects s ON m.subject_id = s.id
                           JOIN exams e ON m.exam_id = e.id
                           WHERE m.student_id = ?
                           ORDER BY e.exam_date DESC, s.name ASC");
$stmt_res->execute([$student_id]);
$results = $stmt_res->fetchAll();

// Grouping logic
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

<div class="mb-10">
    <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Academic Performance</h2>
    <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium italic">Validated assessment reports and examination transcripts.</p>
</div>

<div class="space-y-12 mb-20 pb-10">
    <?php if (empty($grouped_results)): ?>
        <div class="bg-white dark:bg-slate-800 p-20 rounded-[3rem] text-center border-2 border-dashed border-slate-100 dark:border-slate-700 shadow-premium">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                <i class="fas fa-graduation-cap text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-wider">Results Awaited</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Evaluation for your current semester is in progress.</p>
        </div>
    <?php else: ?>
        <?php foreach ($grouped_results as $exam_id => $exam): ?>
            <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-premium border border-slate-100 dark:border-slate-700/50 overflow-hidden animate-in fade-in slide-in-from-bottom-8 duration-700">
                
                <div class="p-8 md:p-10 border-b border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center space-x-3 mb-3">
                            <span class="px-3 py-1 bg-primary-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-lg shadow-primary-500/20">
                                <?php echo $exam['exam_type']; ?>
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <i class="fas fa-calendar-alt mr-1"></i> <?php echo date('F d, Y', strtotime($exam['exam_date'])); ?>
                            </span>
                        </div>
                        <h4 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight"><?php echo $exam['exam_name']; ?></h4>
                    </div>

                    <button class="px-6 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-primary-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-primary-600 hover:text-white transition-all shadow-soft active:scale-95 group">
                        <i class="fas fa-file-pdf mr-2 group-hover:animate-bounce"></i>Download Marksheet
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest bg-white dark:bg-slate-800 border-b border-slate-50 dark:border-slate-700/50">
                                <th class="py-6 px-10">Academic Unit</th>
                                <th class="py-6 px-10 text-center">Engagement</th>
                                <th class="py-6 px-10 text-center">Score (Max)</th>
                                <th class="py-6 px-10 text-right pr-10">Standing</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                            <?php
                            $total_obtain = 0; $total_max = 0;
                            foreach ($exam['marks'] as $mks):
                                $total_obtain += $mks['marks_obtained'];
                                $total_max += $mks['max_marks'];
                                $percent = round(($mks['marks_obtained'] / $mks['max_marks']) * 100, 1);
                                ?>
                                <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                                    <td class="py-8 px-10">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 text-primary-600 rounded-xl flex items-center justify-center font-black text-[10px]">
                                                <?php echo $mks['code']; ?>
                                            </div>
                                            <h6 class="text-sm font-black text-slate-800 dark:text-white"><?php echo $mks['subject_name']; ?></h6>
                                        </div>
                                    </td>
                                    <td class="py-8 px-10 text-center">
                                        <div class="flex items-center justify-center">
                                            <div class="w-32 h-1.5 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden">
                                                <div class="h-full bg-primary-500 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-8 px-10 text-center">
                                        <span class="text-lg font-black text-slate-800 dark:text-white"><?php echo $mks['marks_obtained']; ?></span>
                                        <span class="text-[10px] font-black text-slate-400">/ <?php echo $mks['max_marks']; ?></span>
                                    </td>
                                    <td class="py-8 px-10 text-right pr-10">
                                        <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg <?php echo $percent >= 40 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600'; ?>">
                                            <?php echo $percent >= 40 ? 'Qualified' : 'Backlog'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-950 text-white border-t border-slate-900">
                            <tr>
                                <td colspan="2" class="py-10 px-10">
                                    <h5 class="text-xl font-black uppercase tracking-[0.1em] text-white">Consolidated Profile</h5>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1 italic">Verified Academic Transcript Grade</p>
                                </td>
                                <td class="py-10 px-10 text-center">
                                    <div class="text-3xl font-black"><?php echo $total_obtain; ?> <span class="text-[10px] text-slate-500">/ <?php echo $total_max; ?></span></div>
                                </td>
                                <td class="py-10 px-10 text-right pr-10">
                                    <div class="flex flex-col items-end">
                                        <div class="text-3xl font-black text-primary-400"><?php echo round(($total_obtain / $total_max) * 100, 1); ?>%</div>
                                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mt-1">Aggregate Standing</p>
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

<?php require_once 'includes/footer.php'; ?>