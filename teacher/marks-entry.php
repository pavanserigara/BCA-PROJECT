<?php
$page_title = "Evaluation & Marks";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

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

// Handle Marks Entry
if (isset($_POST['enter_marks'])) {
    $subject_id = $_POST['subject_id'];
    $exam_name = sanitize($_POST['exam_name']);
    $max_marks = (float) $_POST['max_marks'];
    $marks_data = $_POST['marks']; // user_id => marks_obtained

    try {
        $pdo->beginTransaction();

        // 1. Create Exam record if not exists or use generic
        $stmt_exam = $pdo->prepare("INSERT INTO exams (name, course_id, semester, exam_date, type) VALUES (?, (SELECT course_id FROM subjects WHERE id = ?), (SELECT semester FROM subjects WHERE id = ?), ?, 'Internal')");
        $stmt_exam->execute([$exam_name, $subject_id, $subject_id, date('Y-m-d')]);
        $exam_id = $pdo->lastInsertId();

        // 2. Insert Marks
        $stmt_marks = $pdo->prepare("INSERT INTO marks (exam_id, student_id, subject_id, marks_obtained, max_marks) VALUES (?, ?, ?, ?, ?)");
        foreach ($marks_data as $std_id => $mks) {
            $stmt_marks->execute([$exam_id, $std_id, $subject_id, (float) $mks, $max_marks]);
        }

        $pdo->commit();
        $success_message = "Evaluation for '$exam_name' completed successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Gradebook Submission</h2>
            <p class="text-slate-500 font-medium">Capture internal assessment and examination marks.</p>
        </div>
    </div>

    <!-- Configuration Panel -->
    <div class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Active
                    Subject</label>
                <select name="subject_id" required onchange="this.form.submit()"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    <option value="">Choose Subject...</option>
                    <?php foreach ($my_subjects as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo $selected_subject_id == $sub['id'] ? 'selected' : ''; ?>>
                            <?php echo $sub['name']; ?> (
                            <?php echo $sub['code']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center justify-end">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-loose text-right">
                    Selected Subject determines the course and semester automatically.
                </p>
            </div>
        </form>
    </div>

    <?php if ($selected_subject_id): ?>
        <form action="marks-entry.php" method="POST"
            class="bg-white rounded-[3rem] shadow-xl border border-indigo-100/50 overflow-hidden mb-20">
            <input type="hidden" name="enter_marks" value="1">
            <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">

            <!-- Exam Header -->
            <div class="p-10 border-b border-indigo-50 bg-slate-50/50 flex flex-col md:flex-row md:items-center gap-8">
                <div class="flex-1">
                    <label
                        class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Examination/Assessment
                        Name *</label>
                    <input type="text" name="exam_name" required placeholder="e.g. Mid-Term Assessment Oct 2025"
                        class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 outline-none font-bold text-slate-800">
                </div>
                <div class="w-full md:w-64">
                    <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Max
                        Marks *</label>
                    <input type="number" name="max_marks" required value="100" step="0.5"
                        class="w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 outline-none font-bold text-slate-800">
                </div>
            </div>

            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="py-6 px-10">Roll No</th>
                        <th class="py-6 px-10">Full Name</th>
                        <th class="py-6 px-10 text-right">Marks Obtained</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/30">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="3" class="p-20 text-center italic text-slate-400">No students found in this course.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="py-6 px-10 text-sm font-black text-slate-700">
                                    <?php echo $student['roll_no']; ?>
                                </td>
                                <td class="py-6 px-10">
                                    <span class="text-base font-bold text-slate-800">
                                        <?php echo $student['full_name']; ?>
                                    </span>
                                </td>
                                <td class="py-6 px-10 text-right">
                                    <input type="number" name="marks[<?php echo $student['user_id']; ?>]" required step="0.5"
                                        min="0"
                                        class="w-32 px-6 py-4 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-black text-center text-indigo-600">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="p-10 bg-slate-50 border-t border-indigo-50 flex items-center justify-between">
                <p class="text-sm font-bold text-slate-500 italic">Grading portal for <span class="text-indigo-600">
                        <?php echo count($students); ?>
                    </span> students.</p>
                <button type="submit"
                    class="px-12 py-5 bg-indigo-600 text-white font-black rounded-3xl shadow-2xl shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all uppercase tracking-widest text-xs">
                    Publish Evaluation Results
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>