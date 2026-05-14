<?php
$page_title = "My Schedule";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];

// Fetch Timetable for the logged-in teacher
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

$current_day = date('l');
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Faculty Time-Table</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Your personalized weekly academic engagement plan.</p>
    </div>
    <div class="bg-white dark:bg-slate-800 p-4 rounded-[2rem] shadow-soft border border-slate-100 dark:border-slate-700/50 flex items-center space-x-4">
        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Today</p>
            <p class="text-sm font-black text-slate-800 dark:text-white"><?php echo date('D, M d, Y'); ?></p>
        </div>
    </div>
</div>

<div class="space-y-12 pb-20">
    <?php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    foreach ($days as $day):
        $is_today = ($day === $current_day);
    ?>
        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-premium border <?php echo $is_today ? 'border-primary-500' : 'border-slate-100 dark:border-slate-700/50'; ?> overflow-hidden transition-all duration-300">
            <div class="p-6 border-b <?php echo $is_today ? 'bg-primary-600 text-white' : 'bg-slate-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white'; ?> flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <h4 class="text-lg font-black tracking-tight leading-none uppercase"><?php echo $day; ?></h4>
                    <?php if ($is_today): ?>
                        <span class="bg-white/20 px-2 py-1 rounded text-[8px] font-black uppercase tracking-widest">Current Day</span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] font-bold <?php echo $is_today ? 'text-white/80' : 'text-slate-400 dark:text-slate-500'; ?> uppercase tracking-widest">
                    <?php echo count($timetable_data[$day] ?? []); ?> Scheduled Sessions
                </span>
            </div>
            
            <div class="p-8">
                <?php if (empty($timetable_data[$day])): ?>
                    <div class="py-10 text-center">
                        <p class="text-slate-400 italic text-sm">No academic sessions scheduled for this day.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($timetable_data[$day] as $slot): ?>
                            <div class="group p-6 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-100 dark:border-slate-700 hover:border-primary-500 transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-[10px] font-black text-primary-600 bg-primary-50 dark:bg-primary-500/10 px-3 py-1.5 rounded-lg border border-primary-100 dark:border-primary-500/20 uppercase tracking-widest">
                                        <?php echo date('h:i A', strtotime($slot['start_time'])); ?> - <?php echo date('h:i A', strtotime($slot['end_time'])); ?>
                                    </span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Room <?php echo $slot['room_no']; ?></span>
                                </div>
                                <h5 class="text-base font-black text-slate-800 dark:text-white tracking-tight mb-2 group-hover:text-primary-600 transition-colors uppercase">
                                    <?php echo $slot['subject_name']; ?>
                                </h5>
                                <div class="flex items-center justify-between">
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                        <?php echo $slot['course_name']; ?> • SEM <?php echo $slot['semester']; ?>
                                    </p>
                                    <a href="attendance-take.php?subject_id=<?php echo $slot['subject_id']; ?>" class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 hover:text-primary-600 hover:border-primary-600 transition-all shadow-sm">
                                        <i class="fas fa-calendar-check text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>