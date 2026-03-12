<?php
$page_title = "Institutional Teaching Schedule";
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

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Academic Load Profile</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Monitor your teaching hours and assigned
                lecture halls for the current phase.</p>
        </div>

        <div
            class="bg-rose-600 px-8 py-5 rounded-[2.5rem] text-white shadow-2xl shadow-rose-100 flex items-center space-x-6">
            <div class="text-right">
                <p
                    class="text-[10px] font-black text-rose-200 uppercase tracking-widest leading-none mb-1 leading-none italic">
                    Duty Hours</p>
                <p class="text-lg font-black text-white tracking-tight leading-none italic">
                    <?php echo count($results); ?> Weekly Lectures
                </p>
            </div>
            <div
                class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-rose-400 text-xl">
                L</div>
        </div>
    </div>

    <!-- Timetable Grid -->
    <div class="space-y-12 pb-20">
        <?php
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $day):
            $is_today = $day == $today;
            ?>
            <div
                class="bg-white rounded-[4rem] shadow-sm border <?php echo $is_today ? 'border-rose-400 border-2' : 'border-indigo-100/30'; ?> overflow-hidden group hover:shadow-2xl transition-all duration-500">
                <div
                    class="p-12 border-b border-rose-50 <?php echo $is_today ? 'bg-rose-600' : 'bg-slate-50/50'; ?> flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <h4
                            class="text-2xl font-black <?php echo $is_today ? 'text-white' : 'text-slate-800'; ?> tracking-tight leading-none italic uppercase">
                            <?php echo $day; ?> Cycle
                        </h4>
                        <?php if ($is_today): ?>
                            <span
                                class="px-4 py-1.5 bg-white/20 text-white text-[10px] font-black uppercase tracking-widest rounded-full animate-pulse border border-white/20">Duty
                                Cycle Today</span>
                        <?php endif; ?>
                    </div>
                    <span
                        class="text-[10px] font-black <?php echo $is_today ? 'text-rose-100' : 'text-slate-400'; ?> uppercase tracking-widest italic">
                        <?php echo count($timetable_data[$day] ?? []); ?> Academic Hours Scheduled
                    </span>
                </div>
                <div class="p-12">
                    <?php if (empty($timetable_data[$day])): ?>
                        <p class="text-slate-400 italic text-sm py-10 text-center">No teaching assignments scheduled for this
                            day profile.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                            <?php foreach ($timetable_data[$day] as $slot): ?>
                                <div
                                    class="p-10 bg-slate-50/50 rounded-[3rem] border border-slate-50 relative overflow-hidden group/item hover:bg-white hover:border-rose-100/50 hover:shadow-xl hover:shadow-rose-50 transition-all duration-300">
                                    <div class="absolute top-0 right-0 p-8 flex flex-col items-end">
                                        <p
                                            class="text-[9px] font-black text-rose-400 uppercase tracking-widest italic opacity-40 group-hover/item:opacity-100 transition-opacity">
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
                                        class="text-xl font-black text-slate-800 tracking-tight leading-tight group-hover/item:text-rose-600 transition-colors uppercase italic mb-4">
                                        <?php echo $slot['subject_name']; ?>
                                    </h5>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-2">•
                                        <?php echo $slot['course_name']; ?> (Sem
                                        <?php echo $slot['semester']; ?>)
                                    </p>

                                    <div class="mt-8 flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
                                            <span
                                                class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none italic">In
                                                Profile</span>
                                        </div>
                                        <button
                                            class="text-xs text-rose-500 hover:text-rose-700 transition-all italic font-black">Attendance</button>
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