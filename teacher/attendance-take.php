<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Handle POST before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    csrf_guard();
    $subject_id = (int) $_POST['subject_id'];
    $date = $_POST['date'];
    $attendance_data = $_POST['attendance'] ?? [];
    $remarks_data = $_POST['remarks'] ?? [];

    if ($date > date('Y-m-d')) {
        set_flash_message('error', 'You cannot mark attendance for future dates.');
        header("Location: attendance-take.php?subject_id=$subject_id&date=$date");
        exit();
    }

    if (!empty($attendance_data)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO attendance (student_id, subject_id, date, status, marked_by, remarks)
                                   VALUES (:student_id, :subject_id, :date, :status, :marked_by, :remarks)
                                   ON DUPLICATE KEY UPDATE 
                                   status = :update_status, 
                                   remarks = :update_remarks,
                                   marked_by = :update_marked_by");

            foreach ($attendance_data as $std_id => $status) {
                $rem = $remarks_data[$std_id] ?? '';
                $stmt->execute([
                    'student_id' => (int)$std_id,
                    'subject_id' => $subject_id,
                    'date' => $date,
                    'status' => $status,
                    'marked_by' => $teacher_id,
                    'remarks' => $rem,
                    'update_status' => $status,
                    'update_remarks' => $rem,
                    'update_marked_by' => $teacher_id
                ]);
            }
            
            $pdo->commit();
            set_flash_message('success', 'Attendance for ' . count($attendance_data) . ' students recorded successfully.');
            header("Location: attendance-take.php?subject_id=$subject_id&date=$date");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash_message('error', 'Database Error: ' . $e->getMessage());
        }
    } else {
        set_flash_message('error', 'No attendance data received.');
    }
}

$page_title = "Take Attendance";
require_once 'includes/header.php';

$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

$students = [];
$attendance_records = [];
$selected_subject = null;
$selected_subject_id = null;
$selected_date = $_GET['date'] ?? date('Y-m-d');

if (isset($_GET['subject_id'])) {
    $selected_subject_id = (int) $_GET['subject_id'];
    
    $stmt_sub = $pdo->prepare("SELECT s.*, c.name as course_name 
                               FROM subjects s 
                               JOIN courses c ON s.course_id = c.id
                               JOIN teacher_subjects ts ON ts.subject_id = s.id
                               WHERE s.id = ? AND ts.teacher_id = ?");
    $stmt_sub->execute([$selected_subject_id, $teacher_id]);
    $selected_subject = $stmt_sub->fetch();

    if ($selected_subject) {
        $stmt_att = $pdo->prepare("SELECT student_id, status, remarks FROM attendance WHERE subject_id = ? AND date = ?");
        $stmt_att->execute([$selected_subject_id, $selected_date]);
        $rows = $stmt_att->fetchAll();
        foreach ($rows as $row) {
            $attendance_records[$row['student_id']] = $row;
        }

        $stmt = $pdo->prepare("SELECT s.*, u.full_name 
                               FROM students s 
                               JOIN users u ON s.user_id = u.id 
                               WHERE s.course_id = ? AND s.semester = ?
                               ORDER BY u.full_name ASC");
        $stmt->execute([(int) $selected_subject['course_id'], (int) $selected_subject['semester']]);
        $students = $stmt->fetchAll();
    }
}
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Daily Attendance</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Select a subject and date to manage student rolls.</p>
    </div>
    <a href="attendance-report.php" class="inline-flex items-center space-x-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-xl text-[10px] font-bold uppercase tracking-widest border border-slate-100 dark:border-slate-700 hover:text-primary-600 transition-all shadow-soft">
        <i class="fas fa-chart-line"></i>
        <span>View Analytics</span>
    </a>
</div>

<!-- Selection Filter -->
<div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 mb-8">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div>
            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Subject</label>
            <select name="subject_id" required onchange="this.form.submit()"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Select Subject</option>
                <?php foreach ($my_subjects as $sub): ?>
                    <option value="<?php echo $sub['id']; ?>" <?php echo ($selected_subject_id == $sub['id']) ? 'selected' : ''; ?>>
                        <?php echo $sub['name']; ?> (<?php echo $sub['course_name']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Date</label>
            <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
        </div>
    </form>
</div>

<?php display_flash_message(); ?>

<?php if ($selected_subject): ?>
    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <input type="hidden" name="mark_attendance" value="1">
        <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
        <input type="hidden" name="date" value="<?php echo $selected_date; ?>">

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white dark:bg-slate-800 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-left">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-slate-50 dark:bg-slate-900 shadow-sm">
                        <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Student</th>
                        <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Status</th>
                        <th class="py-4 px-6 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php foreach ($students as $student): 
                        $existing = $attendance_records[$student['user_id']] ?? null;
                        $status = $existing['status'] ?? 'Present';
                    ?>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-5 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 font-bold text-xs uppercase shadow-inner">
                                        <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-white"><?php echo $student['full_name']; ?></p>
                                        <p class="text-[10px] text-slate-400 font-bold tracking-widest">ROLL: <?php echo $student['roll_no']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-5 px-6">
                                <div class="flex items-center justify-center space-x-2">
                                    <?php 
                                    $opts = [
                                        ['Present', 'text-emerald-600', 'bg-emerald-50 dark:bg-emerald-500/10'],
                                        ['Absent', 'text-rose-600', 'bg-rose-50 dark:bg-rose-500/10'],
                                        ['Late', 'text-amber-600', 'bg-amber-50 dark:bg-amber-500/10'],
                                        ['Leave', 'text-blue-600', 'bg-blue-50 dark:bg-blue-500/10']
                                    ];
                                    foreach ($opts as $opt):
                                    ?>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendance[<?php echo $student['user_id']; ?>]" value="<?php echo $opt[0]; ?>" <?php echo ($status === $opt[0]) ? 'checked' : ''; ?> class="peer sr-only">
                                            <div class="px-3 py-1.5 rounded-lg border border-transparent peer-checked:bg-primary-600 peer-checked:text-white peer-checked:shadow-soft text-[10px] font-bold transition-all <?php echo $opt[2]; ?> <?php echo $opt[1]; ?> hover:scale-105">
                                                <?php echo $opt[0]; ?>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="py-5 px-6">
                                <input type="text" name="remarks[<?php echo $student['user_id']; ?>]" value="<?php echo htmlspecialchars($existing['remarks'] ?? ''); ?>" placeholder="Add note..."
                                    class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-white outline-none focus:ring-1 focus:ring-primary-500">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div class="md:hidden space-y-4">
            <?php foreach ($students as $student): 
                $existing = $attendance_records[$student['user_id']] ?? null;
                $status = $existing['status'] ?? 'Present';
            ?>
                <div class="bg-white dark:bg-slate-800 p-5 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 font-bold text-xs uppercase shadow-inner">
                            <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-white"><?php echo $student['full_name']; ?></p>
                            <p class="text-[10px] text-slate-400 font-bold">ROLL: <?php echo $student['roll_no']; ?></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <?php foreach ($opts as $opt): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="attendance[<?php echo $student['user_id']; ?>]" value="<?php echo $opt[0]; ?>" <?php echo ($status === $opt[0]) ? 'checked' : ''; ?> class="peer sr-only">
                                <div class="py-2 rounded-lg text-center border border-transparent peer-checked:bg-primary-600 peer-checked:text-white text-[9px] font-bold <?php echo $opt[2]; ?> <?php echo $opt[1]; ?>">
                                    <?php echo $opt[0]; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <input type="text" name="remarks[<?php echo $student['user_id']; ?>]" value="<?php echo htmlspecialchars($existing['remarks'] ?? ''); ?>" placeholder="Remarks"
                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-700 dark:text-white outline-none">
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-primary-600 text-white font-bold rounded-2xl shadow-premium hover:bg-primary-700 hover:scale-[1.02] transition-all transform active:scale-95">
                Save Attendance
            </button>
        </div>
    </form>
<?php elseif (isset($_GET['subject_id'])): ?>
    <div class="py-20 text-center">
        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-4 text-slate-300">
            <i class="fas fa-triangle-exclamation text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Subject context not found</h3>
        <p class="text-sm text-slate-500 mt-1">We couldn't load the student list for this subject. Please verify your assignments.</p>
    </div>
<?php else: ?>
    <div class="py-20 text-center">
        <div class="w-20 h-20 bg-primary-50 dark:bg-primary-500/10 rounded-3xl flex items-center justify-center mx-auto mb-4 text-primary-300">
            <i class="fas fa-hand-pointer text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Ready to start?</h3>
        <p class="text-sm text-slate-500 mt-1">Select a subject from the menu above to begin taking attendance.</p>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>