<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Handle Marks Entry before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enter_marks'])) {
    csrf_guard();
    $subject_id = (int)$_POST['subject_id'];
    $exam_name = sanitize($_POST['exam_name']);
    $max_marks = (float) $_POST['max_marks'];
    $marks_data = $_POST['marks'] ?? [];

    try {
        $pdo->beginTransaction();

        $stmt_exam = $pdo->prepare("INSERT INTO exams (name, course_id, semester, exam_date, type) VALUES (?, (SELECT course_id FROM subjects WHERE id = ?), (SELECT semester FROM subjects WHERE id = ?), ?, 'Internal')");
        $stmt_exam->execute([$exam_name, $subject_id, $subject_id, date('Y-m-d')]);
        $exam_id = $pdo->lastInsertId();

        $stmt_marks = $pdo->prepare("INSERT INTO marks (exam_id, student_id, subject_id, marks_obtained, max_marks) VALUES (?, ?, ?, ?, ?)");
        foreach ($marks_data as $std_id => $mks) {
            $stmt_marks->execute([$exam_id, $std_id, $subject_id, (float) $mks, $max_marks]);
        }

        $pdo->commit();
        set_flash_message('success', "Results for '$exam_name' published successfully.");
        header("Location: marks-entry.php?subject_id=$subject_id");
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        set_flash_message('error', "Error: " . $e->getMessage());
    }
}

$page_title = "Manage Grades";
require_once 'includes/header.php';

// Fetch assigned subjects
$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

// Handle selection
$selected_subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;
$students = [];
if ($selected_subject_id) {
    $stmt = $pdo->prepare("SELECT s.*, u.full_name FROM students s JOIN users u ON s.user_id = u.id WHERE s.course_id = (SELECT course_id FROM subjects WHERE id = ?)");
    $stmt->execute([$selected_subject_id]);
    $students = $stmt->fetchAll();
}
?>

<div class="mb-8">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Gradebook Submission</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Record assessment marks and academic performance.</p>
</div>

<!-- Selection Filter -->
<div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 mb-8">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
        <div>
            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Target Subject</label>
            <select name="subject_id" required onchange="this.form.submit()"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">Choose Subject</option>
                <?php foreach ($my_subjects as $sub): ?>
                    <option value="<?php echo $sub['id']; ?>" <?php echo $selected_subject_id == $sub['id'] ? 'selected' : ''; ?>>
                        <?php echo $sub['name']; ?> (<?php echo $sub['code']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-center justify-end">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right leading-loose">
                Course and semester context is derived automatically.
            </p>
        </div>
    </form>
</div>

<?php display_flash_message(); ?>

<?php if ($selected_subject_id): ?>
    <form action="" method="POST" class="bg-white dark:bg-slate-800 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 overflow-hidden mb-20">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <input type="hidden" name="enter_marks" value="1">
        <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">

        <!-- Exam Configuration -->
        <div class="p-8 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-8">
                <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Assessment Name</label>
                <input type="text" name="exam_name" required placeholder="e.g. Mid-Term Oct 2025"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="md:col-span-4">
                <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Maximum Marks</label>
                <input type="number" name="max_marks" required value="100" step="0.5"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <!-- Student List -->
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white dark:bg-slate-800">
                        <th class="py-4 px-8 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Roll No</th>
                        <th class="py-4 px-8 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">Student Name</th>
                        <th class="py-4 px-8 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 text-right">Marks Obtained</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="3" class="p-20 text-center text-slate-400 font-medium">No students enrolled in this course context.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors">
                                <td class="py-5 px-8 text-sm font-bold text-slate-600 dark:text-slate-400"><?php echo $student['roll_no']; ?></td>
                                <td class="py-5 px-8">
                                    <p class="text-sm font-bold text-slate-800 dark:text-white"><?php echo $student['full_name']; ?></p>
                                </td>
                                <td class="py-5 px-8 text-right">
                                    <input type="number" name="marks[<?php echo $student['user_id']; ?>]" required step="0.5" min="0" placeholder="0.0"
                                        class="w-24 px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-center text-sm font-black text-primary-600 outline-none focus:ring-2 focus:ring-primary-500">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-8 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between gap-6">
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500 italic">Evaluating <span class="text-primary-600 font-black"><?php echo count($students); ?></span> students.</p>
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-primary-600 text-white font-bold rounded-2xl shadow-premium hover:bg-primary-700 transition-all transform active:scale-95">
                Publish Results
            </button>
        </div>
    </form>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>