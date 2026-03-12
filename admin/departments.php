<?php
$page_title = "Departments Management";
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

// Handle Add Department
if (isset($_POST['add_dept'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);

    try {
        $stmt = $pdo->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        $success_message = "Department '$name' created successfully!";
    } catch (PDOException $e) {
        $error_message = "Failed to add department: " . $e->getMessage();
    }
}

$departments = $pdo->query("SELECT d.*, (SELECT COUNT(*) FROM students s WHERE s.course_id IN (SELECT id FROM courses WHERE dept_id = d.id)) as student_count, (SELECT COUNT(*) FROM teachers t WHERE t.dept_id = d.id) as teacher_count FROM departments d ORDER BY d.name")->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Academic Departments</h2>
        <p class="text-slate-500 font-medium">Define and manage the core academic structures.</p>
    </div>

    <button onclick="document.getElementById('add_dept_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
        <i class="fas fa-plus"></i>
        <span>Create Department</span>
    </button>
</div>

<?php if ($success_message): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($departments as $dept): ?>
        <div
            class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50 hover:shadow-xl hover:shadow-indigo-50 hover:-translate-y-2 transition-all group">
            <div class="flex items-center justify-between mb-8">
                <div
                    class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <i class="fas fa-building text-xl"></i>
                </div>
                <div class="flex space-x-2">
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-all">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-slate-800 mb-2 truncate">
                <?php echo $dept['name']; ?>
            </h3>
            <p class="text-sm text-slate-500 line-clamp-2 mb-8 leading-relaxed italic">
                <?php echo $dept['description'] ?: 'No description provided.'; ?>
            </p>

            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-slate-50">
                <div class="text-center p-4 bg-slate-50 rounded-2xl">
                    <div class="text-xl font-black text-slate-800">
                        <?php echo $dept['student_count']; ?>
                    </div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Students</div>
                </div>
                <div class="text-center p-4 bg-indigo-50/50 rounded-2xl">
                    <div class="text-xl font-black text-indigo-600">
                        <?php echo $dept['teacher_count']; ?>
                    </div>
                    <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-1">Teachers</div>
                </div>
            </div>

            <button
                class="w-full mt-8 py-4 bg-white border border-slate-100 text-slate-600 rounded-2xl font-bold text-xs tracking-widest uppercase hover:bg-slate-50 transition-all transform active:scale-95">
                View Department Details
            </button>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Dept Modal -->
<div id="add_dept_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-slate-800">New Department</h3>
            <button onclick="document.getElementById('add_dept_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="departments.php" method="POST" class="space-y-6">
            <input type="hidden" name="add_dept" value="1">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Department Name *</label>
                <input type="text" name="name" required placeholder="e.g. Computer Science"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Description</label>
                <textarea name="description" rows="4" placeholder="Brief about the department..."
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800"></textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('add_dept_modal').classList.add('hidden')"
                    class="flex-1 py-4 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all">Cancel</button>
                <button type="submit"
                    class="flex-1 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Create</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>