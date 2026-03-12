<?php
$page_title = "Assignment Desk";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Get Student info
$stmt_std = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt_std->execute([$student_id]);
$student = $stmt_std->fetch();

// Fetch assignments for student's course and semester
$stmt_assign = $pdo->prepare("SELECT a.*, s.name as subject_name, s.code as subject_code, u.full_name as teacher_name,
                               (SELECT status FROM submissions WHERE assignment_id = a.id AND student_id = ?) as submission_status
                               FROM assignments a
                               JOIN subjects s ON a.subject_id = s.id
                               JOIN users u ON a.teacher_id = u.id
                               WHERE s.course_id = ? AND s.semester = ?
                               ORDER BY a.deadline ASC");
$stmt_assign->execute([$student_id, $student['course_id'], $student['semester']]);
$assignments = $stmt_assign->fetchAll();

// Handle Submission
if (isset($_POST['submit_task'])) {
    $assignment_id = $_POST['assignment_id'];
    $submission_text = sanitize($_POST['submission_text']);

    // In a real app, handle file upload here. 
    $file_path = 'submissions/stud_' . $student_id . '_' . time() . '.pdf';

    try {
        $stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, submission_text, file_path, status) 
                               VALUES (?, ?, ?, ?, 'Submitted') 
                               ON DUPLICATE KEY UPDATE submission_text = VALUES(submission_text), file_path = VALUES(file_path), status = 'Submitted'");
        $stmt->execute([$assignment_id, $student_id, $submission_text, $file_path]);
        $success_message = "Assignment submitted successfully!";
        header("Location: assignments.php"); // Refresh
        exit();
    } catch (PDOException $e) {
        $error_message = "Failed to submit assignment: " . $e->getMessage();
    }
}
?>

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight">Assignment Portfolio</h2>
            <p class="text-slate-500 font-medium tracking-tight">Manage your project deadlines and document submissions.
            </p>
        </div>

        <div
            class="bg-indigo-600 px-8 py-4 rounded-[2rem] text-white shadow-xl shadow-indigo-100 flex items-center space-x-4">
            <div class="text-right">
                <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1">Academic Calendar</p>
                <p class="text-base font-bold text-white tracking-tight">Current Batch 2024-25</p>
            </div>
            <div
                class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center text-sm font-black">
                <i class="fas fa-calendar-alt"></i></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-20 pb-10">
        <?php if (empty($assignments)): ?>
            <div class="lg:col-span-2 bg-white p-20 rounded-[3rem] text-center border border-indigo-50 shadow-sm">
                <div
                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                    <i class="fas fa-tasks text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800">Clear Desk</h3>
                <p class="text-slate-500 mt-2">No pending assignments found in the system for your current semester.</p>
            </div>
        <?php else: ?>
            <?php foreach ($assignments as $task):
                $is_submitted = $task['submission_status'] == 'Submitted' || $task['submission_status'] == 'Graded';
                $is_late = strtotime($task['deadline']) < time();
                ?>
                <div
                    class="bg-white p-10 rounded-[3rem] shadow-sm border border-indigo-100/30 group hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
                    <?php if ($is_submitted): ?>
                        <div
                            class="absolute top-10 right-10 flex items-center space-x-2 text-emerald-500 font-black text-[10px] uppercase tracking-widest">
                            <i class="fas fa-check-double text-xs"></i>
                            <span>Completed</span>
                        </div>
                    <?php elseif ($is_late): ?>
                        <div
                            class="absolute top-10 right-10 flex items-center space-x-2 text-rose-500 font-black text-[10px] uppercase tracking-widest">
                            <i class="fas fa-exclamation-triangle text-xs"></i>
                            <span>Overdue</span>
                        </div>
                    <?php endif; ?>

                    <div>
                        <div class="flex items-center space-x-4 mb-8">
                            <div
                                class="px-3 py-1.5 bg-indigo-50 border border-indigo-100/50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                <?php echo $task['subject_name']; ?>
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">•
                                <?php echo $task['teacher_name']; ?>
                            </div>
                        </div>

                        <h3
                            class="text-2xl font-black text-slate-800 mb-4 group-hover:text-indigo-600 transition-colors leading-tight">
                            <?php echo $task['title']; ?>
                        </h3>
                        <p class="text-sm text-slate-500 italic mb-8 line-clamp-2">
                            <?php echo $task['description']; ?>
                        </p>
                    </div>

                    <div class="pt-8 border-t border-slate-50 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Due Deadline
                            </p>
                            <p class="text-sm font-bold text-slate-700">
                                <?php echo date('M d, Y - h:i A', strtotime($task['deadline'])); ?>
                            </p>
                        </div>

                        <?php if (!$is_submitted): ?>
                            <button
                                onclick="openSubmitModal(<?php echo $task['id']; ?>, '<?php echo addslashes($task['title']); ?>')"
                                class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center hover:bg-indigo-600 transition-all transform active:scale-90 shadow-xl shadow-slate-200">
                                <i class="fas fa-arrow-up text-xs"></i>
                            </button>
                        <?php else: ?>
                            <button
                                class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-sm opacity-50 cursor-default">
                                <i class="fas fa-check"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Submit Modal -->
<div id="submit_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-12 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">Assignment Submmit</h3>
                <p id="modal_task_title" class="text-indigo-500 font-bold text-sm tracking-tight italic mt-1"></p>
            </div>
            <button onclick="document.getElementById('submit_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 bg-slate-50 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="assignments.php" method="POST" class="space-y-8">
            <input type="hidden" name="submit_task" value="1">
            <input type="hidden" name="assignment_id" id="modal_assignment_id">

            <div class="space-y-6">
                <div>
                    <label
                        class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Submission
                        Text / Work Summary</label>
                    <textarea name="submission_text" rows="6" required
                        placeholder="Paste your code link or assignment summary here..."
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800"></textarea>
                </div>

                <div
                    class="p-8 border-2 border-dashed border-slate-200 rounded-[2rem] bg-slate-50 text-center group hover:border-indigo-500 transition-all cursor-pointer">
                    <i
                        class="fas fa-cloud-arrow-up text-3xl text-slate-400 group-hover:text-indigo-600 mb-4 transition-all"></i>
                    <p
                        class="text-sm font-black text-slate-400 uppercase tracking-widest mb-1 group-hover:text-indigo-600">
                        Select PDF or Document</p>
                    <p class="text-[10px] font-bold text-slate-400 italic">Max file size 5MB (Simulation only)</p>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button type="button" onclick="document.getElementById('submit_modal').classList.add('hidden')"
                    class="flex-1 py-5 bg-slate-50 text-slate-500 font-black rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs">Stay
                    on Desk</button>
                <button type="submit"
                    class="flex-1 py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                    Confirm Submission
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSubmitModal(id, title) {
        document.getElementById('modal_assignment_id').value = id;
        document.getElementById('modal_task_title').textContent = title;
        document.getElementById('submit_modal').classList.remove('hidden');
    }
</script>

<?php require_once '../admin/includes/footer.php'; ?>