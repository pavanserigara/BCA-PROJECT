<?php
$page_title = "Courses & Subjects";
require_once 'includes/header.php';

// Fetch data
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
$courses = $pdo->query("SELECT c.*, d.name as dept_name, (SELECT COUNT(*) FROM subjects WHERE course_id = c.id) as subject_count FROM courses c JOIN departments d ON c.dept_id = d.id ORDER BY c.name")->fetchAll();

if (isset($_POST['add_course'])) {
    $name = sanitize($_POST['name']);
    $dept_id = $_POST['dept_id'];
    $duration = sanitize($_POST['duration']);
    $description = sanitize($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO courses (name, dept_id, duration, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $dept_id, $duration, $description]);
    header("Location: courses.php?success=1");
    exit();
}
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Academic Curriculum</h2>
        <p class="text-slate-500 font-medium">Manage degree programs and their syllabus structures.</p>
    </div>

    <button onclick="document.getElementById('add_course_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
        <i class="fas fa-plus"></i>
        <span>Add New Course</span>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($courses as $course): ?>
        <div
            class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50 hover:shadow-xl hover:shadow-indigo-50 hover:-translate-y-2 transition-all group">
            <div class="flex items-center justify-between mb-8">
                <div
                    class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-all">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-black tracking-widest uppercase px-3 py-1.5 bg-slate-50 border border-slate-100 text-slate-400 rounded-lg">
                    <?php echo $course['duration']; ?>
                </span>
            </div>

            <h3 class="text-2xl font-bold text-slate-800 mb-2 truncate group-hover:text-amber-600 transition-colors">
                <?php echo $course['name']; ?>
            </h3>
            <p class="text-xs font-bold text-indigo-500 mb-8 uppercase tracking-widest">
                <?php echo $course['dept_name']; ?>
            </p>

            <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div>
                        <div class="text-lg font-black text-slate-800">
                            <?php echo $course['subject_count']; ?>
                        </div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Subjects</div>
                    </div>
                </div>

                <a href="subjects.php?course_id=<?php echo $course['id']; ?>"
                    class="w-12 h-12 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-lg hover:shadow-indigo-50 transition-all">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Course Modal -->
<div id="add_course_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-slate-800">New Degree Program</h3>
            <button onclick="document.getElementById('add_course_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="courses.php" method="POST" class="space-y-6">
            <input type="hidden" name="add_course" value="1">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Course Name *</label>
                <input type="text" name="name" required placeholder="e.g. Bachelor of Computer Applications"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Parent Department *</label>
                <select name="dept_id" required
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>">
                            <?php echo $dept['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Duration *</label>
                <input type="text" name="duration" required placeholder="e.g. 3 Years / 6 Semesters"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('add_course_modal').classList.add('hidden')"
                    class="flex-1 py-4 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all">Cancel</button>
                <button type="submit"
                    class="flex-1 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Add
                    Course</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>