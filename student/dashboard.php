<?php
$page_title = "My Academic Journey";
require_once 'includes/header.php';

// Fetch Student Info
$student_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT s.*, c.name as course_name FROM students s JOIN courses c ON s.course_id = c.id WHERE s.user_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Attendance Stats (MOCK)
$attendance_rate = 88.5;

// My Subjects
$stmt_subs = $pdo->prepare("SELECT * FROM subjects WHERE course_id = ? AND semester = ?");
$stmt_subs->execute([$student['course_id'], $student['semester']]);
$subjects = $stmt_subs->fetchAll();
?>

<!-- Hero Section -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
    <div
        class="bg-indigo-600 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-100 flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer overflow-hidden relative">
        <div
            class="absolute -right-4 -bottom-4 bg-indigo-500 w-24 h-24 rounded-full group-hover:scale-150 transition-transform flex items-center justify-center opacity-40">
            <i class="fas fa-user-graduate text-3xl"></i>
        </div>
        <div>
            <h4 class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-1">My Status</h4>
            <div class="text-xl font-bold leading-tight">Enrolled in
                <?php echo $student['course_name']; ?>
            </div>
        </div>
        <div class="flex items-center space-x-2 text-indigo-100/60 font-medium text-xs">
            <i class="fas fa-id-badge"></i>
            <span>Roll:
                <?php echo $student['roll_no']; ?>
            </span>
        </div>
    </div>

    <!-- Attendance Card -->
    <div
        class="bg-white p-8 rounded-[2rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-none">Overall Attendance
            </h4>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                <i class="fas fa-check"></i>
            </div>
        </div>
        <div>
            <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mb-1">
                <?php echo $attendance_rate; ?>%
            </div>
            <div class="w-full h-2 bg-slate-50 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-indigo-500 rounded-full group-hover:animate-pulse"
                    style="width: <?php echo $attendance_rate; ?>%"></div>
            </div>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Well Above 75% Target</p>
    </div>

    <!-- Fee Card -->
    <div
        class="bg-white p-8 rounded-[2rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-none">Semester Fees</h4>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
        <div>
            <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mb-1">Cleared</div>
            <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed">Wait, next installment in
                July.</p>
        </div>
        <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">No Dues Pending</p>
    </div>

    <!-- Results Card -->
    <div
        class="bg-white p-8 rounded-[2rem] border border-indigo-50 shadow-sm flex flex-col justify-between h-56 group active:scale-95 transition-all cursor-pointer">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-none">GPA Performance
            </h4>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div>
            <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mb-1">8.42/10</div>
            <p class="text-xs font-bold text-slate-400 mt-4 tracking-tight leading-relaxed">Top 15% in your batch.</p>
        </div>
        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Current Semester Profile</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
    <!-- Active Subjects Table -->
    <div class="lg:col-span-2 bg-white p-10 rounded-[2.5rem] border border-slate-50 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight">Active Subjects</h4>
            <span
                class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 uppercase tracking-widest">Semester
                <?php echo $student['semester']; ?>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="pb-6 px-4">Subject</th>
                        <th class="pb-6 px-4">Code</th>
                        <th class="pb-6 px-4">Attendance</th>
                        <th class="pb-6 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($subjects as $sub): ?>
                        <tr class="group hover:bg-slate-50 transition-all">
                            <td class="py-6 px-4">
                                <span class="text-sm font-bold text-slate-700">
                                    <?php echo $sub['name']; ?>
                                </span>
                            </td>
                            <td class="py-6 px-4">
                                <span
                                    class="text-xs font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-100">
                                    <?php echo $sub['code']; ?>
                                </span>
                            </td>
                            <td class="py-6 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-400" style="width: 85%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-500">85%</span>
                                </div>
                            </td>
                            <td class="py-6 px-4 text-right">
                                <button
                                    class="w-10 h-10 rounded-xl bg-white border border-slate-100 text-slate-300 hover:text-indigo-600 transition-all">
                                    <i class="fas fa-download text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Assignments -->
    <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl flex flex-col">
        <div class="flex items-center justify-between mb-8">
            <h4 class="text-2xl font-black text-white tracking-tight">Assignments</h4>
            <a href="assignments.php" class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">View
                Portal</a>
        </div>

        <div class="space-y-6 flex-1 overflow-y-auto pr-2 scrollbar-hide">
            <!-- MOCK -->
            <div
                class="p-6 bg-slate-800 rounded-[2rem] border border-slate-700/50 hover:border-indigo-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <span
                        class="px-3 py-1 bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-indigo-500/20">Programming</span>
                    <span class="text-rose-400 text-[10px] font-black uppercase tracking-widest">Due Today</span>
                </div>
                <h6 class="text-white font-bold leading-tight group-hover:text-indigo-400 transition-colors mb-4">
                    Complete Tic-Tac-Toe Game with Java Swing</h6>
                <div class="flex items-center justify-between pt-4 border-t border-slate-700/50">
                    <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-500">
                        <i class="fas fa-paper-plane"></i>
                        <span>Not Submitted</span>
                    </div>
                    <button
                        class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center hover:bg-indigo-600 transition-all uppercase font-black text-[10px]">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </div>
            </div>

            <div
                class="p-6 bg-slate-800 rounded-[2rem] border border-slate-700/50 hover:border-emerald-500/50 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <span
                        class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-emerald-500/20">DBMS</span>
                    <span class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Oct 12, 2025</span>
                </div>
                <h6 class="text-white font-bold leading-tight group-hover:text-emerald-400 transition-colors mb-4">
                    Relational Algebra Exercise 1-5</h6>
                <div class="flex items-center justify-between pt-4 border-t border-slate-700/50">
                    <div class="flex items-center space-x-2 text-[10px] font-bold text-emerald-500">
                        <i class="fas fa-check-double"></i>
                        <span>Submitted</span>
                    </div>
                </div>
            </div>
        </div>

        <button
            class="mt-10 w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-900/40 hover:bg-indigo-700 transition-all">
            Open Submissions Dashboard
        </button>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>