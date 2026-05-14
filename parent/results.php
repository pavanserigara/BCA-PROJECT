<?php
$page_title = "Evaluation Matrix";
require_once 'includes/header.php';

$student_id = $_SESSION['linked_student_id'];

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

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Performance Matrix</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Comprehensive evaluation transcripts for <?php echo $_SESSION['linked_student_name']; ?>.</p>
    </div>
</div>

<div class="space-y-12 mb-20 pb-10">
    <?php if (empty($grouped_results)): ?>
        <div class="bg-white dark:bg-slate-800 p-20 rounded-[4rem] text-center border-2 border-dashed border-slate-100 dark:border-slate-800 shadow-premium">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                <i class="fas fa-microchip text-3xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic">Telemetry Pending</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 font-bold italic">Assessment cycles have not yet been synchronized for this period.</p>
        </div>
    <?php else: ?>
        <?php foreach ($grouped_results as $exam_id => $exam): ?>
            <div class="bg-white dark:bg-slate-800 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800 overflow-hidden">
                
                <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <span class="px-4 py-1.5 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-500/20 italic">
                                <?php echo $exam['exam_type']; ?>
                            </span>
                            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">
                                <i class="fas fa-clock-rotate-left mr-1"></i> Captured: <?php echo date('M d, Y', strtotime($exam['exam_date'])); ?>
                            </span>
                        </div>
                        <h4 class="text-2xl font-black text-slate-800 dark:text-white italic uppercase tracking-tight"><?php echo $exam['exam_name']; ?></h4>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="px-8 py-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center space-x-4 shadow-soft">
                            <div class="text-right">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic leading-none mb-1">Institutional Seal</p>
                                <p class="text-[10px] font-black text-emerald-600 uppercase italic tracking-widest">Verified Log</p>
                            </div>
                            <i class="fas fa-shield-halved text-emerald-500"></i>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest bg-white dark:bg-slate-800 border-b border-slate-50 dark:border-slate-700/50">
                                <th class="py-8 px-10">Academic Unit</th>
                                <th class="py-8 px-10 text-center">Trajectory</th>
                                <th class="py-8 px-10 text-center">Yield (Max)</th>
                                <th class="py-8 px-10 text-right pr-10">Standing</th>
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
                                    <td class="py-10 px-10">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-700 text-emerald-600 rounded-2xl flex items-center justify-center font-black text-[10px] italic shadow-soft group-hover:scale-110 transition-transform">
                                                <?php echo $mks['code']; ?>
                                            </div>
                                            <h6 class="text-sm font-black text-slate-800 dark:text-white uppercase italic leading-none"><?php echo $mks['subject_name']; ?></h6>
                                        </div>
                                    </td>
                                    <td class="py-10 px-10 text-center">
                                        <div class="flex items-center justify-center">
                                            <div class="w-32 h-2 bg-slate-50 dark:bg-slate-900 rounded-full overflow-hidden border border-slate-100 dark:border-slate-800">
                                                <div class="h-full bg-emerald-500 rounded-full" style="width: <?php echo $percent; ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-10 px-10 text-center">
                                        <span class="text-xl font-black text-slate-800 dark:text-white italic"><?php echo $mks['marks_obtained']; ?></span>
                                        <span class="text-[10px] font-black text-slate-400 italic">/ <?php echo $mks['max_marks']; ?></span>
                                    </td>
                                    <td class="py-10 px-10 text-right pr-10">
                                        <span class="px-4 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-xl italic <?php echo $percent >= 40 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border border-emerald-100 dark:border-emerald-500/20' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 border border-rose-100 dark:border-rose-500/20'; ?>">
                                            <?php echo $percent >= 40 ? 'Authorized' : 'Review Required'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-900 text-white">
                            <tr>
                                <td colspan="2" class="py-12 px-10">
                                    <h5 class="text-2xl font-black uppercase tracking-tight text-white italic leading-none mb-2">Aggregate Portfolio</h5>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest italic">Consolidated Assessment Data</p>
                                </td>
                                <td class="py-12 px-10 text-center">
                                    <div class="text-4xl font-black italic"><?php echo $total_obtain; ?> <span class="text-[10px] text-slate-500 italic">/ <?php echo $total_max; ?></span></div>
                                </td>
                                <td class="py-12 px-10 text-right pr-10">
                                    <div class="flex flex-col items-end">
                                        <div class="text-4xl font-black text-emerald-400 italic"><?php echo $total_max > 0 ? round(($total_obtain / $total_max) * 100, 1) : 0; ?>%</div>
                                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2 italic leading-none">Net Performance Index</p>
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
