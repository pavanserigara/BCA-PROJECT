<?php
$page_title = "Institutional Student Profile";
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch Student detailed info
$stmt = $pdo->prepare("SELECT u.*, s.*, c.name as course_name, d.name as department_name 
                       FROM users u 
                       JOIN students s ON u.id = s.user_id 
                       JOIN courses c ON s.course_id = c.id 
                       JOIN departments d ON c.dept_id = d.id
                       WHERE u.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    echo "<div class='p-20 text-center'><h3 class='text-2xl font-black text-rose-500'>Profile Not Found</h3><a href='students-list.php' class='text-indigo-600 font-bold'>Back to Directory</a></div>";
    require_once 'includes/footer.php';
    exit();
}

// Fetch Attendance Stats
$stmt_att = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present
    FROM attendance WHERE student_id = ?");
$stmt_att->execute([$id]);
$att_stats = $stmt_att->fetch();
$att_percent = $att_stats['total'] > 0 ? round(($att_stats['present'] / $att_stats['total']) * 100) : 0;
?>

<div class="flex items-center justify-between mb-15">
    <div class="flex items-center space-x-6">
        <a href="students-list.php"
            class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">
                <?php echo $student['full_name']; ?>
            </h2>
            <div class="flex items-center space-x-3 mt-3">
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">
                    <?php echo $student['roll_no']; ?>
                </span>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                    <?php echo $student['course_name']; ?> (Sem
                    <?php echo $student['semester']; ?>)
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <button
            class="bg-indigo-600 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100/50 hover:-translate-y-1 transition-all active:scale-95 flex items-center space-x-3 italic">
            <i class="fas fa-edit text-sm"></i>
            <span>Modify Record</span>
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pb-20">
    <!-- Left Column: Primary Stats -->
    <div class="space-y-12">
        <div class="bg-slate-900 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-10">
                <div
                    class="w-16 h-16 bg-white/5 border border-white/10 rounded-3xl flex items-center justify-center text-white font-black italic shadow-2xl">
                    P</div>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6 italic">Academic
                    Attendance Profile</p>
                <h3 class="text-6xl font-black text-white tracking-tighter mb-4 italic leading-none">
                    <?php echo $att_percent; ?>%
                </h3>
                <div class="w-full bg-white/10 h-2.5 rounded-full overflow-hidden mb-6">
                    <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000"
                        style="width: <?php echo $att_percent; ?>%"></div>
                </div>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">
                    <?php echo $att_stats['present']; ?> /
                    <?php echo $att_stats['total']; ?> Total Lectures Present
                </p>
            </div>
        </div>

        <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden">
            <h4
                class="text-xl font-black text-slate-800 tracking-tight italic mb-10 border-b border-indigo-50 pb-6 leading-none">
                Institutional Treasury</h4>
            <div class="space-y-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Total
                            Tuition</p>
                        <p class="text-lg font-black text-slate-800 leading-none">₹85,000</p>
                    </div>
                    <span
                        class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black italic text-[10px] leading-none">TR</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Cleared
                            Flow</p>
                        <p class="text-lg font-black text-emerald-500 leading-none">₹50,000</p>
                    </div>
                    <span
                        class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black italic text-[10px] leading-none">CL</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Remaining
                            Balance</p>
                        <p class="text-lg font-black text-rose-500 leading-none">₹35,000</p>
                    </div>
                    <span
                        class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-black italic text-[10px] leading-none">RM</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Personal & Academic Details -->
    <div class="lg:col-span-2 space-y-12">
        <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                <span
                    class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xs italic">C</span>
                <span>Primary Credentials</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Full
                        Legal Name</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo $student['full_name']; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Institutional
                        Email Flow</label>
                    <p class="text-lg font-black text-indigo-600 tracking-tight italic">
                        <?php echo $student['email']; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Administrative
                        Department</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo $student['department_name']; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Registration
                        Date</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo date('M d, Y', strtotime($student['created_at'])); ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Mobile
                        Coordination</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo $student['phone'] ?: 'Not Assigned'; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Gender
                        Profile</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic uppercase">
                        <?php echo $student['gender']; ?>
                    </p>
                </div>
            </div>

            <div class="mt-15 p-10 bg-slate-50/50 rounded-[3rem] border border-slate-50">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Residential
                    Address Profile</label>
                <p class="text-base font-bold text-slate-600 italic leading-loose">
                    <?php echo $student['address'] ?: 'Academic residence profile not logged.'; ?>
                </p>
            </div>
        </div>

        <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                <span
                    class="w-10 h-10 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-xs italic">A</span>
                <span>Academic Performance Index</span>
            </h4>

            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="py-6 px-4">Subject Name</th>
                        <th class="py-6 px-4">Internal</th>
                        <th class="py-6 px-4">Semester</th>
                        <th class="py-6 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/20">
                    <tr class="group hover:bg-slate-50 transition-all">
                        <td class="py-6 px-4">
                            <p class="text-sm font-black text-slate-800 italic uppercase">Advanced Java Programming</p>
                        </td>
                        <td class="py-6 px-4"><span class="text-sm font-bold text-slate-500">22/25</span></td>
                        <td class="py-6 px-4"><span class="text-sm font-bold text-slate-500">68/75</span></td>
                        <td class="py-6 px-4 text-right"><span
                                class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic font-black">Qualified</span>
                        </td>
                    </tr>
                    <tr class="group hover:bg-slate-50 transition-all">
                        <td class="py-6 px-4">
                            <p class="text-sm font-black text-slate-800 italic uppercase">Cloud Infrastructure</p>
                        </td>
                        <td class="py-6 px-4"><span class="text-sm font-bold text-slate-500">24/25</span></td>
                        <td class="py-6 px-4"><span class="text-sm font-bold text-slate-500">72/75</span></td>
                        <td class="py-6 px-4 text-right"><span
                                class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic font-black">Qualified</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>