<?php
$page_title = "My Academic Schedule";
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

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Institutional Timetable
            </h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Monitor your academic sessions and
                classroom assignments for this semester.</p>
        </div>
    </div>

    <div class="space-y-12">
        <?php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $day):
            $is_today = $day == $today;
            ?>
            <div
                class="bg-white rounded-[4rem] shadow-sm border <?php echo $is_today ? 'border-indigo-400 border-2' : 'border-indigo-100/30'; ?> overflow-hidden group hover:shadow-2xl transition-all duration-500">
                <div
                    class="p-12 border-b border-indigo-50 <?php echo $is_today ? 'bg-indigo-600' : 'bg-slate-50/50'; ?> flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <h4
                            class="text-2xl font-black <?php echo $is_today ? 'text-white' : 'text-slate-800'; ?> tracking-tight leading-none italic uppercase">
                            <?php echo $day; ?> Cycle
                        </h4>
                        <?php if ($is_today): ?>
                            <span
                                class="px-4 py-1.5 bg-white/20 text-white text-[10px] font-black uppercase tracking-widest rounded-full animate-pulse border border-white/20">Active
                                Today</span>
                        <?php endif; ?>
                    </div>
                    <span
                        class="text-[10px] font-black <?php echo $is_today ? 'text-indigo-100' : 'text-slate-400'; ?> uppercase tracking-widest italic">
                        <?php echo count($timetable_data[$day] ?? []); ?> Academic Hours Scheduled
                    </span>
                </div>
                <div class="p-12">
                    <?php if (empty($timetable_data[$day])): ?>
                        <p class="text-slate-400 italic text-sm py-10 text-center">Institutional schedule is open for this day
                            profile. No sessions found.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                            <?php foreach ($timetable_data[$day] as $slot): ?>
                                <div
                                    class="p-10 bg-slate-50/50 rounded-[3rem] border border-slate-50 relative overflow-hidden group/item hover:bg-white hover:border-indigo-100/50 hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
                                    <div class="absolute top-0 right-0 p-8 flex flex-col items-end">
                                        <p
                                            class="text-[9px] font-black text-indigo-400 uppercase tracking-widest italic opacity-40 group-hover/item:opacity-100 transition-opacity">
                                            Room
                                            <?php echo $slot['room_no']; ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-4 mb-8">
                                        <span
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic border border-slate-100 px-4 py-2 rounded-xl bg-white shadow-sm leading-none">
                                            <?php echo date('h:i A', strtotime($slot['start_time'])); ?> -
                                            <?php echo date('h:i A', strtotime($slot['end_time'])); ?>
                                        </span>
                                    </div>
                                    <h5
                                        class="text-xl font-black text-slate-800 tracking-tight leading-tight group-hover/item:text-indigo-600 transition-colors uppercase italic mb-4">
                                        <?php echo $slot['subject_name']; ?>
                                    </h5>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">•
                                        <?php echo $slot['teacher_name']; ?>
                                    </p>

                                    <div class="mt-8 flex items-center space-x-2">
                                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
                                        <span
                                            class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none italic">In
                                            Session soon</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>