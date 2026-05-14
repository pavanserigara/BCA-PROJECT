<?php
$page_title = "Academic Schedule";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Get Student info (Course and Semester)
$stmt_std = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt_std->execute([$student_id]);
$student = $stmt_std->fetch();

$timetable_data = [];
if ($student) {
    // Fetch Timetable for the student's course and semester
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

$current_day = date('l');
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic">Institutional Schedule</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Your synchronized roadmap for academic engagement and classroom sessions.</p>
    </div>
    
    <div class="flex items-center space-x-4 bg-white dark:bg-slate-800 p-4 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-800">
        <div class="w-12 h-12 bg-primary-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none mb-1">Today</p>
            <p class="text-base font-black text-slate-800 dark:text-white leading-none italic"><?php echo date('l, F d'); ?></p>
        </div>
    </div>
</div>

<div class="space-y-15 pb-20">
    <?php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    foreach ($days as $day):
        $is_today = ($day === $current_day);
        $slots = $timetable_data[$day] ?? [];
    ?>
        <div class="bg-white dark:bg-slate-900/50 rounded-[3rem] shadow-premium border <?php echo $is_today ? 'border-primary-500/50 ring-4 ring-primary-500/5' : 'border-slate-100 dark:border-slate-800'; ?> overflow-hidden group transition-all duration-500">
            <div class="p-8 border-b <?php echo $is_today ? 'bg-primary-600 text-white' : 'bg-slate-50/50 dark:bg-slate-800/50 text-slate-800 dark:text-white'; ?> flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 <?php echo $is_today ? 'bg-white/20' : 'bg-white dark:bg-slate-800'; ?> rounded-2xl flex items-center justify-center text-sm font-black italic shadow-sm"><?php echo substr($day, 0, 1); ?></div>
                    <h4 class="text-xl font-black tracking-tight leading-none italic uppercase"><?php echo $day; ?></h4>
                    <?php if ($is_today): ?>
                        <span class="bg-white/20 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest animate-pulse">Live</span>
                    <?php endif; ?>
                </div>
                <p class="text-[10px] font-black <?php echo $is_today ? 'text-white/70' : 'text-slate-400 dark:text-slate-500'; ?> uppercase tracking-widest italic"><?php echo count($slots); ?> Sessions Registered</p>
            </div>
            
            <div class="p-10">
                <?php if (empty($slots)): ?>
                    <div class="py-12 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-[1.5rem] flex items-center justify-center text-slate-200 dark:text-slate-700 mb-4 italic text-2xl">Zzz</div>
                        <p class="text-slate-400 dark:text-slate-600 italic font-bold">No academic engagement scheduled for this day.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                        <?php foreach ($slots as $slot): ?>
                            <div class="relative p-8 bg-slate-50 dark:bg-slate-800/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-700 group/slot hover:bg-white dark:hover:bg-slate-800 hover:border-primary-500/30 transition-all duration-300">
                                <div class="absolute top-0 right-0 p-8">
                                    <p class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest italic group-hover/slot:text-primary-600/50 transition-colors">RM <?php echo $slot['room_no']; ?></p>
                                </div>
                                
                                <div class="flex items-center space-x-3 mb-6">
                                    <span class="text-[9px] font-black text-primary-600 bg-primary-50 dark:bg-primary-500/10 px-3 py-2 rounded-xl border border-primary-100 dark:border-primary-500/20 uppercase tracking-widest italic leading-none">
                                        <?php echo date('h:i A', strtotime($slot['start_time'])); ?> - <?php echo date('h:i A', strtotime($slot['end_time'])); ?>
                                    </span>
                                </div>
                                
                                <h5 class="text-lg font-black text-slate-800 dark:text-white tracking-tight leading-tight italic uppercase mb-2 group-hover/slot:text-primary-600 transition-colors">
                                    <?php echo $slot['subject_name']; ?>
                                </h5>
                                
                                <div class="flex items-center space-x-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                                    <div class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500 italic uppercase">
                                        <?php echo substr($slot['teacher_name'], 0, 1); ?>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest italic"><?php echo $slot['teacher_name']; ?></p>
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