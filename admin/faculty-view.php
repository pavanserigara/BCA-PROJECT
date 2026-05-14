<?php
$page_title = "Academic Faculty Profile";
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch Faculty detailed info
$stmt = $pdo->prepare("SELECT u.*, t.*, d.name as department_name 
                       FROM users u 
                       JOIN teachers t ON u.id = t.user_id 
                       JOIN departments d ON t.dept_id = d.id
                       WHERE u.id = ?");
$stmt->execute([$id]);
$faculty = $stmt->fetch();

if (!$faculty) {
    echo "<div class='p-20 text-center'><h3 class='text-2xl font-black text-rose-500'>Profile Not Found</h3><a href='faculty-list.php' class='text-indigo-600 font-bold'>Back to Directory</a></div>";
    require_once 'includes/footer.php';
    exit();
}

// Assign / remove subjects
$success_message = '';
$error_message = '';

if (isset($_POST['assign_subject'])) {
    $subject_id = isset($_POST['subject_id']) ? (int) $_POST['subject_id'] : 0;
    if ($subject_id <= 0) {
        $error_message = "Please select a valid subject to assign.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO teacher_subjects (teacher_id, subject_id) VALUES (?, ?)");
            $stmt->execute([$id, $subject_id]);
            $success_message = "Subject assigned successfully.";
        } catch (PDOException $e) {
            $error_message = "Failed to assign subject: " . $e->getMessage();
        }
    }
}

if (isset($_POST['remove_subject'])) {
    $subject_id = isset($_POST['subject_id']) ? (int) $_POST['subject_id'] : 0;
    if ($subject_id <= 0) {
        $error_message = "Invalid subject selected.";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM teacher_subjects WHERE teacher_id = ? AND subject_id = ?");
            $stmt->execute([$id, $subject_id]);
            $success_message = "Subject removed successfully.";
        } catch (PDOException $e) {
            $error_message = "Failed to remove subject: " . $e->getMessage();
        }
    }
}

// Fetch Assigned Subjects
$stmt_sub = $pdo->prepare("
    SELECT s.id, s.name, s.code, s.semester, c.name AS course_name
    FROM teacher_subjects ts
    JOIN subjects s ON ts.subject_id = s.id
    JOIN courses c ON s.course_id = c.id
    WHERE ts.teacher_id = ?
    ORDER BY c.name, s.semester, s.name
");
$stmt_sub->execute([$id]);
$assigned_subjects = $stmt_sub->fetchAll();

// Fetch Available Subjects (same department as faculty)
$stmt_avail = $pdo->prepare("
    SELECT s.id, s.name, s.code, s.semester, c.name AS course_name
    FROM subjects s
    JOIN courses c ON s.course_id = c.id
    WHERE c.dept_id = ?
    ORDER BY c.name, s.semester, s.name
");
$stmt_avail->execute([(int) $faculty['dept_id']]);
$available_subjects = $stmt_avail->fetchAll();
?>

<div class="flex items-center justify-between mb-15">
    <div class="flex items-center space-x-6">
        <a href="faculty-list.php"
            class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">
                <?php echo $faculty['full_name']; ?>
            </h2>
            <div class="flex items-center space-x-3 mt-3">
                <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest italic">
                    <?php echo $faculty['employee_id']; ?>
                </span>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                    <?php echo $faculty['designation']; ?>
                </span>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">
                    <?php echo $faculty['department_name']; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <button
            class="bg-indigo-600 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100/50 hover:-translate-y-1 transition-all active:scale-95 flex items-center space-x-3 italic">
            <i class="fas fa-edit text-sm"></i>
            <span>Institutional Update</span>
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pb-20">
    <!-- Left Column: Professional Summary -->
    <div class="space-y-12">
        <div class="bg-slate-900 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden group">
            <div class="relative z-10 text-center">
                <div
                    class="w-32 h-32 bg-indigo-500 rounded-[3rem] mx-auto mb-8 flex items-center justify-center text-white font-black text-4xl italic shadow-2xl shadow-indigo-900/50 ring-4 ring-indigo-400">
                    <?php echo substr($faculty['full_name'], 0, 1); ?>
                </div>
                <h4 class="text-xl font-black text-white italic tracking-tight mb-2 uppercase">
                    <?php echo $faculty['full_name']; ?>
                </h4>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-10 italic">
                    <?php echo $faculty['designation']; ?>
                </p>

                <div class="flex items-center justify-center space-x-4">
                    <div class="bg-white/5 border border-white/10 px-6 py-3 rounded-2xl">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1 italic">Experience
                        </p>
                        <p class="text-sm font-black text-white italic"><?php echo $faculty['experience'] ?: '8.5 Years'; ?></p>
                    </div>
                    <div class="bg-white/5 border border-white/10 px-6 py-3 rounded-2xl">
                        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1 italic">
                            Status</p>
                        <p class="text-sm font-black text-white italic">Verified</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30">
            <h4
                class="text-xl font-black text-slate-800 tracking-tight italic mb-8 border-b border-indigo-50 pb-6 leading-none">
                Institutional Duty</h4>
            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Base
                        Payroll</span>
                    <span class="text-sm font-black text-slate-800 italic">₹1,20,000 / mo</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Teaching
                        Load</span>
                    <span class="text-sm font-black text-indigo-500 italic">18 hrs / week</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Institutional
                        Role</span>
                    <span class="text-sm font-black text-emerald-500 italic">Senior Faculty</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details & Subjects -->
    <div class="lg:col-span-2 space-y-12">
        <?php if ($success_message): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle text-[12px]"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error" role="alert">
                <i class="fas fa-triangle-exclamation text-[12px]"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                <span
                    class="w-10 h-10 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 text-xs italic">Q</span>
                <span>Faculty Qualifications</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-15">
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Highest
                        Academic Degree</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo $faculty['qualification'] ?: 'Ph.D. in Computer Science'; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Certification
                        Authority</label>
                    <p class="text-lg font-black text-indigo-600 tracking-tight italic">Institutional Verified Registry</p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Specialization
                        Index</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">Distributed Systems & AI
                        Architecture</p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Appointment
                        Date</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo date('M d, Y', strtotime($faculty['created_at'])); ?>
                    </p>
                </div>
            </div>

            <div class="p-10 bg-slate-50 rounded-[3rem] border border-slate-100 shadow-inner">
                <h5 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 italic">Professional
                    Biography Summary</h5>
                <p class="text-sm font-medium text-slate-500 leading-loose italic">Dedicated researcher and educator
                    with over 8 years of experience in higher education. Specialized in architecting scalable
                    distributed systems and mentoring senior batch students for placement excellence. Lead faculty for
                    the Institutional AI Research Cell.</p>
            </div>
        </div>

        <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                <span
                    class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xs italic">S</span>
                <span>Assigned Syllabus Flow</span>
            </h4>

            <div class="bg-slate-50/60 border border-slate-100 rounded-[2.5rem] p-8 mb-10">
                <form method="POST" class="flex flex-col md:flex-row gap-4 items-stretch md:items-end">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Assign
                            Subject</label>
                        <select name="subject_id" required class="w-full bg-white border border-slate-200 rounded-2xl px-4 py-3 text-[12px] font-semibold text-slate-700">
                            <option value="">Select a subject</option>
                            <?php foreach ($available_subjects as $s): ?>
                                <option value="<?php echo (int) $s['id']; ?>">
                                    <?php echo htmlspecialchars($s['course_name'] . " • Sem " . $s['semester'] . " • " . $s['code'] . " — " . $s['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="assign_subject" value="1"
                        class="btn btn-primary md:w-auto w-full">
                        <i class="fas fa-plus text-[11px] mr-2"></i>Assign
                    </button>
                </form>
            </div>

            <?php if (empty($assigned_subjects)): ?>
                <div class="p-10 bg-white border border-slate-100 rounded-[2.5rem] text-center text-slate-500">
                    <p class="font-semibold">No subjects assigned yet.</p>
                    <p class="text-[12px] text-slate-400 mt-1">Assign subjects above to populate the faculty portal.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($assigned_subjects as $s): ?>
                        <div
                            class="p-8 bg-slate-50/50 border border-slate-100 rounded-[2.5rem] flex items-center justify-between gap-6 group hover:bg-white hover:border-indigo-100 hover:shadow-xl transition-all duration-300">
                            <div class="min-w-0">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 italic truncate">
                                    <?php echo htmlspecialchars($s['course_name']); ?> — Semester <?php echo (int) $s['semester']; ?>
                                </p>
                                <h6 class="text-base font-black text-slate-800 italic uppercase truncate">
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </h6>
                                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest italic mt-2">
                                    <?php echo htmlspecialchars($s['code']); ?>
                                </p>
                            </div>
                            <form method="POST" onsubmit="return confirm('Remove this subject from faculty allocation?');">
                                <input type="hidden" name="subject_id" value="<?php echo (int) $s['id']; ?>">
                                <button type="submit" name="remove_subject" value="1"
                                    class="w-11 h-11 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 hover:text-rose-600 hover:border-rose-200 transition-all">
                                    <i class="fas fa-trash-alt text-[12px]"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>