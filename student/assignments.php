<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('student')) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Get Student info
$stmt_std = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt_std->execute([$student_id]);
$student = $stmt_std->fetch();

// Handle Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_task'])) {
    csrf_guard();
    $assignment_id = (int)$_POST['assignment_id'];
    $submission_text = sanitize($_POST['submission_text']);
    
    $file_path = NULL;
    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === 0) {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'zip'];
        $ext = strtolower(pathinfo($_FILES['submission_file']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $upload_dir = '../uploads/submissions/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $filename = 'sub_' . $assignment_id . '_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
            $dest = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $dest)) {
                $file_path = 'submissions/' . $filename;
            }
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, submission_text, file_path, status) 
                               VALUES (?, ?, ?, ?, 'Submitted') 
                               ON DUPLICATE KEY UPDATE submission_text = VALUES(submission_text), file_path = VALUES(file_path), status = 'Submitted'");
        $stmt->execute([$assignment_id, $student_id, $submission_text, $file_path]);
        set_flash_message('success', 'Assignment submitted successfully!');
        header("Location: assignments.php");
        exit();
    } catch (PDOException $e) {
        set_flash_message('error', "Failed: " . $e->getMessage());
    }
}

$page_title = "Workspace";
require_once 'includes/header.php';

// Fetch assignments
$stmt_assign = $pdo->prepare("SELECT a.*, s.name as subject_name, s.code as subject_code, u.full_name as teacher_name,
                               (SELECT status FROM submissions WHERE assignment_id = a.id AND student_id = ?) as submission_status
                               FROM assignments a
                               JOIN subjects s ON a.subject_id = s.id
                               JOIN users u ON a.teacher_id = u.id
                               WHERE s.course_id = ? AND s.semester = ?
                               ORDER BY a.deadline ASC");
$stmt_assign->execute([$student_id, $student['course_id'], $student['semester']]);
$assignments = $stmt_assign->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Assignment Workspace</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Track deadlines and submit your academic tasks.</p>
    </div>
    
    <div class="flex items-center space-x-4">
        <div class="text-right hidden sm:block">
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Active Tasks</p>
            <p class="text-sm font-black text-slate-800 dark:text-white"><?php echo count($assignments); ?> Modules</p>
        </div>
        <div class="w-12 h-12 bg-primary-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-primary-500/20">
            <i class="fas fa-tasks"></i>
        </div>
    </div>
</div>

<?php display_flash_message(); ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-20 pb-10">
    <?php if (empty($assignments)): ?>
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-20 rounded-[3rem] text-center border-2 border-dashed border-slate-200 dark:border-slate-700">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                <i class="fas fa-wind text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-wider">Workspace Clear</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2">No active assignments found for your current semester.</p>
        </div>
    <?php else: ?>
        <?php foreach ($assignments as $task):
            $is_submitted = $task['submission_status'] == 'Submitted' || $task['submission_status'] == 'Graded';
            $deadline = strtotime($task['deadline']);
            $is_late = $deadline < time() && !$is_submitted;
            $due_date = date('M d, Y • h:i A', $deadline);
            ?>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700/50 group hover:shadow-premium transition-all duration-500 relative flex flex-col justify-between overflow-hidden">
                
                <?php if ($is_submitted): ?>
                    <div class="absolute top-8 right-8 flex items-center space-x-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-black uppercase tracking-widest rounded-full border border-emerald-100 dark:border-emerald-500/20">
                        <i class="fas fa-circle-check"></i>
                        <span>Completed</span>
                    </div>
                <?php elseif ($is_late): ?>
                    <div class="absolute top-8 right-8 flex items-center space-x-1.5 px-3 py-1 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[9px] font-black uppercase tracking-widest rounded-full border border-rose-100 dark:border-rose-500/20 animate-pulse">
                        <i class="fas fa-clock"></i>
                        <span>Overdue</span>
                    </div>
                <?php endif; ?>

                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <span class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest bg-primary-50 dark:bg-primary-500/10 px-2.5 py-1 rounded-lg"><?php echo $task['subject_code']; ?></span>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"><?php echo $task['teacher_name']; ?></span>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-4 group-hover:text-primary-600 transition-colors leading-tight"><?php echo $task['title']; ?></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed italic mb-8 line-clamp-2"><?php echo $task['description']; ?></p>

                    <?php if ($task['file_path']): ?>
                        <a href="../uploads/<?php echo $task['file_path']; ?>" target="_blank" class="inline-flex items-center space-x-2 text-[10px] font-bold text-primary-600 hover:underline mb-8">
                            <i class="fas fa-paperclip"></i>
                            <span>View Task Document</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="pt-8 border-t border-slate-50 dark:border-slate-700/50 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1 italic">Submission Window</p>
                        <p class="text-xs font-black text-slate-700 dark:text-slate-300"><?php echo $due_date; ?></p>
                    </div>

                    <?php if (!$is_submitted): ?>
                        <button onclick="openSubmitModal(<?php echo $task['id']; ?>, '<?php echo addslashes($task['title']); ?>')" class="px-6 py-3 bg-slate-900 dark:bg-primary-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-primary-600 dark:hover:bg-primary-700 transition-all shadow-lg active:scale-95 flex items-center space-x-2">
                             <span>Submit</span>
                             <i class="fas fa-arrow-up-from-bracket"></i>
                        </button>
                    <?php else: ?>
                        <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner">
                            <i class="fas fa-check"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Submit Modal -->
<div id="submit_modal" class="fixed inset-0 bg-slate-900/60 transition-all z-[100] hidden flex items-center justify-center p-4 backdrop-blur-md">
    <div class="bg-white dark:bg-slate-800 w-full max-w-xl rounded-[2.5rem] shadow-premium p-8 lg:p-12 transform scale-95 opacity-0 transition-all duration-300" id="modal_content">
        <div class="flex items-center justify-between mb-8">
            <div class="min-w-0">
                <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight truncate pr-4" id="modal_task_title"></h3>
                <p class="text-xs font-bold text-primary-600 uppercase tracking-widest mt-1 italic">Final Submision Tool</p>
            </div>
            <button onclick="closeSubmitModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-500 hover:text-slate-800 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="submit_task" value="1">
            <input type="hidden" name="assignment_id" id="modal_assignment_id">

            <div>
                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">Work Summary / Link</label>
                <textarea name="submission_text" rows="5" required placeholder="Type your response or paste links here..."
                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 rounded-2xl font-medium text-sm text-slate-800 dark:text-white outline-none focus:ring-2 focus:ring-primary-500 transition-all"></textarea>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">Attach File (PDF/DOC)</label>
                <div class="relative group">
                    <input type="file" name="submission_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="p-8 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-900 text-center group-hover:border-primary-500 transition-all pointer-events-none">
                        <i class="fas fa-cloud-arrow-up text-3xl text-slate-300 dark:text-slate-600 group-hover:text-primary-500 mb-4"></i>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1 group-hover:text-primary-500">Drop your file here</p>
                        <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 italic">Individual document submission only</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="closeSubmitModal()" class="flex-1 py-4 bg-slate-100 dark:bg-slate-900 text-slate-500 font-black rounded-2xl uppercase text-[10px] tracking-widest transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-primary-600 text-white font-black rounded-2xl shadow-lg shadow-primary-500/20 uppercase text-[10px] tracking-widest hover:bg-primary-700 transition-all">Upload Final Work</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSubmitModal(id, title) {
        document.getElementById('modal_assignment_id').value = id;
        document.getElementById('modal_task_title').textContent = title;
        const modal = document.getElementById('submit_modal');
        const content = document.getElementById('modal_content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeSubmitModal() {
        const modal = document.getElementById('submit_modal');
        const content = document.getElementById('modal_content');
        content.classList.add('scale-95', 'opacity-0');
        content.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>

<?php require_once 'includes/footer.php'; ?>