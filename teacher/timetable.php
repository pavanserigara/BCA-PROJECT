<?php
$page_title = "Schedule";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];

$timetable_data = [];
$stmt_tt = $pdo->prepare("SELECT tt.*, s.name as subject_name, c.name as course_name 
                          FROM timetable tt 
                          JOIN subjects s ON tt.subject_id = s.id 
                          JOIN courses c ON tt.course_id = c.id 
                          WHERE tt.teacher_id = ? 
                          ORDER BY FIELD(tt.day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), tt.start_time");
$stmt_tt->execute([$teacher_id]);
$results = $stmt_tt->fetchAll();

foreach ($results as $row) {
    $timetable_data[$row['day']][] = $row;
}

$today = date('l');
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Teaching Schedule</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Plan your academic day and lecture commitments.</p>
    </div>
    <div class="bg-rose-50 dark:bg-rose-500/10 px-6 py-3 rounded-2xl border border-rose-100 dark:border-rose-500/20">
        <p class="text-[10px] font-bold text-rose-600 uppercase tracking-widest leading-none mb-1">Weekly Load</p>
        <p class="text-lg font-black text-slate-800 dark:text-white"><?php echo count($results); ?> Lectures</p>
    </div>
</div>

<div class="space-y-8 pb-10">
    <?php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    foreach ($days as $day):
        $is_today = $day == $today;
        $slots = $timetable_data[$day] ?? [];
    ?>
    <div class="relative">
        <div class="flex items-center gap-4 mb-4">
            <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]"><?php echo $day; ?></h4>
            <?php if ($is_today): ?>
                <span class="px-2.5 py-1 bg-rose-500 text-white text-[9px] font-bold uppercase tracking-widest rounded-lg shadow-sm">Current</span>
            <?php endif; ?>
            <div class="h-px bg-slate-100 dark:bg-slate-700 flex-1"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($slots)): ?>
                <div class="md:col-span-3 py-10 text-center bg-slate-50/50 dark:bg-slate-900/50 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700">
                    <p class="text-xs font-medium text-slate-400">No lectures scheduled</p>
                </div>
            <?php else: ?>
                <?php foreach ($slots as $slot): ?>
                <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] shadow-soft border border-slate-100 dark:border-slate-700 flex flex-col justify-between hover:border-primary-500 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2 text-slate-400">
                            <i class="far fa-clock text-[10px]"></i>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-600 dark:text-slate-400">
                                <?php echo date('h:i A', strtotime($slot['start_time'])); ?> - <?php echo date('h:i A', strtotime($slot['end_time'])); ?>
                            </span>
                        </div>
                        <span class="text-[9px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-500/10 px-2 py-1 rounded-lg">Rm <?php echo $slot['room_no']; ?></span>
                    </div>

                    <h5 class="text-lg font-extrabold text-slate-800 dark:text-white leading-tight mb-1 group-hover:text-primary-600 transition-colors">
                        <?php echo $slot['subject_name']; ?>
                    </h5>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-6">
                        <?php echo $slot['course_name']; ?> (Sem <?php echo $slot['semester']; ?>)
                    </p>

                    <div class="pt-4 border-t border-slate-50 dark:border-slate-700 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Active Session</span>
                        </div>
                        <a href="attendance-take.php?subject_id=<?php echo $slot['subject_id']; ?>" class="text-[10px] font-black text-primary-600 hover:text-primary-700 uppercase tracking-widest transition-colors">Attendance</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>