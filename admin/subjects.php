<?php
$page_title = "Manage Subjects";
require_once 'includes/header.php';

$course_id = isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0;
if (!$course_id) {
    header("Location: courses.php");
    exit();
}

// Fetch Course Details
$stmt_course = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt_course->execute([$course_id]);
$course = $stmt_course->fetch();

if (!$course) {
    header("Location: courses.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle Add Subject
if (isset($_POST['add_subject'])) {
    $name = sanitize($_POST['name']);
    $code = sanitize($_POST['code']);
    $semester = (int) $_POST['semester'];

    try {
        $stmt = $pdo->prepare("INSERT INTO subjects (course_id, name, code, semester) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_id, $name, $code, $semester]);
        $success_message = "Subject '$name' added successfully!";
    } catch (PDOException $e) {
        $error_message = "Failed to add subject: " . $e->getMessage();
    }
}

// Fetch Subjects for this course
$stmt_subjects = $pdo->prepare("SELECT * FROM subjects WHERE course_id = ? ORDER BY semester, name");
$stmt_subjects->execute([$course_id]);
$subjects = $stmt_subjects->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">
            <?php echo $course['name']; ?>
        </h2>
        <p class="text-slate-500 font-medium">Curriculum structure for all semesters.</p>
    </div>

    <div class="flex items-center space-x-4">
        <a href="courses.php"
            class="text-slate-500 hover:text-indigo-600 font-bold flex items-center space-x-2 transition-all">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Courses</span>
        </a>
        <button onclick="document.getElementById('add_subject_modal').classList.remove('hidden')"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Add Subject</span>
        </button>
    </div>
</div>

<?php if ($success_message): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-indigo-100/50 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr
                class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50 bg-slate-50/50">
                <th class="py-6 px-10">Semester</th>
                <th class="py-6 px-10">Subject Code</th>
                <th class="py-6 px-10">Subject Name</th>
                <th class="py-6 px-10 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-indigo-50/30">
            <?php if (empty($subjects)): ?>
                <tr>
                    <td colspan="4" class="py-20 text-center">
                        <p class="text-slate-400 italic">No subjects added to this course yet.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php
                $current_sem = 0;
                foreach ($subjects as $sub):
                    if ($current_sem != $sub['semester']):
                        $current_sem = $sub['semester'];
                        ?>
                        <tr class="bg-indigo-50/30">
                            <td colspan="4" class="py-4 px-10">
                                <span class="text-xs font-black text-indigo-600 uppercase tracking-widest">Semester
                                    <?php echo $current_sem; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr class="group hover:bg-slate-50 transition-all">
                        <td class="py-6 px-10">
                            <span class="text-sm font-bold text-slate-500">Sem
                                <?php echo $sub['semester']; ?>
                            </span>
                        </td>
                        <td class="py-6 px-10">
                            <span
                                class="text-sm font-black text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                <?php echo $sub['code']; ?>
                            </span>
                        </td>
                        <td class="py-6 px-10">
                            <span class="text-base font-bold text-slate-800">
                                <?php echo $sub['name']; ?>
                            </span>
                        </td>
                        <td class="py-6 px-10 text-right">
                            <button class="text-slate-400 hover:text-rose-600 transition-all"><i
                                    class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Subject Modal -->
<div id="add_subject_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-slate-800">New Subject</h3>
            <button onclick="document.getElementById('add_subject_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="subjects.php?course_id=<?php echo $course_id; ?>" method="POST" class="space-y-6">
            <input type="hidden" name="add_subject" value="1">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Subject Name *</label>
                <input type="text" name="name" required placeholder="e.g. Data Structures & Algorithms"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Subject Code *</label>
                <input type="text" name="code" required placeholder="e.g. CS201"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Assign Semester *</label>
                <select name="semester" required
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800">
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                    <option value="3">Semester 3</option>
                    <option value="4">Semester 4</option>
                    <option value="5">Semester 5</option>
                    <option value="6">Semester 6</option>
                </select>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('add_subject_modal').classList.add('hidden')"
                    class="flex-1 py-4 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all">Cancel</button>
                <button type="submit"
                    class="flex-1 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Add
                    Subject</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>