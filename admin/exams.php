<?php
$page_title = "Academic Sessions & Exams";
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

// Fetch Stats
$total_exams = $pdo->query("SELECT COUNT(*) FROM exams")->fetchColumn();
$active_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();

// Handle Exam Schedule Creation
if (isset($_POST['create_exam'])) {
    $name = sanitize($_POST['name']);
    $course_id = (int) $_POST['course_id'];
    $semester = (int) $_POST['semester'];
    $exam_date = $_POST['exam_date'];
    $type = $_POST['type'];

    try {
        $stmt = $pdo->prepare("INSERT INTO exams (name, course_id, semester, exam_date, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $course_id, $semester, $exam_date, $type]);
        $success_message = "Examination schedule for '$name' published!";
    } catch (PDOException $e) {
        $error_message = "Failed to schedule exam: " . $e->getMessage();
    }
}

// Fetch Courses for dropdown
$courses = $pdo->query("SELECT * FROM courses ORDER BY name")->fetchAll();

// Fetch Exams
$exams = $pdo->query("SELECT e.*, c.name as course_name 
                      FROM exams e 
                      JOIN courses c ON e.course_id = c.id 
                      ORDER BY e.exam_date DESC LIMIT 50")->fetchAll();
?>

<div class="flex items-center justify-between mb-15">
    <div>
        <h2 class="text-4xl font-black text-slate-800 tracking-tight">Examination Management</h2>
        <p class="text-slate-500 font-medium tracking-tight">Schedule academic evaluations and manage institution-wide
            result publications.</p>
    </div>

    <button onclick="document.getElementById('exam_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4.5 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all hover:-translate-y-1 transform active:scale-95 flex items-center space-x-3">
        <i class="fas fa-scroll text-sm"></i>
        <span>Schedule New Exam</span>
    </button>
</div>

<?php if ($success_message): ?>
    <div
        class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-8 rounded-[2.5rem] mb-12 flex items-center animate__animated animate__fadeInDown">
        <i class="fas fa-check-circle text-2xl mr-6"></i>
        <p class="text-base font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-15">
    <!-- Stat Card -->
    <div
        class="bg-indigo-600 p-10 rounded-[3rem] text-white shadow-2xl shadow-indigo-100 flex flex-col justify-between group h-56 relative overflow-hidden">
        <div class="relative z-10">
            <h4 class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-1 leading-none italic">
                Institutional Performance</h4>
            <div class="text-5xl font-black tracking-tight mt-4">
                <?php echo $total_exams; ?>
            </div>
            <p class="text-[10px] font-black text-indigo-100 uppercase tracking-widest mt-6 italic">Exams Conducted</p>
        </div>
        <div
            class="absolute -right-8 -bottom-8 w-40 h-40 bg-indigo-500 rounded-full opacity-30 group-hover:scale-150 transition-transform">
        </div>
    </div>

    <!-- Mini Stat -->
    <div class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56">
        <h4 class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1 italic">Average Score</h4>
        <div class="text-4xl font-black text-slate-800 tracking-tight">72.4<span class="text-slate-300">%</span></div>
        <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed italic">System Aggregate
            Portfolio.</p>
    </div>

    <div class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56">
        <h4 class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1 italic">Passing Ratio</h4>
        <div class="text-4xl font-black text-emerald-500 tracking-tight">88.2<span class="text-emerald-200">%</span>
        </div>
        <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed italic">Qualified Students vs
            Total.</p>
    </div>

    <div class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56">
        <h4 class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1 italic">Backlog Status</h4>
        <div class="text-4xl font-black text-rose-500 tracking-tight">11.8<span class="text-rose-200">%</span></div>
        <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed italic">Pending Remedial Exams.
        </p>
    </div>
</div>

<div
    class="bg-white rounded-[4rem] shadow-sm border border-indigo-100/50 overflow-hidden mb-20 animate__animated animate__fadeInUp">
    <div
        class="p-10 border-b border-indigo-50 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <h4 class="text-2xl font-black text-slate-800 tracking-tight">Examination Roster</h4>
        <div class="flex items-center space-x-4">
            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Ongoing Academic Year
                2024-25</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                    <th class="py-8 px-12">Examination Detail</th>
                    <th class="py-8 px-12">Target Course</th>
                    <th class="py-8 px-12 text-center">Semester</th>
                    <th class="py-8 px-12 text-center">Phase Type</th>
                    <th class="py-8 px-12 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-50/20">
                <?php if (empty($exams)): ?>
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <i class="fas fa-scroll-old text-slate-100 text-6xl mb-6 block"></i>
                            <p class="text-slate-400 italic text-xl">No examination schedules found in the system.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($exams as $ex): ?>
                        <tr class="group hover:bg-slate-50/80 transition-all">
                            <td class="py-8 px-12">
                                <h6
                                    class="text-lg font-black text-slate-800 tracking-tight group-hover:text-indigo-600 transition-colors uppercase leading-none italic">
                                    <?php echo $ex['name']; ?>
                                </h6>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">
                                    <?php echo date('D, M d, Y', strtotime($ex['exam_date'])); ?>
                                </p>
                            </td>
                            <td class="py-8 px-12">
                                <span class="text-sm font-bold text-slate-600">
                                    <?php echo $ex['course_name']; ?>
                                </span>
                            </td>
                            <td class="py-8 px-12 text-center">
                                <span
                                    class="text-xs font-black text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">Semester
                                    <?php echo $ex['semester']; ?>
                                </span>
                            </td>
                            <td class="py-8 px-12 text-center">
                                <span
                                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg">
                                    <?php echo $ex['type']; ?>
                                </span>
                            </td>
                            <td class="py-8 px-12 text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <button
                                        class="w-10 h-10 border border-slate-100 bg-white rounded-xl text-slate-300 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
                                        <i class="fas fa-chart-line text-[10px]"></i>
                                    </button>
                                    <button
                                        class="w-10 h-10 border border-slate-100 bg-white rounded-xl text-slate-300 hover:text-rose-600 hover:border-rose-600 transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="exam_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-12 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-12">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">Institutional Examination Schedule</h3>
            <button onclick="document.getElementById('exam_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 bg-slate-50 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="exams.php" method="POST" class="space-y-8">
            <input type="hidden" name="create_exam" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label
                        class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Evaluation
                        / Exam Name *</label>
                    <input type="text" name="name" required placeholder="e.g. End Semester Theory 2025"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Target
                        Course *</label>
                    <select name="course_id" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo $c['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Semester
                        *</label>
                    <select name="semester" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                        <option value="1">Sem 1</option>
                        <option value="2">Sem 2</option>
                        <option value="3">Sem 3</option>
                        <option value="4">Sem 4</option>
                        <option value="5">Sem 5</option>
                        <option value="6">Sem 6</option>
                    </select>
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Examination
                        Date *</label>
                    <input type="date" name="exam_date" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-widest text-[10px]">Phase
                        Type *</label>
                    <select name="type" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                        <option value="Internal">Internal Assessment</option>
                        <option value="Practical">Practical Evaluation</option>
                        <option value="Semester">Main Semester Exam</option>
                        <option value="Remedial">Remedial / Backlog</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button type="button" onclick="document.getElementById('exam_modal').classList.add('hidden')"
                    class="flex-1 py-5 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs">Stay
                    on Schedule</button>
                <button type="submit"
                    class="flex-1 py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                    Authorize & Post Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>