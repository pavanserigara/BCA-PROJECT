<?php
$page_title = "Assignment Desk";
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

// Handle New Assignment Upload
if (isset($_POST['upload_assignment'])) {
    $title = sanitize($_POST['title']);
    $description = $_POST['description'];
    $subject_id = $_POST['subject_id'];
    $deadline = $_POST['deadline'];

    // In a real app, handle file upload here. For now, we'll store a placeholder or dummy path.
    $file_path = 'assignments/doc_' . time() . '.pdf';

    try {
        $stmt = $pdo->prepare("INSERT INTO assignments (teacher_id, subject_id, title, description, file_path, deadline) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$teacher_id, $subject_id, $title, $description, $file_path, $deadline]);
        $success_message = "Assignment published successfully!";
    } catch (PDOException $e) {
        $error_message = "Failed to create assignment: " . $e->getMessage();
    }
}

// Fetch published assignments
$stmt_published = $pdo->prepare("SELECT a.*, s.name as subject_name, (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id) as submission_count 
                                  FROM assignments a 
                                  JOIN subjects s ON a.subject_id = s.id 
                                  WHERE a.teacher_id = ? ORDER BY a.created_at DESC");
$stmt_published->execute([$teacher_id]);
$published_assignments = $stmt_published->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Assignment Management</h2>
        <p class="text-slate-500 font-medium">Distribute tasks and manage student submissions.</p>
    </div>

    <button onclick="document.getElementById('upload_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5 transform active:scale-95">
        <i class="fas fa-file-circle-plus"></i>
        <span>Publish Assignment</span>
    </button>
</div>

<?php if ($success_message): ?>
    <div
        class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center animate__animated animate__fadeInDown">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-12">
    <?php if (empty($published_assignments)): ?>
        <div class="lg:col-span-3 py-20 bg-white rounded-[3rem] text-center border border-indigo-50">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                <i class="fas fa-file-lines text-3xl"></i>
            </div>
            <h4 class="text-xl font-bold text-slate-800">No Assignments Yet</h4>
            <p class="text-slate-500 mt-2">Create and assign tasks to your students.</p>
        </div>
    <?php else: ?>
        <?php foreach ($published_assignments as $assign): ?>
            <div
                class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50 hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-8">
                        <span
                            class="text-[10px] font-black tracking-widest uppercase px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-500 rounded-lg">
                            <?php echo $assign['subject_name']; ?>
                        </span>
                        <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest"><i
                                class="fas fa-clock mr-1"></i> Due
                            <?php echo date('M d', strtotime($assign['deadline'])); ?>
                        </span>
                    </div>

                    <h3
                        class="text-2xl font-black text-slate-800 leading-tight mb-4 group-hover:text-indigo-600 transition-colors">
                        <?php echo $assign['title']; ?>
                    </h3>
                    <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed italic mb-8">
                        <?php echo strip_tags($assign['description']); ?>
                    </p>
                </div>

                <div class="pt-8 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-indigo-600 font-black text-sm">
                            <?php echo $assign['submission_count']; ?>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Submissions</div>
                    </div>
                    <a href="submissions-view.php?id=<?php echo $assign['id']; ?>"
                        class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-lg hover:shadow-indigo-50 transition-all">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="upload_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-12 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">Post Assignment</h3>
            <button onclick="document.getElementById('upload_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 bg-slate-50 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="assignments.php" method="POST" class="space-y-8">
            <input type="hidden" name="upload_assignment" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Assignment Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Database Normalization 1NF to 3NF"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Target Subject *</label>
                    <select name="subject_id" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800">
                        <option value="">Select Subject</option>
                        <?php foreach ($my_subjects as $sub): ?>
                            <option value="<?php echo $sub['id']; ?>">
                                <?php echo $sub['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Submission Deadline *</label>
                    <input type="datetime-local" name="deadline" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Instructions / Description</label>
                    <textarea name="description" rows="4" placeholder="Brief about the task and grading criteria..."
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button type="button" onclick="document.getElementById('upload_modal').classList.add('hidden')"
                    class="flex-1 py-5 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs">Cancel</button>
                <button type="submit"
                    class="flex-1 py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                    Publish to Class
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>