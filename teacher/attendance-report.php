<?php
$page_title = "Attendance Reports";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];
$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

$selected_subject_id = $_GET['subject_id'] ?? null;
$report_data = [];
$total_classes = 0;

if ($selected_subject_id) {
    // Get total classes held
    $stmt_classes = $pdo->prepare("SELECT COUNT(DISTINCT date) FROM attendance WHERE subject_id = ?");
    $stmt_classes->execute([$selected_subject_id]);
    $total_classes = $stmt_classes->fetchColumn();

    // Get student-wise summary
    $stmt_report = $pdo->prepare("SELECT 
        u.full_name, 
        s.roll_no,
        COUNT(CASE WHEN a.status = 'Present' THEN 1 END) as present_count,
        COUNT(CASE WHEN a.status = 'Absent' THEN 1 END) as absent_count,
        COUNT(CASE WHEN a.status = 'Late' THEN 1 END) as late_count,
        COUNT(CASE WHEN a.status = 'Leave' THEN 1 END) as leave_count,
        COUNT(a.status) as total_marked
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN attendance a ON s.user_id = a.student_id AND a.subject_id = ?
        WHERE s.course_id = (SELECT course_id FROM subjects WHERE id = ?)
        AND s.semester = (SELECT semester FROM subjects WHERE id = ?)
        GROUP BY s.user_id
        ORDER BY u.full_name ASC");
    
    $stmt_sub_info = $pdo->prepare("SELECT course_id, semester FROM subjects WHERE id = ?");
    $stmt_sub_info->execute([$selected_subject_id]);
    $sub_info = $stmt_sub_info->fetch();
    
    if ($sub_info) {
        $stmt_report->execute([$selected_subject_id, $selected_subject_id, $selected_subject_id]);
        $report_data = $stmt_report->fetchAll();
    }
}
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Attendance Analytics</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review student performance and export data.</p>
    </div>
    <?php if ($selected_subject_id && !empty($report_data)): ?>
        <button onclick="exportToCSV()" class="inline-flex items-center space-x-2 px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
            <i class="fas fa-file-export"></i>
            <span>Export CSV</span>
        </button>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 mb-8">
    <form method="GET" class="flex flex-col md:flex-row md:items-end gap-6">
        <div class="flex-1">
            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Select Subject for Analysis</label>
            <select name="subject_id" required onchange="this.form.submit()"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Choose a subject...</option>
                <?php foreach ($my_subjects as $sub): ?>
                    <option value="<?php echo $sub['id']; ?>" <?php echo ($selected_subject_id == $sub['id']) ? 'selected' : ''; ?>>
                        <?php echo $sub['name']; ?> (<?php echo $sub['course_name']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($selected_subject_id): ?>
        <div class="bg-primary-50 dark:bg-primary-500/10 px-6 py-3 rounded-2xl border border-primary-100 dark:border-primary-500/20">
            <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest">Classes Held</p>
            <p class="text-lg font-black text-slate-800 dark:text-white"><?php echo $total_classes; ?></p>
        </div>
        <?php endif; ?>
    </form>
</div>

<?php if ($selected_subject_id): ?>
    <?php if (empty($report_data)): ?>
        <div class="py-20 text-center">
            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fas fa-chart-line text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">No data available</h3>
            <p class="text-sm text-slate-500 mt-1">No attendance has been marked for this subject yet.</p>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="reportTable">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50">
                            <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Student</th>
                            <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">P</th>
                            <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">A</th>
                            <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">L</th>
                            <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">LV</th>
                            <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Total</th>
                            <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php foreach ($report_data as $row): 
                            $percentage = $total_classes > 0 ? round(($row['present_count'] / $total_classes) * 100, 1) : 0;
                            $color_class = $percentage >= 75 ? 'text-emerald-500' : ($percentage >= 60 ? 'text-amber-500' : 'text-rose-500');
                        ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                <td class="py-4 px-6">
                                    <p class="font-bold text-slate-800 dark:text-white"><?php echo $row['full_name']; ?></p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase"><?php echo $row['roll_no']; ?></p>
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-emerald-500"><?php echo $row['present_count']; ?></td>
                                <td class="py-4 px-6 text-center font-bold text-rose-500"><?php echo $row['absent_count']; ?></td>
                                <td class="py-4 px-6 text-center font-bold text-amber-500"><?php echo $row['late_count']; ?></td>
                                <td class="py-4 px-6 text-center font-bold text-blue-500"><?php echo $row['leave_count']; ?></td>
                                <td class="py-4 px-6 text-center font-bold text-slate-700 dark:text-slate-300"><?php echo $row['total_marked']; ?></td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-current <?php echo $color_class; ?>" style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                        <span class="text-xs font-black <?php echo $color_class; ?>"><?php echo $percentage; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        function exportToCSV() {
            let csv = [];
            let rows = document.querySelectorAll("#reportTable tr");
            
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length; j++) {
                    let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                    row.push('"' + text + '"');
                }
                csv.push(row.join(","));
            }

            let csvString = csv.join("\n");
            let filename = "Attendance_Report_<?php echo date('Y-m-d'); ?>.csv";
            let link = document.createElement("a");
            link.style.display = 'none';
            link.setAttribute("target", "_blank");
            link.setAttribute("href", "data:text/csv;charset=utf-8," + encodeURIComponent(csvString));
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        </script>
    <?php endif; ?>
<?php else: ?>
    <div class="py-20 text-center">
        <div class="w-20 h-20 bg-primary-50 dark:bg-primary-500/10 rounded-3xl flex items-center justify-center mx-auto mb-4 text-primary-300">
            <i class="fas fa-magnifying-glass-chart text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Analyze Attendance</h3>
        <p class="text-sm text-slate-500 mt-1">Select a subject from the menu above to generate a summary report.</p>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
