<?php
$page_title = "Overview Dashboard";
require_once 'includes/header.php';

$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_departments = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();

$recent_students = $pdo->query("SELECT u.full_name, u.created_at, s.roll_no, s.gender, c.name as course_name 
                                FROM students s 
                                JOIN users u ON s.user_id = u.id 
                                JOIN courses c ON s.course_id = c.id 
                                ORDER BY u.created_at DESC LIMIT 5")->fetchAll();

$notices = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Real attendance data for last 6 months
$attendance_data = ['labels' => [], 'data' => []];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_label = date('M', strtotime("-$i months"));
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status IN ('Present', 'Late') THEN 1 ELSE 0 END) as present
        FROM attendance WHERE DATE_FORMAT(date, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $res = $stmt->fetch();
    $attendance_data['labels'][] = $month_label;
    $attendance_data['data'][] = $res['total'] > 0 ? round(($res['present'] / $res['total']) * 100, 1) : 0;
}

// Real gender distribution
$gender_counts = $pdo->query("SELECT gender, COUNT(*) as count FROM students GROUP BY gender")->fetchAll(PDO::FETCH_KEY_PAIR);
$male_count = $gender_counts['Male'] ?? 0;
$female_count = $gender_counts['Female'] ?? 0;
$total_gender = $male_count + $female_count;
$male_percent = $total_gender > 0 ? round(($male_count / $total_gender) * 100) : 0;
$female_percent = $total_gender > 0 ? round(($female_count / $total_gender) * 100) : 0;

$today = date('l, F j, Y');
$hour = date('H');
if ($hour < 12) $greeting = 'Good Morning';
elseif ($hour < 17) $greeting = 'Good Afternoon';
else $greeting = 'Good Evening';
?>

<!-- Welcome Banner -->
<div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-primary-900 rounded-xl p-4 sm:p-5 lg:p-6 mb-4 lg:mb-5 overflow-hidden">
    <div class="absolute -top-12 -right-12 w-32 h-32 bg-primary-600/20 rounded-full blur-2xl"></div>
    <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-violet-600/15 rounded-full blur-xl"></div>

    <div class="relative z-10 flex flex-col sm:flex-row sm:items-end justify-between gap-3">
        <div>
            <div class="flex items-center space-x-1.5 mb-2">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-medium text-slate-400 tracking-wide"><?php echo $today; ?></span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight leading-tight mb-1">
                <?php echo $greeting; ?>,
                <span class="bg-gradient-to-r from-primary-400 to-violet-400 bg-clip-text text-transparent">
                    <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>
                </span>
            </h1>
            <p class="text-slate-400 text-[11px] max-w-md leading-relaxed">
                Overview of institutional operations and academic performance.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="students-add.php"
                class="inline-flex items-center space-x-1.5 bg-primary-600 hover:bg-primary-700 text-white px-3 py-1.5 rounded-lg font-medium text-[11px] shadow-md shadow-primary-600/20 hover:-translate-y-px transition-all">
                <i class="fas fa-plus text-[9px]"></i><span>Enroll Student</span>
            </a>
            <a href="notices.php"
                class="inline-flex items-center space-x-1.5 bg-white/10 hover:bg-white/15 text-white px-3 py-1.5 rounded-lg font-medium text-[11px] border border-white/10 transition-all">
                <i class="fas fa-bullhorn text-[9px]"></i><span>Post Notice</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4 lg:mb-5">

    <div class="bg-white rounded-xl p-3.5 shadow-card hover:shadow-card-hover transition-all duration-200 border border-slate-100/80 group">
        <div class="flex items-center justify-between mb-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
                <i class="fas fa-user-graduate text-[11px]"></i>
            </div>
            <span class="text-[9px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">
                <i class="fas fa-arrow-up text-[7px] mr-0.5"></i>Active
            </span>
        </div>
        <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Students</p>
        <h3 class="text-xl font-bold text-slate-800"><?php echo $total_students; ?></h3>
    </div>

    <div class="bg-white rounded-xl p-3.5 shadow-card hover:shadow-card-hover transition-all duration-200 border border-slate-100/80 group">
        <div class="flex items-center justify-between mb-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-violet-600 rounded-lg flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
                <i class="fas fa-chalkboard-teacher text-[11px]"></i>
            </div>
            <span class="text-[9px] font-semibold text-violet-600 bg-violet-50 px-1.5 py-0.5 rounded border border-violet-100">
                <i class="fas fa-check text-[7px] mr-0.5"></i>Verified
            </span>
        </div>
        <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Faculty</p>
        <h3 class="text-xl font-bold text-slate-800"><?php echo $total_teachers; ?></h3>
    </div>

    <div class="bg-white rounded-xl p-3.5 shadow-card hover:shadow-card-hover transition-all duration-200 border border-slate-100/80 group">
        <div class="flex items-center justify-between mb-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-rose-500 to-rose-600 rounded-lg flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
                <i class="fas fa-building text-[11px]"></i>
            </div>
            <span class="text-[9px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">Units</span>
        </div>
        <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Departments</p>
        <h3 class="text-xl font-bold text-slate-800"><?php echo $total_departments; ?></h3>
    </div>

    <div class="bg-white rounded-xl p-3.5 shadow-card hover:shadow-card-hover transition-all duration-200 border border-slate-100/80 group">
        <div class="flex items-center justify-between mb-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
                <i class="fas fa-graduation-cap text-[11px]"></i>
            </div>
            <span class="text-[9px] font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">Programs</span>
        </div>
        <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Courses</p>
        <h3 class="text-xl font-bold text-slate-800"><?php echo $total_courses; ?></h3>
    </div>

</div>

<!-- Charts & Notices -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-3 sm:gap-4 mb-4 lg:mb-5">

    <!-- Attendance Chart -->
    <div class="xl:col-span-2 bg-white rounded-xl p-4 sm:p-5 shadow-card border border-slate-100/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h4 class="text-[13px] font-bold text-slate-800">Attendance Insights</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Participation over the last 6 months</p>
            </div>
            <select class="text-[10px] font-medium border border-slate-200 bg-slate-50 rounded-md px-2 py-1 focus:ring-1 focus:ring-primary-100 text-slate-500 outline-none cursor-pointer">
                <option>Current Semester</option>
                <option>Last Semester</option>
            </select>
        </div>
        <div class="h-48 sm:h-56 w-full">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Recent Notices -->
    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-slate-100/80 flex flex-col">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-[13px] font-bold text-slate-800">Latest Notices</h4>
            <a href="notices.php" class="text-[10px] font-semibold text-primary-600 hover:underline">View All</a>
        </div>
        <div class="space-y-1 flex-1 overflow-y-auto max-h-[220px] custom-scroll">
            <?php if (empty($notices)): ?>
                <div class="text-center py-6">
                    <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center mx-auto mb-2 text-slate-300"><i class="fas fa-bullhorn text-sm"></i></div>
                    <p class="text-slate-400 text-[11px]">No notices yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notices as $notice): ?>
                    <div class="flex items-start space-x-2 group cursor-pointer hover:bg-primary-50/50 p-2 rounded-lg transition-all">
                        <div class="w-7 h-7 min-w-[1.75rem] bg-primary-50 rounded-md flex items-center justify-center text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-all mt-px">
                            <i class="fas fa-calendar-day text-[9px]"></i>
                        </div>
                        <div class="min-w-0">
                            <h6 class="text-[11px] font-semibold text-slate-700 line-clamp-1 group-hover:text-primary-600"><?php echo htmlspecialchars($notice['title']); ?></h6>
                            <p class="text-[9px] text-slate-400 mt-px"><?php echo date('M d, Y h:i A', strtotime($notice['created_at'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a href="notices.php" class="mt-3 w-full py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium text-[11px] transition-all flex items-center justify-center space-x-1 shadow-sm">
            <i class="fas fa-plus-circle text-[9px]"></i><span>Add Notice</span>
        </a>
    </div>

</div>

<!-- Recent Students & Fee -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-3 sm:gap-4 mb-4 lg:mb-5">

    <!-- Recent Students -->
    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-slate-100/80">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-[13px] font-bold text-slate-800">Recent Enrollments</h4>
            <a href="students-list.php" class="text-[10px] font-semibold text-primary-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto -mx-4 sm:-mx-5 px-4 sm:px-5">
            <table class="w-full text-left min-w-[360px]">
                <thead>
                    <tr class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                        <th class="pb-2 pr-3">Student</th>
                        <th class="pb-2 px-3">Program</th>
                        <th class="pb-2 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($recent_students)): ?>
                        <tr><td colspan="3" class="py-6 text-center"><p class="text-slate-400 text-[11px]">No students enrolled yet.</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_students as $std): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-2.5 pr-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 rounded-md bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center font-bold text-[9px]">
                                            <?php echo strtoupper(substr($std['full_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <span class="text-[11px] font-semibold text-slate-700"><?php echo htmlspecialchars($std['full_name']); ?></span>
                                            <p class="text-[9px] text-slate-400"><?php echo $std['roll_no']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="text-[9px] font-semibold text-primary-600 bg-primary-50 px-1.5 py-0.5 rounded border border-primary-100 whitespace-nowrap">
                                        <?php echo htmlspecialchars($std['course_name']); ?>
                                    </span>
                                </td>
                                <td class="py-2.5 text-right text-[10px] font-medium text-slate-400">
                                    <?php echo date('M d', strtotime($std['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Financial -->
    <div class="bg-white rounded-xl p-4 sm:p-5 shadow-card border border-slate-100/80">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-[13px] font-bold text-slate-800">Financial Overview</h4>
            <div class="flex items-center space-x-1">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[9px] font-semibold text-emerald-600">Live</span>
            </div>
        </div>

        <div class="flex items-end justify-between mb-4">
            <div>
                <p class="text-[9px] font-medium text-slate-400 uppercase tracking-wider mb-0.5">Total Collected</p>
                <div class="text-2xl font-bold text-slate-800">₹ 2,45,000</div>
            </div>
            <span class="text-[9px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100">+15% vs Last</span>
        </div>

        <div class="flex items-center justify-center mb-4">
            <div class="relative w-32 h-32">
                <canvas id="genderChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-lg font-bold text-slate-800"><?php echo $total_students; ?></span>
                    <span class="text-[9px] text-slate-400">Total</span>
                </div>
            </div>
        </div>

        <div class="space-y-3 mb-4">
            <div>
                <div class="flex justify-between text-[10px] font-medium text-slate-600 mb-1">
                    <span>Target Achievement</span><span class="text-primary-600">85%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-primary-500 to-violet-500 rounded-full" style="width:85%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-[10px] font-medium text-slate-600 mb-1">
                    <span>Pending Dues</span><span class="text-rose-500">15%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-rose-400 to-rose-500 rounded-full" style="width:15%"></div>
                </div>
            </div>
        </div>

        <a href="fees.php" class="w-full py-2 bg-slate-800 text-white rounded-lg font-medium text-[11px] hover:bg-primary-700 transition-all flex items-center justify-center space-x-1.5 shadow-sm">
            <i class="fas fa-file-invoice-dollar text-[9px]"></i><span>View Fee Report</span>
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-xl p-4 sm:p-5 mb-2 relative overflow-hidden">
    <div class="absolute -top-12 -right-12 w-32 h-32 bg-primary-600/10 rounded-full blur-2xl"></div>
    <h4 class="text-[13px] font-bold text-white mb-3 relative z-10">Quick Actions</h4>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 relative z-10">
        <?php
        $actions = [
            ['students-add.php', 'fa-user-plus', 'Add Student', 'New enrollment', 'from-blue-500 to-blue-600', 'shadow-blue-600/20'],
            ['faculty-add.php', 'fa-chalkboard-teacher', 'Add Faculty', 'Hire staff', 'from-violet-500 to-violet-600', 'shadow-violet-600/20'],
            ['timetable.php', 'fa-calendar-alt', 'Timetable', 'Schedule', 'from-amber-500 to-amber-600', 'shadow-amber-500/20'],
            ['settings.php', 'fa-cog', 'Settings', 'Configuration', 'from-emerald-500 to-emerald-600', 'shadow-emerald-500/20'],
        ];
        foreach ($actions as $act):
        ?>
        <a href="<?php echo $act[0]; ?>"
            class="bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 p-3 sm:p-4 rounded-lg text-center group transition-all hover:-translate-y-px">
            <div class="w-8 h-8 bg-gradient-to-br <?php echo $act[4]; ?> rounded-lg flex items-center justify-center text-white mx-auto mb-2 shadow-md <?php echo $act[5]; ?> group-hover:scale-105 transition-transform">
                <i class="fas <?php echo $act[1]; ?> text-[11px]"></i>
            </div>
            <p class="text-white font-medium text-[11px]"><?php echo $act[2]; ?></p>
            <p class="text-[9px] text-slate-400 mt-px hidden sm:block"><?php echo $act[3]; ?></p>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($attendance_data['labels']); ?>,
                datasets: [{
                    label: 'Attendance (%)',
                    data: <?php echo json_encode($attendance_data['data']); ?>,
                    borderColor: '#4F46E5',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: context => {
                        if (!context.chart.chartArea) return;
                        const {ctx, chartArea:{top,bottom}} = context.chart;
                        const g = ctx.createLinearGradient(0,top,0,bottom);
                        g.addColorStop(0,'rgba(79,70,229,0.08)');
                        g.addColorStop(1,'rgba(79,70,229,0)');
                        return g;
                    },
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4F46E5',
                    pointHoverRadius: 5,
                    pointRadius: 3,
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family:'Inter', weight:'600', size:11 },
                        bodyFont: { family:'Inter', weight:'500', size:10 },
                        padding: 8,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    y: { beginAtZero:false, min:60, max:100, grid:{color:'#f1f5f9',drawBorder:false}, ticks:{font:{family:'Inter',weight:'500',size:9},color:'#94a3b8'} },
                    x: { grid:{display:false}, ticks:{font:{family:'Inter',weight:'500',size:9},color:'#94a3b8'} }
                }
            }
        });
    }

    const gCtx = document.getElementById('genderChart');
    if (gCtx) {
        new Chart(gCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Male','Female'],
                datasets: [{ data:[<?php echo $male_count; ?>, <?php echo $female_count; ?>], backgroundColor:['#4F46E5','#F43F5E'], borderWidth:0, borderRadius:3, spacing:2 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: { display:true, position:'bottom', labels:{padding:8,usePointStyle:true,pointStyle:'circle',font:{family:'Inter',weight:'500',size:9},color:'#64748B'} },
                    tooltip: { backgroundColor:'#0f172a', titleFont:{family:'Inter',weight:'600',size:10}, bodyFont:{family:'Inter',weight:'500',size:9}, padding:8, cornerRadius:6 }
                }
            }
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>