<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$assignment_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$teacher_id = $_SESSION['user_id'];

// Handle Grading before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_submission'])) {
    csrf_guard();
    $submission_id = (int)$_POST['submission_id'];
    $grade = sanitize($_POST['grade']);
    $feedback = sanitize($_POST['feedback']);

    try {
        $stmt_grade = $pdo->prepare("UPDATE submissions SET grade = ?, feedback = ? WHERE id = ?");
        $stmt_grade->execute([$grade, $feedback, $submission_id]);
        set_flash_message('success', 'Submission graded successfully.');
        header("Location: submissions-view.php?id=$assignment_id");
        exit();
    } catch (PDOException $e) {
        set_flash_message('error', "Failed to grade submission.");
    }
}

$page_title = "Assignment Submissions";
require_once 'includes/header.php';

// Verify assignment ownership
$stmt_check = $pdo->prepare("SELECT a.*, s.name as subject_name 
                             FROM assignments a 
                             JOIN subjects s ON a.subject_id = s.id 
                             WHERE a.id = ? AND a.teacher_id = ?");
$stmt_check->execute([$assignment_id, $teacher_id]);
$assignment = $stmt_check->fetch();

if (!$assignment) {
    echo "<div class='py-20 text-center'><h3 class='text-xl font-bold'>Access Denied</h3></div>";
    require_once 'includes/footer.php';
    exit();
}

// Fetch submissions
$stmt_subs = $pdo->prepare("SELECT sub.*, u.full_name, st.roll_no 
                            FROM submissions sub
                            JOIN users u ON sub.student_id = u.id
                            JOIN students st ON st.user_id = u.id
                            WHERE sub.assignment_id = ?
                            ORDER BY sub.submitted_at DESC");
$stmt_subs->execute([$assignment_id]);
$submissions = $stmt_subs->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div class="flex items-center space-x-4">
        <a href="assignments.php" class="w-10 h-10 bg-white dark:bg-slate-800 rounded-xl shadow-soft flex items-center justify-center text-slate-400 hover:text-primary-600 transition-all border border-slate-100 dark:border-slate-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white"><?php echo $assignment['title']; ?></h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase font-bold tracking-widest"><?php echo $assignment['subject_name']; ?> • Submissions Review</p>
        </div>
    </div>
    <div class="bg-primary-50 dark:bg-primary-500/10 px-6 py-3 rounded-2xl border border-primary-100 dark:border-primary-500/20">
        <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest leading-none mb-1">Total Received</p>
        <p class="text-lg font-black text-slate-800 dark:text-white"><?php echo count($submissions); ?></p>
    </div>
</div>

<?php display_flash_message(); ?>

<div class="space-y-6">
    <?php if (empty($submissions)): ?>
        <div class="py-20 bg-white dark:bg-slate-800 rounded-[2.5rem] text-center border border-dashed border-slate-200 dark:border-slate-700">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fas fa-hourglass-start text-2xl"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">No students have submitted this task yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($submissions as $sub): ?>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center text-primary-600 font-black text-sm uppercase">
                            <?php echo strtoupper(substr($sub['full_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 dark:text-white"><?php echo $sub['full_name']; ?></h4>
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">ROLL: <?php echo $sub['roll_no']; ?> • <?php echo date('M d, H:i', strtotime($sub['submitted_at'])); ?></p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="../uploads/<?php echo $sub['file_path']; ?>" target="_blank" class="px-5 py-2.5 bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-300 text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-primary-50 dark:hover:bg-primary-500/10 hover:text-primary-600 transition-all border border-slate-100 dark:border-slate-700">
                            <i class="fas fa-file-pdf mr-2"></i>View Solution
                        </a>
                        
                        <?php if ($sub['grade']): ?>
                            <div class="px-5 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 text-[10px] font-bold uppercase tracking-widest rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                                Grade: <?php echo $sub['grade']; ?>
                            </div>
                        <?php else: ?>
                            <button onclick="openGradeModal(<?php echo $sub['id']; ?>, '<?php echo $sub['full_name']; ?>')" class="px-5 py-2.5 bg-primary-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-primary-700 transition-all shadow-soft">
                                Evaluate
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($sub['feedback']): ?>
                    <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-start space-x-3">
                        <i class="fas fa-comment-dots text-primary-500 mt-1 text-xs"></i>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed italic">"<?php echo $sub['feedback']; ?>"</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Grading Modal -->
<div id="grade_modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 w-full max-md rounded-[2.5rem] shadow-premium p-8 lg:p-10 transform transition-all">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">Evaluate</h3>
                <p id="student_name_display" class="text-xs font-bold text-primary-600 uppercase tracking-widest mt-1"></p>
            </div>
            <button onclick="document.getElementById('grade_modal').classList.add('hidden')" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="grade_submission" value="1">
            <input type="hidden" name="submission_id" id="modal_sub_id">
            
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Assign Grade</label>
                <select name="grade" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="A+">A+ (Excellent)</option>
                    <option value="A">A (Very Good)</option>
                    <option value="B">B (Good)</option>
                    <option value="C">C (Satisfactory)</option>
                    <option value="D">D (Needs Improvement)</option>
                    <option value="F">F (Failed)</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Feedback Comment</label>
                <textarea name="feedback" rows="4" placeholder="Well done! Keep it up..."
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>

            <div class="flex gap-4 pt-2">
                <button type="button" onclick="document.getElementById('grade_modal').classList.add('hidden')"
                    class="flex-1 py-4 bg-slate-100 dark:bg-slate-900 text-slate-500 font-bold rounded-2xl">Skip</button>
                <button type="submit" class="flex-1 py-4 bg-primary-600 text-white font-bold rounded-2xl shadow-premium hover:bg-primary-700 transition-all">Grade Now</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGradeModal(subId, name) {
    document.getElementById('modal_sub_id').value = subId;
    document.getElementById('student_name_display').innerText = name;
    document.getElementById('grade_modal').classList.remove('hidden');
}
</script>

<?php require_once 'includes/footer.php'; ?>
