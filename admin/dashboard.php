<?php
$page_title = "Overview Dashboard";
require_once 'includes/header.php';

// Fetch stats for the dashboard
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_departments = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();

// Fetch monthly attendance data for chart (MOCK DATA for visualization)
$attendance_data = [
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'data' => [88, 92, 85, 91, 89, 94]
];

// Recent NOTICES
$notices = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<!-- Analytics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">

    <!-- Total Students -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm shadow-indigo-100 hover:shadow-indigo-200 transition-all border border-indigo-100/30 card-hover">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-14 h-14 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                <i class="fas fa-user-graduate text-xl"></i>
            </div>
            <div class="text-emerald-500 text-xs font-bold bg-emerald-50 px-2.5 py-1 rounded-full flex items-center">
                <i class="fas fa-arrow-up mr-1 text-[10px]"></i> +12%
            </div>
        </div>
        <p class="text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">Total Students</p>
        <h3 class="text-3xl font-black text-slate-800 tracking-tight">
            <?php echo $total_students; ?>
        </h3>
    </div>

    <!-- Total Teachers -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm shadow-indigo-100 hover:shadow-indigo-200 transition-all border border-indigo-100/30 card-hover">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-14 h-14 bg-blue-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-chalkboard-teacher text-xl"></i>
            </div>
            <div class="text-slate-400 text-xs font-bold bg-slate-50 px-2.5 py-1 rounded-full flex items-center">
                <i class="fas fa-minus mr-1 text-[10px]"></i> 0%
            </div>
        </div>
        <p class="text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">Total Faculty</p>
        <h3 class="text-3xl font-black text-slate-800 tracking-tight">
            <?php echo $total_teachers; ?>
        </h3>
    </div>

    <!-- Departments -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm shadow-indigo-100 hover:shadow-indigo-200 transition-all border border-indigo-100/30 card-hover">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-14 h-14 bg-rose-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-rose-200">
                <i class="fas fa-building text-xl"></i>
            </div>
            <div class="text-emerald-500 text-xs font-bold bg-emerald-50 px-2.5 py-1 rounded-full flex items-center">
                <i class="fas fa-check mr-1 text-[10px]"></i> Active
            </div>
        </div>
        <p class="text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">Departments</p>
        <h3 class="text-3xl font-black text-slate-800 tracking-tight">
            <?php echo $total_departments; ?>
        </h3>
    </div>

    <!-- Courses -->
    <div
        class="bg-white p-6 rounded-[2rem] shadow-sm shadow-indigo-100 hover:shadow-indigo-200 transition-all border border-indigo-100/30 card-hover">
        <div class="flex items-center justify-between mb-4">
            <div
                class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                <i class="fas fa-graduation-cap text-xl"></i>
            </div>
            <div class="text-amber-500 text-xs font-bold bg-amber-50 px-2.5 py-1 rounded-full flex items-center">
                <i class="fas fa-clock mr-1 text-[10px]"></i> 1 New
            </div>
        </div>
        <p class="text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1">Active Courses</p>
        <h3 class="text-3xl font-black text-slate-800 tracking-tight">
            <?php echo $total_courses; ?>
        </h3>
    </div>

</div>

<!-- Charts and Recent Activities -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Attendance Insights -->
    <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-indigo-50/50">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h4 class="text-xl font-bold text-slate-800">Attendance Overview</h4>
                <p class="text-sm text-slate-500">Student participation rates over the last 6 months</p>
            </div>
            <select class="text-xs font-bold border-none bg-slate-100 rounded-xl px-4 py-2 focus:ring-0">
                <option>Current Semester</option>
                <option>Last Semester</option>
            </select>
        </div>

        <div class="h-80 w-full">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Recent Notices -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-indigo-50/50 flex flex-col">
        <div class="flex items-center justify-between mb-8">
            <h4 class="text-xl font-bold text-slate-800">Latest Notices</h4>
            <a href="notices.php"
                class="text-xs font-bold text-indigo-600 hover:underline tracking-widest uppercase">View All</a>
        </div>

        <div class="space-y-6 flex-1 overflow-y-auto">
            <?php if (empty($notices)): ?>
                <div class="text-center py-10">
                    <div
                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="fas fa-bullhorn text-2xl"></i>
                    </div>
                    <p class="text-slate-400 text-sm italic">No recent notices published.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notices as $notice): ?>
                    <div
                        class="flex items-start space-x-4 group cursor-pointer hover:bg-slate-50 p-2 rounded-2xl transition-all">
                        <div
                            class="w-10 h-10 min-w-[2.5rem] bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                            <i class="fas fa-calendar-day text-sm"></i>
                        </div>
                        <div>
                            <h6 class="text-sm font-bold text-slate-800 line-clamp-1 group-hover:text-indigo-600">
                                <?php echo $notice['title']; ?>
                            </h6>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                <?php echo date('M d, Y h:i A', strtotime($notice['created_at'])); ?>
                            </p>
                            <p class="text-[11px] text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                <?php echo strip_tags($notice['content']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button
            class="mt-8 w-full py-4 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-2xl font-bold text-xs tracking-widest uppercase transition-all flex items-center justify-center space-x-2">
            <i class="fas fa-plus-circle"></i> <span>Add New Notice</span>
        </button>
    </div>

</div>

<!-- Second Row: Recent Admissions & Fee Collection -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-indigo-50/50">
        <h4 class="text-xl font-bold text-slate-800 mb-6">Recent Students</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="pb-4 pr-4">Student</th>
                        <th class="pb-4 px-4">Course</th>
                        <th class="pb-4 px-4">Gender</th>
                        <th class="pb-4 text-right">Date Applied</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/50">
                    <?php
                    // MOCK Table Data
                    $mock_students = [
                        ['name' => 'Arun Kumar', 'course' => 'BCA', 'gender' => 'Male', 'date' => 'Oct 12, 2025'],
                        ['name' => 'Sara Ali', 'course' => 'BBA', 'gender' => 'Female', 'date' => 'Oct 10, 2025'],
                        ['name' => 'Vikram Singh', 'course' => 'BCA', 'gender' => 'Male', 'date' => 'Oct 09, 2025'],
                    ];
                    foreach ($mock_students as $std): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 pr-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                        <?php echo substr($std['name'], 0, 1); ?>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700">
                                        <?php echo $std['name']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                                    <?php echo $std['course']; ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-500">
                                <?php echo $std['gender']; ?>
                            </td>
                            <td class="py-4 text-right text-xs font-bold text-slate-600">
                                <?php echo $std['date']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fee Collection Performance -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-indigo-50/50">
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-xl font-bold text-slate-800">Fee Collection</h4>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                <span class="text-xs font-bold text-indigo-500">Target Reached</span>
            </div>
        </div>

        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Collected</p>
                <div class="text-4xl font-black text-slate-800 mt-1">₹ 2,45,000</div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">+15% vs Last Year</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-xs font-bold text-slate-600 mb-2">
                    <span>Target Achievement</span>
                    <span>85%</span>
                </div>
                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500" style="width: 85%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs font-bold text-slate-600 mb-2">
                    <span>Pending Dues</span>
                    <span>15%</span>
                </div>
                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-rose-400" style="width: 15%"></div>
                </div>
            </div>
        </div>

        <button
            class="w-full mt-10 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-sm tracking-widest uppercase shadow-lg shadow-indigo-100 hover:shadow-indigo-200 hover:-translate-y-0.5 transition-all">
            Generate Report
        </button>
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
                                                'rgba(79, 70, 229, 0.1)',
                                                'rgba(79, 70, 229, 0)'
                                            ];
                                            if (!context.chart.chartArea) return;
                                            const { ctx, data, chartArea: { top, bottom } } = context.chart;
                                            const gradient = ctx.createLinearGradient(0, top, 0, bottom);
                                            gradient.addColorStop(0, bgColor[0]);
                                            gradient.addColorStop(1, bgColor[1]);
                                            return gradient;
                                        },
                                            pointBackgroundColor: '#fff',
                                                pointBorderColor: '#4F46E5',
                                                    pointHoverRadius: 8,
                                                        pointRadius: 6,
                                                            pointBorderWidth: 4,
            }]
        },
    options: {
        responsive: true,
            maintainAspectRatio: false,
                plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: false,
                    min: 60,
                        max: 100,
                            grid: { color: '#f1f5f9' },
                ticks: { font: { family: 'Inter', weight: 'bold' }, color: '#94a3b8' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: 'Inter', weight: 'bold' }, color: '#94a3b8' }
            }
        }
    }
    });
</script>

<?php require_once 'includes/footer.php'; ?>