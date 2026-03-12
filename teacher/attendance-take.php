<?php
$page_title = "Mark Attendance";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];
$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

$students = [];
$selected_subject = null;

if (isset($_GET['subject_id'])) {
    $selected_subject_id = $_GET['subject_id'];
    $stmt = $pdo->prepare("SELECT s.*, u.full_name FROM students s JOIN users u ON s.user_id = u.id WHERE s.course_id = (SELECT course_id FROM subjects WHERE id = ?)");
    $stmt->execute([$selected_subject_id]);
    $students = $stmt->fetchAll();

    $stmt_sub = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt_sub->execute([$selected_subject_id]);
    $selected_subject = $stmt_sub->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $subject_id = $_POST['subject_id'];
    $date = $_POST['date'];
    $attendance_data = $_POST['attendance']; // Array of student_id => status

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, subject_id, date, status, marked_by) VALUES (?, ?, ?, ?, ?)");
        foreach ($attendance_data as $std_id => $status) {
            $stmt->execute([$std_id, $subject_id, $date, $status, $teacher_id]);
        }
        $pdo->commit();
        $success = "Attendance recorded successfully for " . count($attendance_data) . " students.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Daily Attendance</h2>
            <p class="text-slate-500 font-medium">Capture student presence for your assigned lectures.</p>
        </div>
    </div>

    <!-- Subject Selection -->
    <div class="bg-white p-10 rounded-[2.5rem] border border-indigo-50 shadow-sm mb-8">
        <form method="GET" class="flex flex-col md:flex-row md:items-end gap-6">
            <div class="flex-1">
                <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Select
                    Lecture / Subject</label>
                <select name="subject_id" required onchange="this.form.submit()"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    <option value="">Choose Subject...</option>
                    <?php foreach ($my_subjects as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo ($selected_subject && $selected_subject['id'] == $sub['id']) ? 'selected' : ''; ?>>
                            <?php echo $sub['name']; ?> (
                            <?php echo $sub['code']; ?>) -
                            <?php echo $sub['course_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full md:w-64">
                <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Lecture
                    Date</label>
                <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
            </div>
        </form>
    </div>

    <?php if ($selected_subject): ?>
        <form action="attendance-take.php" method="POST"
            class="bg-white rounded-[3rem] shadow-xl border border-indigo-100/50 overflow-hidden mb-20 animate__animated animate__fadeInUp">
            <input type="hidden" name="mark_attendance" value="1">
            <input type="hidden" name="subject_id" value="<?php echo $selected_subject['id']; ?>">
            <input type="hidden" name="date" value="<?php echo $_GET['date'] ?? date('Y-m-d'); ?>">

            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50 bg-slate-50/50">
                        <th class="py-6 px-10">Roll No</th>
                        <th class="py-6 px-10">Student Name</th>
                        <th class="py-6 px-10 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/30">
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
                            <td class="py-6 px-10">
                                <div class="flex items-center justify-center space-x-6">
                                    <label class="relative flex items-center cursor-pointer group">
                                        <input type="radio" name="attendance[<?php echo $student['user_id']; ?>]"
                                            value="Present" checked class="peer sr-only">
                                        <div
                                            class="px-6 py-2.5 bg-slate-100 text-slate-400 font-black text-[10px] uppercase tracking-widest rounded-xl transition-all peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-emerald-200">
                                            P
                                        </div>
                                    </label>
                                    <label class="relative flex items-center cursor-pointer group">
                                        <input type="radio" name="attendance[<?php echo $student['user_id']; ?>]" value="Absent"
                                            class="peer sr-only">
                                        <div
                                            class="px-6 py-2.5 bg-slate-100 text-slate-400 font-black text-[10px] uppercase tracking-widest rounded-xl transition-all peer-checked:bg-rose-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-rose-200">
                                            A
                                        </div>
                                    </label>
                                    <label class="relative flex items-center cursor-pointer group">
                                        <input type="radio" name="attendance[<?php echo $student['user_id']; ?>]" value="Late"
                                            class="peer sr-only">
                                        <div
                                            class="px-6 py-2.5 bg-slate-100 text-slate-400 font-black text-[10px] uppercase tracking-widest rounded-xl transition-all peer-checked:bg-amber-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-amber-200">
                                            L
                                        </div>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="p-10 bg-slate-50 border-t border-indigo-50 flex items-center justify-between">
                <p class="text-sm font-bold text-slate-500 italic">Marking attendance for <span class="text-indigo-600">
                        <?php echo count($students); ?>
                    </span> students.</p>
                <button type="submit"
                    class="px-12 py-5 bg-indigo-600 text-white font-black rounded-3xl shadow-2xl shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition-all uppercase tracking-widest text-xs">
                    Submit Attendance Sheet
                </button>
            </div>
        </form>
    <?php elseif (isset($_GET['subject_id'])): ?>
        <div class="py-40 text-center">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
                <i class="fas fa-users-slash text-3xl"></i>
            </div>
            <h4 class="text-2xl font-black text-slate-800">No Students Found</h4>
            <p class="text-slate-500 mt-2">No students registered for this course yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../admin/includes/footer.php'; ?>