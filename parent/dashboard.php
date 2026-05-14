<?php
$page_title = "Academic Health Deck";
require_once 'includes/header.php';

$student_id = $_SESSION['linked_student_id'];

// Fetch Attendance Stats
$stmt = $pdo->prepare("SELECT 
                        COUNT(*) as total, 
                        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present 
                       FROM attendance WHERE student_id = ?");
$stmt->execute([$student_id]);
$attendance = $stmt->fetch();
$att_percent = ($attendance['total'] > 0) ? round(($attendance['present'] / $attendance['total']) * 100) : 0;

// Fetch Recent Results
$stmt = $pdo->prepare("SELECT m.*, s.name as subject_name, e.name as exam_name 
                        FROM marks m 
                        JOIN subjects s ON m.subject_id = s.id 
                        JOIN exams e ON m.exam_id = e.id 
                        WHERE m.student_id = ? 
                        ORDER BY e.exam_date DESC LIMIT 5");
$stmt->execute([$student_id]);
$recent_results = $stmt->fetchAll();

// Fetch Notices
$stmt = $pdo->query("SELECT * FROM notices WHERE role_target IN ('all', 'students') ORDER BY created_at DESC LIMIT 3");
$notices = $stmt->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Performance Intelligence</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Synchronized telemetry of <?php echo $_SESSION['linked_student_name']; ?>'s academic trajectory.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
    <!-- Attendance Metric -->
    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800 relative overflow-hidden group">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 italic">Presence Registry</p>
        <div class="flex items-end justify-between">
            <div>
                <h3 class="text-5xl font-black text-slate-800 dark:text-white italic"><?php echo $att_percent; ?>%</h3>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-2 italic">Institutional Compliance</p>
            </div>
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 shadow-soft border border-emerald-100 dark:border-emerald-500/20 italic font-black text-xl">A</div>
        </div>
    </div>

    <!-- Assignments Status (Placeholder for now, or fetch actual) -->
    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800 relative overflow-hidden group">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 italic">Project Velocity</p>
        <div class="flex items-end justify-between">
            <div>
                <h3 class="text-5xl font-black text-slate-800 dark:text-white italic">Active</h3>
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-2 italic">Learning Protocol</p>
            </div>
            <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 shadow-soft border border-indigo-100 dark:border-indigo-500/20 italic font-black text-xl">P</div>
        </div>
    </div>

    <!-- Exam Status -->
    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800 relative overflow-hidden group">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-amber-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6 italic">Valuation Grade</p>
        <div class="flex items-end justify-between">
            <div>
                <h3 class="text-5xl font-black text-slate-800 dark:text-white italic">Elite</h3>
                <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mt-2 italic">Assessment Quality</p>
            </div>
            <div class="w-16 h-16 bg-amber-50 dark:bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600 shadow-soft border border-amber-100 dark:border-amber-500/20 italic font-black text-xl">V</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    <!-- Performance Log -->
    <div class="bg-white dark:bg-slate-800 rounded-[4rem] shadow-premium border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
            <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase italic">Assessment Stream</h4>
            <a href="results.php" class="text-[10px] font-black text-emerald-600 uppercase tracking-widest italic">Full Matrix</a>
        </div>
        <div class="p-10 space-y-6">
            <?php if (empty($recent_results)): ?>
                <div class="text-center py-10 text-slate-400 italic font-bold">No evaluation records synchronized yet.</div>
            <?php else: ?>
                <?php foreach ($recent_results as $res): ?>
                    <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 group hover:bg-white dark:hover:bg-slate-800 transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 italic font-black text-sm shadow-soft border border-slate-50 dark:border-slate-700"><?php echo substr($res['subject_name'], 0, 1); ?></div>
                            <div>
                                <h6 class="text-xs font-black text-slate-800 dark:text-white uppercase italic"><?php echo $res['subject_name']; ?></h6>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5"><?php echo $res['exam_name']; ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-emerald-600 italic"><?php echo $res['marks_obtained']; ?> <span class="text-[10px] text-slate-300 dark:text-slate-600">/ <?php echo $res['max_marks']; ?></span></p>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Score Captured</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Institutional Broadcasts -->
    <div class="bg-white dark:bg-slate-800 rounded-[4rem] shadow-premium border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
            <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase italic">Broadcast Stream</h4>
        </div>
        <div class="p-10 space-y-8">
            <?php foreach ($notices as $notice): ?>
                <div class="relative pl-8 border-l-2 border-emerald-100 dark:border-emerald-500/20 py-2">
                    <div class="absolute -left-[5px] top-4 w-2 h-2 bg-emerald-500 rounded-full shadow-lg shadow-emerald-500/30"></div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic"><?php echo date('M d, Y', strtotime($notice['created_at'])); ?></p>
                    <h5 class="text-sm font-black text-slate-800 dark:text-white uppercase italic leading-tight mb-2"><?php echo $notice['title']; ?></h5>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium italic line-clamp-2"><?php echo strip_tags($notice['content']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="mt-10 p-12 bg-emerald-600 rounded-[4rem] text-white relative overflow-hidden group shadow-premium shadow-emerald-500/20">
    <div class="absolute right-0 top-0 w-96 h-96 bg-white/10 rounded-full -translate-y-48 translate-x-48 group-hover:scale-110 transition-transform duration-1000"></div>
    <div class="relative z-10">
        <h3 class="text-3xl font-black italic uppercase mb-6 tracking-tight">Parent-Institutional Synergy</h3>
        <p class="text-emerald-50 font-medium italic max-w-2xl leading-relaxed mb-10">You are viewing verified academic telemetry. For deep-dive discussions or pedagogical concerns, please schedule a virtual interface with the faculty coordinator.</p>
        <div class="flex flex-wrap gap-6">
            <a href="#" class="px-8 py-5 bg-white text-emerald-600 font-black rounded-3xl text-[10px] uppercase tracking-widest italic shadow-xl hover:bg-emerald-50 transition-all active:scale-95">Initiate Protocol</a>
            <a href="#" class="px-8 py-5 bg-emerald-500/30 backdrop-blur-md text-white border border-white/20 font-black rounded-3xl text-[10px] uppercase tracking-widest italic shadow-xl hover:bg-emerald-500/40 transition-all">Support Relay</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
