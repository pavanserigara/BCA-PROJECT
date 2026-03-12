<?php
$page_title = "Overview Dashboard";
require_once 'includes/header.php';

// Fetch stats for the dashboard
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_departments = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();

// Recent Students (Live Data)
$recent_students = $pdo->query("SELECT u.full_name, u.created_at, s.roll_no, s.gender, c.name as course_name 
                                FROM students s 
                                JOIN users u ON s.user_id = u.id 
                                JOIN courses c ON s.course_id = c.id 
                                ORDER BY u.created_at DESC LIMIT 5")->fetchAll();

// Recent Notices
$notices = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Attendance mock data
$attendance_data = [
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'data' => [88, 92, 85, 91, 89, 94]
];

// Today's date info
$today = date('l, F j, Y');
$greeting = '';
$hour = date('H');
if ($hour < 12)
    $greeting = 'Good Morning';
elseif ($hour < 17)
    $greeting = 'Good Afternoon';
else
    $greeting = 'Good Evening';
?>

<!-- Hero Welcome Banner -->
<div class="bg-slate-900 rounded-[4rem] p-12 md:p-16 mb-12 relative overflow-hidden group">
    <!-- Ambient decorative elements -->
    <div
        class="absolute -top-20 -right-20 w-60 h-60 bg-indigo-600/20 rounded-full group-hover:scale-150 transition-all duration-1000">
    </div>
    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-violet-600/10 rounded-full"></div>
    <div class="absolute top-1/2 right-1/4 w-20 h-20 bg-indigo-500/10 rounded-full"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-10">
        <div>
            <div class="flex items-center space-x-3 mb-6">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Live Dashboard •
                    <?php echo $today; ?></span>
            </div>
            <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight leading-none italic mb-4">
                <?php echo $greeting; ?>,<br>
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-violet-400 to-purple-400">
                    <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>
                </span>
            </h1>
            <p class="text-slate-400 font-medium text-lg italic max-w-xl">
                Here's an overview of your institutional operations and academic performance metrics.
            </p>
        </div>

        <div class="flex items-center space-x-4">
            <a href="students-add.php"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-5 rounded-[2rem] font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-600/30 hover:-translate-y-1 transition-all flex items-center space-x-3 italic">
                <i class="fas fa-plus"></i>
                <span>Enroll Student</span>
            </a>
            <a href="notices.php"
                class="bg-white/10 hover:bg-white/15 text-white px-8 py-5 rounded-[2rem] font-black text-[10px] uppercase tracking-widest transition-all flex items-center space-x-3 italic border border-white/10">
                <i class="fas fa-bullhorn"></i>
                <span>Post Notice</span>
            </a>
        </div>
    </div>
</div>

<!-- Analytics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">

    <!-- Total Students -->
    <div
        class="bg-white p-8 rounded-[3rem] shadow-sm border border-indigo-100/30 hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500 group relative overflow-hidden">
        <div
            class="absolute -top-8 -right-8 w-24 h-24 bg-indigo-600/5 rounded-full group-hover:scale-200 transition-all duration-700">
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-200 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <div
                    class="text-emerald-500 text-[10px] font-black bg-emerald-50 px-3 py-1.5 rounded-xl flex items-center border border-emerald-100 uppercase tracking-widest italic">
                    <i class="fas fa-arrow-up mr-1.5 text-[8px]"></i>Active
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Total Students</p>
            <h3 class="text-4xl font-black text-slate-800 tracking-tight leading-none"><?php echo $total_students; ?>
            </h3>
        </div>
    </div>

    <!-- Total Faculty -->
    <div
        class="bg-white p-8 rounded-[3rem] shadow-sm border border-violet-100/30 hover:shadow-2xl hover:shadow-violet-50 transition-all duration-500 group relative overflow-hidden">
        <div
            class="absolute -top-8 -right-8 w-24 h-24 bg-violet-600/5 rounded-full group-hover:scale-200 transition-all duration-700">
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-14 h-14 bg-violet-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-violet-200 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                </div>
                <div
                    class="text-violet-500 text-[10px] font-black bg-violet-50 px-3 py-1.5 rounded-xl flex items-center border border-violet-100 uppercase tracking-widest italic">
                    <i class="fas fa-check mr-1.5 text-[8px]"></i>Verified
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Total Faculty</p>
            <h3 class="text-4xl font-black text-slate-800 tracking-tight leading-none"><?php echo $total_teachers; ?>
            </h3>
        </div>
    </div>

    <!-- Departments -->
    <div
        class="bg-white p-8 rounded-[3rem] shadow-sm border border-rose-100/30 hover:shadow-2xl hover:shadow-rose-50 transition-all duration-500 group relative overflow-hidden">
        <div
            class="absolute -top-8 -right-8 w-24 h-24 bg-rose-600/5 rounded-full group-hover:scale-200 transition-all duration-700">
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-14 h-14 bg-rose-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-rose-200 group-hover:scale-110 transition-transform">
                    <i class="fas fa-building text-xl"></i>
                </div>
                <div
                    class="text-rose-500 text-[10px] font-black bg-rose-50 px-3 py-1.5 rounded-xl flex items-center border border-rose-100 uppercase tracking-widest italic">
                    <i class="fas fa-building mr-1.5 text-[8px]"></i>Units
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Departments</p>
            <h3 class="text-4xl font-black text-slate-800 tracking-tight leading-none"><?php echo $total_departments; ?>
            </h3>
        </div>
    </div>

    <!-- Active Courses -->
    <div
        class="bg-white p-8 rounded-[3rem] shadow-sm border border-amber-100/30 hover:shadow-2xl hover:shadow-amber-50 transition-all duration-500 group relative overflow-hidden">
        <div
            class="absolute -top-8 -right-8 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-200 transition-all duration-700">
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-amber-200 group-hover:scale-110 transition-transform">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div
                    class="text-amber-500 text-[10px] font-black bg-amber-50 px-3 py-1.5 rounded-xl flex items-center border border-amber-100 uppercase tracking-widest italic">
                    <i class="fas fa-book mr-1.5 text-[8px]"></i>Programs
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Active Courses</p>
            <h3 class="text-4xl font-black text-slate-800 tracking-tight leading-none"><?php echo $total_courses; ?>
            </h3>
        </div>
    </div>

</div>

<!-- Charts and Notices -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-12">

    <!-- Attendance Insights -->
    <div
        class="lg:col-span-2 bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Attendance Insights</h4>
                <p class="text-sm text-slate-400 font-medium italic mt-1">Student participation rates over the last 6
                    months</p>
            </div>
            <select
                class="text-[10px] font-black border-2 border-slate-100 bg-slate-50 rounded-2xl px-5 py-3 focus:ring-0 uppercase tracking-widest text-slate-600 italic outline-none">
                <option>Current Semester</option>
                <option>Last Semester</option>
            </select>
        </div>

        <div class="h-80 w-full">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Recent Notices -->
    <div
        class="bg-white p-10 rounded-[4rem] shadow-sm border border-indigo-100/30 flex flex-col hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500">
        <div class="flex items-center justify-between mb-10">
            <h4 class="text-xl font-black text-slate-800 tracking-tight italic uppercase">Latest Notices</h4>
            <a href="notices.php"
                class="text-[10px] font-black text-indigo-600 hover:underline tracking-widest uppercase italic">View
                All</a>
        </div>

        <div class="space-y-5 flex-1 overflow-y-auto">
            <?php if (empty($notices)): ?>
                <div class="text-center py-10">
                    <div
                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="fas fa-bullhorn text-2xl"></i>
                    </div>
                    <p class="text-slate-400 text-sm italic font-bold">No notices published yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notices as $notice): ?>
                    <div
                        class="flex items-start space-x-4 group cursor-pointer hover:bg-indigo-50/50 p-4 rounded-2xl transition-all">
                        <div
                            class="w-10 h-10 min-w-[2.5rem] bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                            <i class="fas fa-calendar-day text-sm"></i>
                        </div>
                        <div>
                            <h6 class="text-sm font-black text-slate-800 line-clamp-1 group-hover:text-indigo-600 italic">
                                <?php echo $notice['title']; ?>
                            </h6>
                            <p class="text-[10px] text-slate-400 mt-0.5 font-bold">
                                <?php echo date('M d, Y h:i A', strtotime($notice['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="notices.php"
            class="mt-8 w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[2rem] font-black text-[10px] tracking-widest uppercase transition-all flex items-center justify-center space-x-2 shadow-xl shadow-indigo-100 italic">
            <i class="fas fa-plus-circle"></i> <span>Add New Notice</span>
        </a>
    </div>

</div>

<!-- Recent Students & Fee Collection -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">

    <!-- Recent Students (LIVE DATA) -->
    <div
        class="bg-white p-10 rounded-[4rem] shadow-sm border border-indigo-100/30 hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500">
        <div class="flex items-center justify-between mb-10">
            <h4 class="text-xl font-black text-slate-800 tracking-tight italic uppercase">Recent Enrollments</h4>
            <a href="students-list.php"
                class="text-[10px] font-black text-indigo-600 hover:underline tracking-widest uppercase italic">View
                All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="pb-5 pr-4 italic">Student</th>
                        <th class="pb-5 px-4 italic">Program</th>
                        <th class="pb-5 text-right italic">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/30">
                    <?php if (empty($recent_students)): ?>
                        <tr>
                            <td colspan="3" class="py-12 text-center">
                                <p class="text-slate-400 italic text-sm font-bold">No students enrolled yet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_students as $std): ?>
                            <tr class="group hover:bg-indigo-50/30 transition-all">
                                <td class="py-5 pr-4">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs italic shadow-lg shadow-indigo-100">
                                            <?php echo strtoupper(substr($std['full_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <span
                                                class="text-sm font-black text-slate-800 italic"><?php echo $std['full_name']; ?></span>
                                            <p class="text-[10px] font-bold text-slate-400"><?php echo $std['roll_no']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-4">
                                    <span
                                        class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 uppercase tracking-widest italic">
                                        <?php echo $std['course_name']; ?>
                                    </span>
                                </td>
                                <td class="py-5 text-right text-xs font-bold text-slate-500 italic">
                                    <?php echo date('M d', strtotime($std['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fee Collection Performance -->
    <div
        class="bg-white p-10 rounded-[4rem] shadow-sm border border-indigo-100/30 hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500">
        <div class="flex items-center justify-between mb-10">
            <h4 class="text-xl font-black text-slate-800 tracking-tight italic uppercase">Financial Treasury</h4>
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black text-emerald-500 italic uppercase tracking-widest">Live</span>
            </div>
        </div>

        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Total Collected</p>
                <div class="text-5xl font-black text-slate-800 mt-2 tracking-tight">₹ 2,45,000</div>
            </div>
            <div class="text-right">
                <p
                    class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100">
                    +15% vs Last</p>
            </div>
        </div>

        <div class="space-y-6 mb-10">
            <div>
                <div
                    class="flex justify-between text-[10px] font-black text-slate-600 mb-3 uppercase tracking-widest italic">
                    <span>Target Achievement</span>
                    <span class="text-indigo-600">85%</span>
                </div>
                <div class="w-full h-4 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                    <div class="h-full bg-gradient-to-r from-indigo-600 to-violet-600 rounded-full" style="width: 85%">
                    </div>
                </div>
            </div>
            <div>
                <div
                    class="flex justify-between text-[10px] font-black text-slate-600 mb-3 uppercase tracking-widest italic">
                    <span>Pending Dues</span>
                    <span class="text-rose-500">15%</span>
                </div>
                <div class="w-full h-4 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                    <div class="h-full bg-gradient-to-r from-rose-400 to-rose-500 rounded-full" style="width: 15%">
                    </div>
                </div>
            </div>
        </div>

        <a href="fees.php"
            class="w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] tracking-widest uppercase shadow-xl hover:bg-indigo-700 hover:-translate-y-1 transition-all flex items-center justify-center space-x-3 italic">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Generate Treasury Report</span>
        </a>
    </div>
</div>

<!-- Quick Action Grid -->
<div class="bg-slate-900 rounded-[4rem] p-12 mb-8 relative overflow-hidden">
    <div class="absolute -top-20 -right-20 w-60 h-60 bg-indigo-600/10 rounded-full"></div>
    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-violet-600/10 rounded-full"></div>

    <h4 class="text-xl font-black text-white tracking-tight italic uppercase mb-10 relative z-10">Quick Actions</h4>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative z-10">
        <a href="students-add.php"
            class="bg-white/5 hover:bg-white/10 backdrop-blur-sm border border-white/10 p-8 rounded-[2.5rem] text-center group transition-all hover:-translate-y-1">
            <div
                class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-indigo-600/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-user-plus text-xl"></i>
            </div>
            <p class="text-white font-black text-xs italic">Add Student</p>
            <p class="text-[9px] text-slate-500 font-bold italic mt-1">New enrollment</p>
        </a>
        <a href="faculty-add.php"
            class="bg-white/5 hover:bg-white/10 backdrop-blur-sm border border-white/10 p-8 rounded-[2.5rem] text-center group transition-all hover:-translate-y-1">
            <div
                class="w-14 h-14 bg-violet-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-violet-600/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-chalkboard-teacher text-xl"></i>
            </div>
            <p class="text-white font-black text-xs italic">Recruit Faculty</p>
            <p class="text-[9px] text-slate-500 font-bold italic mt-1">Hiring portal</p>
        </a>
        <a href="timetable.php"
            class="bg-white/5 hover:bg-white/10 backdrop-blur-sm border border-white/10 p-8 rounded-[2.5rem] text-center group transition-all hover:-translate-y-1">
            <div
                class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
            <p class="text-white font-black text-xs italic">Timetable</p>
            <p class="text-[9px] text-slate-500 font-bold italic mt-1">Schedule mgmt</p>
        </a>
        <a href="settings.php"
            class="bg-white/5 hover:bg-white/10 backdrop-blur-sm border border-white/10 p-8 rounded-[2.5rem] text-center group transition-all hover:-translate-y-1">
            <div
                class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-cog text-xl"></i>
            </div>
            <p class="text-white font-black text-xs italic">Settings</p>
            <p class="text-[9px] text-slate-500 font-bold italic mt-1">Global config</p>
        </a>
    </div>
</div>

<script>
    // Attendance Chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($attendance_data['labels']); ?>,
            datasets: [{
                label: 'Attendance Rate (%)',
                data: <?php echo json_encode($attendance_data['data']); ?>,
                borderColor: '#4F46E5',
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                backgroundColor: (context) => {
                    const bgColor = [
                        'rgba(79, 70, 229, 0.08)',
                        'rgba(79, 70, 229, 0)'
                    ];
                    if (!context.chart.chartArea) return;
                    const { ctx, chartArea: { top, bottom } } = context.chart;
                    const gradient = ctx.createLinearGradient(0, top, 0, bottom);
                    gradient.addColorStop(0, bgColor[0]);
                    gradient.addColorStop(1, bgColor[1]);
                    return gradient;
                },
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4F46E5',
                pointHoverRadius: 10,
                pointRadius: 7,
                pointBorderWidth: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { family: 'Inter', weight: 'bold', size: 13 },
                    bodyFont: { family: 'Inter', weight: 'bold', size: 12 },
                    padding: 16,
                    cornerRadius: 16,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 60,
                    max: 100,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { font: { family: 'Inter', weight: '800', size: 10 }, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', weight: '800', size: 10 }, color: '#94a3b8' }
                }
            }
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>