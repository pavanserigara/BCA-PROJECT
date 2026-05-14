<?php
$page_title = "My Subjects";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];

// Fetch Assigned Subjects for the teacher
$stmt_subs = $pdo->prepare("SELECT s.*, c.name as course_name 
                            FROM subjects s 
                            JOIN courses c ON s.course_id = c.id 
                            JOIN teacher_subjects ts ON s.id = ts.subject_id 
                            WHERE ts.teacher_id = ? 
                            ORDER BY c.name, s.semester, s.name");
$stmt_subs->execute([$teacher_id]);
$assigned_subjects = $stmt_subs->fetchAll();
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Academic Portfolio</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage your assigned curriculum and syllabus flow.</p>
    </div>
    <div class="bg-primary-50 dark:bg-primary-500/10 px-6 py-3 rounded-2xl border border-primary-100 dark:border-primary-500/20">
        <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest leading-none mb-1">Total Subjects</p>
        <p class="text-lg font-black text-slate-800 dark:text-white"><?php echo count($assigned_subjects); ?> Courses</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-10">
    <?php if (empty($assigned_subjects)): ?>
        <div class="md:col-span-3 py-20 bg-white dark:bg-slate-800 rounded-[2.5rem] text-center border border-dashed border-slate-200 dark:border-slate-700">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fas fa-book-open text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">No Subjects Assigned</h3>
            <p class="text-sm text-slate-500 mt-1">Please coordinate with the academic office for allocation.</p>
        </div>
    <?php else: ?>
        <?php foreach ($assigned_subjects as $s): ?>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700 flex flex-col justify-between group hover:border-primary-500 transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-[9px] font-bold text-primary-600 bg-primary-50 dark:bg-primary-500/10 px-3 py-1.5 rounded-lg border border-primary-100 dark:border-primary-500/20 uppercase tracking-widest">
                            Semester <?php echo $s['semester']; ?>
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            Code: <?php echo $s['code']; ?>
                        </span>
                    </div>

                    <h3 class="text-xl font-extrabold text-slate-800 dark:text-white leading-tight mb-2 group-hover:text-primary-600 transition-colors">
                        <?php echo $s['name']; ?>
                    </h3>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-8">
                        <?php echo $s['course_name']; ?>
                    </p>
                </div>

                <div class="space-y-4 pt-6 border-t border-slate-50 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Syllabus Flow</span>
                        </div>
                        <span class="text-[10px] font-black text-slate-800 dark:text-white">65% Active</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-900 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-primary-600 h-full rounded-full transition-all duration-500" style="width: 65%"></div>
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <a href="attendance-take.php?subject_id=<?php echo $s['id']; ?>" class="flex-1 py-3 bg-primary-600 text-white rounded-xl flex items-center justify-center font-bold text-[10px] uppercase tracking-widest shadow-soft hover:bg-primary-700 transition-all">Take Attendance</a>
                        <a href="marks-entry.php?subject_id=<?php echo $s['id']; ?>" class="w-11 h-11 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary-600 hover:border-primary-500 transition-all">
                            <i class="fas fa-pen-to-square text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>