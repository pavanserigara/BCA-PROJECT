<?php
$page_title = "My Academic Profile";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Fetch Student detailed info
$stmt = $pdo->prepare("SELECT u.*, s.*, c.name as course_name, d.name as department_name 
                       FROM users u 
                       JOIN students s ON u.id = s.user_id 
                       JOIN courses c ON s.course_id = c.id 
                       JOIN departments d ON c.dept_id = d.id
                       WHERE u.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Attendance Status
$stmt_att = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present
    FROM attendance WHERE student_id = ?");
$stmt_att->execute([$student_id]);
$att_stats = $stmt_att->fetch();
$att_percent = $att_stats['total'] > 0 ? round(($att_stats['present'] / $att_stats['total']) * 100) : 0;
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Institutional Identity
            </h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Review your academic credentials and
                personal record profile.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-indigo-600 px-8 py-4 rounded-[2.5rem] text-white shadow-xl shadow-indigo-100 flex items-center space-x-6">
                <div class="text-right">
                    <p
                        class="text-[10px] font-black text-indigo-300 uppercase tracking-widest leading-none mb-1 leading-none italic">
                        Portal Status</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">Active Academic</p>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-indigo-400 text-xl">
                    ID</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Sidebar Info -->
        <div class="space-y-12">
            <div class="bg-slate-900 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden group">
                <div class="relative z-10 text-center">
                    <div class="w-40 h-40 rounded-[3.5rem] mx-auto mb-10 overflow-hidden ring-4 ring-indigo-500/30 p-2">
                        <img src="../assets/images/<?php echo $student['profile_pic']; ?>"
                            class="w-full h-full object-cover rounded-[3rem]" alt="Profile">
                    </div>
                    <h4 class="text-2xl font-black text-white italic tracking-tight mb-2 uppercase">
                        <?php echo $student['full_name']; ?>
                    </h4>
                    <p
                        class="text-[11px] font-black text-indigo-400 uppercase tracking-widest mb-12 italic tracking-[0.2em] opacity-80">
                        <?php echo $student['roll_no']; ?>
                    </p>

                    <div class="bg-white/5 border border-white/10 rounded-[2.5rem] p-8">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6 italic">Current
                            Phase Attendance</p>
                        <h3 class="text-5xl font-black text-white tracking-tighter mb-4 italic leading-none">
                            <?php echo $att_percent; ?>%
                        </h3>
                        <div class="w-full bg-white/10 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000"
                                style="width: <?php echo $att_percent; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
                <h4
                    class="text-xl font-black text-slate-800 tracking-tight italic mb-10 border-b border-indigo-50 pb-6 leading-none">
                    Security Center</h4>
                <div class="space-y-6">
                    <button
                        class="w-full py-4.5 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all text-xs font-black uppercase tracking-widest italic flex items-center justify-center space-x-3">
                        <i class="fas fa-key text-[10px]"></i>
                        <span>Rotate Password</span>
                    </button>
                    <button
                        class="w-full py-4.5 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 hover:text-rose-500 hover:border-rose-500 transition-all text-xs font-black uppercase tracking-widest italic flex items-center justify-center space-x-3">
                        <i class="fas fa-id-card text-[10px]"></i>
                        <span>Request Digital ID</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                    <span
                        class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xs italic">D</span>
                    <span>Identity Profile Registry</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-15">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Preferred
                            Mailing Hub</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic">
                            <?php echo $student['email']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Coordinating
                            Phone</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic">
                            <?php echo $student['phone'] ?: 'N/A'; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Academic
                            Curriculum</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic uppercase">
                            <?php echo $student['course_name']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Major
                            Department</label>
                        <p class="text-xl font-black text-indigo-600 tracking-tight italic uppercase">
                            <?php echo $student['department_name']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Gender
                            Selection</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic uppercase">
                            <?php echo $student['gender']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Academic
                            Semester</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic uppercase">Cycle
                            <?php echo $student['semester']; ?>
                        </p>
                    </div>
                </div>

                <div class="mt-15 p-12 bg-slate-50 rounded-[3rem] border border-slate-50 italic">
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic leading-none opacity-50">Permanent
                        Address Residence</label>
                    <p class="text-lg font-bold text-slate-600 leading-relaxed">
                        <?php echo $student['address'] ?: 'Profile address not assigned yet.'; ?>
                    </p>
                </div>
            </div>

            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                    <span
                        class="w-10 h-10 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 text-xs italic">F</span>
                    <span>Institutional Treasury Flow</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        class="p-8 bg-slate-50 border border-slate-100 rounded-[2.5rem] group hover:bg-white hover:border-indigo-100/50 transition-all duration-300">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-6 italic">Total
                            Tuition</p>
                        <p class="text-2xl font-black text-slate-800 tracking-tighter italic">₹85,500</p>
                    </div>
                    <div
                        class="p-8 bg-slate-50 border border-slate-100 rounded-[2.5rem] group hover:bg-white hover:border-emerald-100/50 transition-all duration-300">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-6 italic">Paid Flow
                        </p>
                        <p class="text-2xl font-black text-emerald-500 tracking-tighter italic">₹85,500</p>
                    </div>
                    <div class="p-8 bg-emerald-50 border border-emerald-100 rounded-[2.5rem] relative overflow-hidden">
                        <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-6 italic">Balance
                            Due</p>
                        <p class="text-2xl font-black text-emerald-800 tracking-tighter italic">₹0.00</p>
                        <div class="absolute top-0 right-0 p-6">
                            <i class="fas fa-check-double text-emerald-200 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>