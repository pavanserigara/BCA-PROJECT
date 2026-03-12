<?php
$page_title = "Institutional Timetable";
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

// Handle Slot Creation
if (isset($_POST['create_slot'])) {
    $course_id = (int) $_POST['course_id'];
    $semester = (int) $_POST['semester'];
    $day = $_POST['day'];
    $subject_id = (int) $_POST['subject_id'];
    $teacher_id = (int) $_POST['teacher_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_no = sanitize($_POST['room_no']);

    try {
        $stmt = $pdo->prepare("INSERT INTO timetable (course_id, semester, day, subject_id, teacher_id, start_time, end_time, room_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $semester, $day, $subject_id, $teacher_id, $start_time, $end_time, $room_no]);
        $success_message = "New lecture slot published to the $day schedule!";
    } catch (PDOException $e) {
        $error_message = "Failed to schedule slot: " . $e->getMessage();
    }
}

// Fetch Courses, Subjects, Teachers for dropdowns
$courses = $pdo->query("SELECT * FROM courses ORDER BY name ASC")->fetchAll();
$subjects = $pdo->query("SELECT s.*, c.name as course_name FROM subjects s JOIN courses c ON s.course_id = c.id ORDER BY c.name, s.name")->fetchAll();
$teachers = $pdo->query("SELECT * FROM users WHERE role = 'teacher' ORDER BY full_name")->fetchAll();

// Fetch Timetable with filters
$sel_course = isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0;
$sel_sem = isset($_GET['semester']) ? (int) $_GET['semester'] : 0;

$timetable_data = [];
if ($sel_course && $sel_sem) {
    $stmt_tt = $pdo->prepare("SELECT tt.*, s.name as subject_name, u.full_name as teacher_name 
                              FROM timetable tt 
                              JOIN subjects s ON tt.subject_id = s.id 
                              JOIN users u ON tt.teacher_id = u.id 
                              WHERE tt.course_id = ? AND tt.semester = ? 
                              ORDER BY FIELD(tt.day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), tt.start_time");
    $stmt_tt->execute([$sel_course, $sel_sem]);
    $results = $stmt_tt->fetchAll();

    foreach ($results as $row) {
        $timetable_data[$row['day']][] = $row;
    }
}
?>

<div class="flex items-center justify-between mb-15">
    <div>
        <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Academic Scheduling Flow</h2>
        <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Configure and manage lecture timings for all
            institutional programs.</p>
    </div>

    <button onclick="document.getElementById('slot_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4.5 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all hover:-translate-y-1 transform active:scale-95 flex items-center space-x-3">
        <i class="fas fa-calendar-plus text-sm"></i>
        <span>Register New Slot</span>
    </button>
</div>

<!-- Filters -->
<div class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm mb-12">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
        <div>
            <label
                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Institutional
                Course Profile</label>
            <select name="course_id" required
                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                <option value="">Select Course</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $sel_course == $c['id'] ? 'selected' : ''; ?>>
                        <?php echo $c['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Academic
                Semester</label>
            <select name="semester" required
                class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                <option value="">Select Semester</option>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo $sel_sem == $i ? 'selected' : ''; ?>>Semester
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div>
            <button type="submit"
                class="w-full py-4.5 bg-slate-900 text-white font-black rounded-2xl hover:bg-indigo-600 transition-all uppercase tracking-widest text-[10px] italic">Fetch
                Schedule Profile</button>
        </div>
    </form>
</div>

<?php if ($success_message): ?>
    <div
        class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-8 rounded-[2.5rem] mb-12 flex items-center animate__animated animate__fadeInDown italic">
        <i class="fas fa-check-circle text-2xl mr-6"></i>
        <p class="text-base font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<!-- Timetable Grid -->
<?php if ($sel_course && $sel_sem): ?>
    <div class="space-y-12 pb-20">
        <?php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $day):
            ?>
            <div
                class="bg-white rounded-[3.5rem] shadow-sm border border-indigo-100/30 overflow-hidden group hover:shadow-2xl transition-all duration-300">
                <div class="p-10 border-b border-indigo-50 bg-slate-50/50 flex items-center justify-between">
                    <h4 class="text-2xl font-black text-slate-800 tracking-tight leading-none italic uppercase">
                        <?php echo $day; ?>
                    </h4>
                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">
                        <?php echo count($timetable_data[$day] ?? []); ?> Slots Active
                    </span>
                </div>
                <div class="p-10">
                    <?php if (empty($timetable_data[$day])): ?>
                        <p class="text-slate-400 italic text-sm py-10 text-center">No academic sessions scheduled for this day
                            Profile.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <?php foreach ($timetable_data[$day] as $slot): ?>
                                <div
                                    class="p-8 bg-slate-50/50 rounded-[2.5rem] border border-slate-50 relative overflow-hidden group/item hover:bg-white hover:border-indigo-100 transition-all">
                                    <div class="absolute top-0 right-0 p-6 flex flex-col items-end">
                                        <p
                                            class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic opacity-40 group-hover/item:opacity-100 transition-opacity">
                                            Room
                                            <?php echo $slot['room_no']; ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-4 mb-4">
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic border border-slate-100 px-3 py-1.5 rounded-lg">
                                            <?php echo date('h:i A', strtotime($slot['start_time'])); ?> -
                                            <?php echo date('h:i A', strtotime($slot['end_time'])); ?>
                                        </span>
                                    </div>
                                    <h5
                                        class="text-lg font-black text-slate-800 tracking-tight leading-tight group-hover/item:text-indigo-600 transition-colors uppercase italic mb-2">
                                        <?php echo $slot['subject_name']; ?>
                                    </h5>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-6">•
                                        <?php echo $slot['teacher_name']; ?>
                                    </p>

                                    <div class="flex items-center space-x-3 mt-4">
                                        <button
                                            class="w-10 h-10 bg-white border border-slate-100 rounded-xl text-slate-300 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm"><i
                                                class="fas fa-edit text-[10px]"></i></button>
                                        <button
                                            class="w-10 h-10 bg-white border border-slate-100 rounded-xl text-slate-300 hover:text-rose-600 hover:border-rose-600 transition-all shadow-sm"><i
                                                class="fas fa-trash-alt text-[10px]"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Slot Modal -->
<div id="slot_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl p-15 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-12">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic">Academic Slot
                Configuration</h3>
            <button onclick="document.getElementById('slot_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 bg-slate-50 w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="timetable.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <input type="hidden" name="create_slot" value="1">

            <div>
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Institutional
                    Course *</label>
                <select name="course_id" required
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>">
                            <?php echo $c['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Active
                    Semester *</label>
                <select name="semester" required
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?php echo $i; ?>">Semester
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Target
                    Day Profile *</label>
                <select name="day" required
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    <option value="Monday">Monday Cycle</option>
                    <option value="Tuesday">Tuesday Cycle</option>
                    <option value="Wednesday">Wednesday Cycle</option>
                    <option value="Thursday">Thursday Cycle</option>
                    <option value="Friday">Friday Cycle</option>
                    <option value="Saturday">Saturday Cycle</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Subject
                    Selection *</label>
                <select name="subject_id" required
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?php echo $s['id']; ?>">[
                            <?php echo $s['course_name']; ?>]
                            <?php echo $s['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="col-span-2">
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Faculty
                        Member *</label>
                    <select name="teacher_id" required
                        class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo $t['id']; ?>">
                                <?php echo $t['full_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Start
                        Time *</label>
                    <input type="time" name="start_time" required
                        class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">End
                        Time *</label>
                    <input type="time" name="end_time" required
                        class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                </div>
            </div>

            <div class="md:col-span-2">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Assigned
                    Room / Lab Location *</label>
                <input type="text" name="room_no" required placeholder="e.g. Lab 4B or Room 302"
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
            </div>

            <div class="md:col-span-2 pt-4 flex items-center gap-8">
                <button type="button" onclick="document.getElementById('slot_modal').classList.add('hidden')"
                    class="flex-1 py-6 bg-slate-50 text-slate-500 font-black rounded-3xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs italic">Stay
                    on Dashboard</button>
                <button type="submit"
                    class="flex-1 py-6 bg-indigo-600 text-white font-black rounded-3xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs italic">
                    Authorize & Publish Slot
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>