<?php
$page_title = "Academic Schedule";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Get Student info
$stmt_std = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt_std->execute([$student_id]);
$student = $stmt_std->fetch();

$timetable_data = [];
if ($student) {
    $stmt_tt = $pdo->prepare("SELECT tt.*, s.name as subject_name, u.full_name as teacher_name 
                              FROM timetable tt 
                              JOIN subjects s ON tt.subject_id = s.id 
                              JOIN users u ON tt.teacher_id = u.id 
                              WHERE tt.course_id = ? AND tt.semester = ? 
                              ORDER BY FIELD(tt.day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), tt.start_time");
    $stmt_tt->execute([$student['course_id'], $student['semester']]);
    $results = $stmt_tt->fetchAll();

    foreach ($results as $row) {
        $timetable_data[$row['day']][] = $row;
    }
}

$today = date('l');
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Institutional Timetable</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Weekly lecture cycle and classroom assignments.</p>
    </div>
    <div class="bg-primary-50 dark:bg-primary-500/10 px-6 py-3 rounded-2xl border border-primary-100 dark:border-primary-500/20">
        <p class="text-[10px] font-black text-primary-600 uppercase tracking-widest leading-none mb-1">Today</p>
        <p class="text-base font-black text-slate-800 dark:text-white"><?php echo date('l, d M'); ?></p>
    </div>
</div>

<div class="space-y-12 mb-20">
    <?php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    foreach ($days as $day):
        $is_today = ($day == $today);
        $sessions = $timetable_data[$day] ?? [];
    ?>
        <div class="relative group">
            <div class="flex items-center space-x-4 mb-6">
                <h4 class="text-xl font-black <?php echo $is_today ? 'text-primary-600' : 'text-slate-400 dark:text-slate-500'; ?> uppercase tracking-[0.2em] italic transition-all duration-500"><?php echo $day; ?></h4>
                <?php if ($is_today): ?>
                    <span class="px-2.5 py-1 bg-primary-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg animate-pulse">Live</span>
                <?php endif; ?>
                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800"></div>
            </div>

            <?php if (empty($sessions)): ?>
                <div class="bg-white/50 dark:bg-slate-800/30 p-10 rounded-[2.5rem] border border-dashed border-slate-200 dark:border-slate-700 text-center">
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 italic uppercase">No academic sessions scheduled for this cycle.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <?php foreach ($sessions as $slot): ?>
                        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50 hover:border-primary-500 transition-all duration-500 relative overflow-hidden group/item">
                            <div class="absolute top-0 right-0 p-6 flex flex-col items-end">
                                <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 dark:group-hover/item:text-primary-400/30 transition-all"><?php echo $slot['room_no']; ?></span>
                            </div>
                            
                            <div class="flex items-center space-x-2 text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest mb-4">
                                <i class="far fa-clock"></i>
                                <span><?php echo date('h:i A', strtotime($slot['start_time'])); ?> - <?php echo date('h:i A', strtotime($slot['end_time'])); ?></span>
                            </div>

                            <h5 class="text-sm font-black text-slate-800 dark:text-white leading-tight mb-2 group-hover/item:text-primary-600 transition-colors uppercase italic"><?php echo $slot['subject_name']; ?></h5>
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Prof. <?php echo $slot['teacher_name']; ?></p>
                            
                            <div class="mt-6 pt-6 border-t border-slate-50 dark:border-slate-700 flex items-center justify-between">
                                <div class="flex items-center space-x-1.5 grayscale group-hover/item:grayscale-0 transition-all">
                                    <div class="w-1 h-1 rounded-full bg-emerald-500"></div>
                                    <span class="text-[9px] font-black text-slate-400 dark:text-slate-600 group-hover/item:text-emerald-500 uppercase">Confirmed</span>
                                </div>
                                <i class="fas fa-location-dot text-slate-300 dark:text-slate-700 text-xs"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>