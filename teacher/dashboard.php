<?php
$page_title = "Faculty Overview";
require_once 'includes/header.php';

// Fetch teacher specific stats
$teacher_id = $_SESSION['user_id'];
$subjects_count = $pdo->prepare("SELECT COUNT(*) FROM teacher_subjects WHERE teacher_id = ?");
$subjects_count->execute([$teacher_id]);
$total_subs = $subjects_count->fetchColumn();

// Fetch assigned subjects
$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();
?>

<!-- Top Hero Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
    <div
        class="bg-gradient-to-tr from-indigo-600 to-indigo-800 p-10 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-100 flex flex-col justify-between">
        <div>
            <h4 class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-2">My Academic Load</h4>
            <div class="text-5xl font-black">
                <?php echo $total_subs; ?>
            </div>
        </div>
        <div class="mt-10 flex items-center space-x-2 text-indigo-100/60 font-medium">
            <i class="fas fa-book-open"></i>
            <span>Active Subjects This Semester</span>
        </div>
    </div>

    <div
        class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-50 flex flex-col justify-between card-hover">
        <div class="flex items-center justify-between mb-8">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                <i class="fas fa-clipboard-check text-xl"></i>
            </div>
            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Today</span>
        </div>
        <div>
            <h4 class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Avg. Attendance</h4>
            <div class="text-4xl font-black text-slate-800 tracking-tight">92.4%</div>
        </div>
    </div>

    <div
        class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-50 flex flex-col justify-between card-hover">
        <div class="flex items-center justify-between mb-8">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Pending</span>
        </div>
        <div>
            <h4 class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Unchecked Submissions</h4>
            <div class="text-4xl font-black text-slate-800 tracking-tight">18</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Active Courses/Subjects -->
    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-50">
        <div class="flex items-center justify-between mb-10">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight">My Subjects</h4>
            <a href="subjects.php" class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Manage All</a>
        </div>

        <div class="space-y-6">
            <?php foreach ($my_subjects as $sub): ?>
                <div
                    class="flex items-center justify-between p-6 bg-slate-50/50 rounded-3xl border border-slate-50 group hover:bg-indigo-50 hover:border-indigo-100 transition-all">
                    <div class="flex items-center space-x-5">
                        <div
                            class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm font-black text-xs">
                            <?php echo $sub['code']; ?>
                        </div>
                        <div>
                            <h6
                                class="text-lg font-bold text-slate-800 tracking-tight leading-none group-hover:text-indigo-600 transition-colors mb-1">
                                <?php echo $sub['name']; ?>
                            </h6>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <?php echo $sub['course_name']; ?> • Sem
                                <?php echo $sub['semester']; ?>
                            </p>
                        </div>
                    </div>
                    <button
                        class="w-10 h-10 bg-white rounded-xl shadow-sm text-slate-300 hover:text-indigo-600 transition-all flex items-center justify-center">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl flex flex-col justify-between">
        <div>
            <h4 class="text-2xl font-black text-white tracking-tight mb-2">Faculty Hub</h4>
            <p class="text-slate-400 font-medium mb-12">Quickly manage your academic operations.</p>

            <div class="grid grid-cols-2 gap-6">
                <!-- Mark Attendance -->
                <a href="attendance-take.php"
                    class="p-8 bg-slate-800 rounded-3xl border border-slate-700 hover:border-indigo-500 hover:bg-slate-800 group transition-all transform hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <span class="block text-white font-black text-xs uppercase tracking-widest mb-1">Take
                        Attendance</span>
                    <p class="text-slate-500 text-[10px] leading-relaxed">Daily rolls for assigned subjects.</p>
                </a>

                <!-- Upload Assignment -->
                <a href="assignments.php"
                    class="p-8 bg-slate-800 rounded-3xl border border-slate-700 hover:border-emerald-500 hover:bg-slate-800 group transition-all transform hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <i class="fas fa-file-circle-plus text-xl"></i>
                    </div>
                    <span class="block text-white font-black text-xs uppercase tracking-widest mb-1">New Task</span>
                    <p class="text-slate-500 text-[10px] leading-relaxed">Distribute assignments to classes.</p>
                </a>

                <!-- Enter Marks -->
                <a href="marks-entry.php"
                    class="p-8 bg-slate-800 rounded-3xl border border-slate-700 hover:border-amber-500 hover:bg-slate-800 group transition-all transform hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 mb-6 group-hover:bg-amber-600 group-hover:text-white transition-all">
                        <i class="fas fa-marker text-xl"></i>
                    </div>
                    <span class="block text-white font-black text-xs uppercase tracking-widest mb-1">Grading</span>
                    <p class="text-slate-500 text-[10px] leading-relaxed">Input internal and final results.</p>
                </a>

                <!-- Post Notice -->
                <a href="notices.php"
                    class="p-8 bg-slate-800 rounded-3xl border border-slate-700 hover:border-rose-500 hover:bg-slate-800 group transition-all transform hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 mb-6 group-hover:bg-rose-600 group-hover:text-white transition-all">
                        <i class="fas fa-bullhorn text-xl"></i>
                    </div>
                    <span class="block text-white font-black text-xs uppercase tracking-widest mb-1">Broadcast</span>
                    <p class="text-slate-500 text-[10px] leading-relaxed">Announcements for your students.</p>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>