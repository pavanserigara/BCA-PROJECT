<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Handle New Assignment Upload before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_assignment'])) {
    $title = sanitize($_POST['title']);
    $description = $_POST['description'];
    $subject_id = (int)$_POST['subject_id'];
    $deadline = $_POST['deadline'];
    
    $file_path = NULL;
    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === 0) {
        $upload_dir = '../uploads/assignments/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = 'assignment_' . time() . '_' . $_FILES['assignment_file']['name'];
        $dest = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $dest)) {
            $file_path = 'assignments/' . $filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO assignments (teacher_id, subject_id, title, description, file_path, deadline) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$teacher_id, $subject_id, $title, $description, $file_path, $deadline]);
        set_flash_message('success', 'Assignment published successfully!');
        header("Location: assignments.php");
        exit();
    } catch (PDOException $e) {
        set_flash_message('error', "Failed to create assignment: " . $e->getMessage());
    }
}

$page_title = "Assignments";
require_once 'includes/header.php';

// Fetch assigned subjects
$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

// Fetch published assignments
$stmt_published = $pdo->prepare("SELECT a.*, s.name as subject_name, (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id) as submission_count 
                                  FROM assignments a 
                                  JOIN subjects s ON a.subject_id = s.id 
                                  WHERE a.teacher_id = ? ORDER BY a.created_at DESC");
$stmt_published->execute([$teacher_id]);
$published_assignments = $stmt_published->fetchAll();
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Assignments</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage tasks and evaluate student submissions.</p>
    </div>
    <button onclick="document.getElementById('upload_modal').classList.remove('hidden')"
        class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-2xl font-bold flex items-center space-x-2 shadow-premium transition-all transform active:scale-95">
        <i class="fas fa-plus-circle"></i>
        <span>Post New Task</span>
    </button>
</div>

<?php display_flash_message(); ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($published_assignments)): ?>
        <div class="md:col-span-2 lg:col-span-3 py-20 bg-white dark:bg-slate-800 rounded-3xl text-center border border-dashed border-slate-200 dark:border-slate-700">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fas fa-file-invoice text-2xl"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">No assignments posted yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($published_assignments as $assign): ?>
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700 flex flex-col justify-between hover:border-primary-500 transition-all group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-primary-600 uppercase tracking-widest bg-primary-50 dark:bg-primary-500/10 px-2.5 py-1 rounded-lg">
                            <?php echo $assign['subject_name']; ?>
                        </span>
                        <span class="text-[10px] font-bold text-rose-500 uppercase tracking-widest flex items-center">
                            <i class="fas fa-calendar-times mr-1"></i>
                            Due <?php echo date('M d', strtotime($assign['deadline'])); ?>
                        </span>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-white leading-snug mb-2 group-hover:text-primary-600 transition-colors">
                        <?php echo $assign['title']; ?>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-6">
                        <?php echo strip_tags($assign['description']); ?>
                    </p>
                </div>

                <div class="pt-6 border-t border-slate-50 dark:border-slate-700 flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-slate-400">
                        <i class="fas fa-users text-[10px]"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest"><span class="text-slate-700 dark:text-white"><?php echo $assign['submission_count']; ?></span> Submissions</span>
                    </div>
                    <a href="submissions-view.php?id=<?php echo $assign['id']; ?>" class="w-9 h-9 bg-slate-50 dark:bg-slate-900 text-slate-400 rounded-xl flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="upload_modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-[2.5rem] shadow-premium p-8 lg:p-10 transform transition-all">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">New Assignment</h3>
            <button onclick="document.getElementById('upload_modal').classList.add('hidden')" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="upload_assignment" value="1">
            
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Title</label>
                <input type="text" name="title" required placeholder="Enter task title"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Subject</label>
                    <select name="subject_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Select</option>
                        <?php foreach ($my_subjects as $sub): ?>
                            <option value="<?php echo $sub['id']; ?>"><?php echo $sub['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Deadline</label>
                    <input type="datetime-local" name="deadline" required
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Attachment (PDF/Image)</label>
                <input type="file" name="assignment_file"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Instructions</label>
                <textarea name="description" rows="4" placeholder="Assignment details..."
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>

            <div class="flex gap-4 pt-2">
                <button type="button" onclick="document.getElementById('upload_modal').classList.add('hidden')"
                    class="flex-1 py-4 bg-slate-100 dark:bg-slate-900 text-slate-500 font-bold rounded-2xl">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-primary-600 text-white font-bold rounded-2xl shadow-premium hover:bg-primary-700 transition-all">Publish</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>