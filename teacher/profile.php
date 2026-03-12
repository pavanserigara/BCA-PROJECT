<?php
$page_title = "Academic Faculty Profile";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];

// Fetch Faculty detailed info
$stmt = $pdo->prepare("SELECT u.*, t.*, d.name as department_name 
                       FROM users u 
                       JOIN teachers t ON u.id = t.user_id 
                       JOIN departments d ON t.dept_id = d.id
                       WHERE u.id = ?");
$stmt->execute([$teacher_id]);
$faculty = $stmt->fetch();
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Professional Identity</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Review your academic credentials and
                professional load profile.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-rose-600 px-8 py-5 rounded-[2.5rem] text-white shadow-2xl shadow-rose-100 flex items-center space-x-6 animate__animated animate__fadeInRight">
                <div class="text-right">
                    <p
                        class="text-[10px] font-black text-rose-200 uppercase tracking-widest leading-none mb-1 leading-none italic">
                        Duty Status</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">In Duty - Faculty</p>
                </div>
                <div
                    class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-rose-400 text-xl">
                    FT</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Faculty Sidebar -->
        <div class="space-y-12">
            <div class="bg-slate-900 p-15 rounded-[4rem] shadow-2xl relative overflow-hidden group">
                <div class="relative z-10 text-center">
                    <div
                        class="w-44 h-44 rounded-[4rem] mx-auto mb-10 overflow-hidden ring-4 ring-rose-500/20 p-2 shadow-2xl">
                        <img src="../assets/images/<?php echo $faculty['profile_pic']; ?>"
                            class="w-full h-full object-cover rounded-[3.5rem]" alt="Profile">
                    </div>
                    <h4 class="text-2xl font-black text-white italic tracking-tight mb-2 uppercase">
                        <?php echo $faculty['full_name']; ?>
                    </h4>
                    <p
                        class="text-[11px] font-black text-rose-400 uppercase tracking-widest mb-12 italic tracking-[0.2em] opacity-80">
                        <?php echo $faculty['designation']; ?>
                    </p>

                    <div class="grid grid-cols-2 gap-6 pb-10 border-t border-slate-800 pt-10">
                        <div class="text-center">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 italic">Duty
                                Hours</p>
                            <p class="text-xl font-black text-white italic">18 / wk</p>
                        </div>
                        <div class="text-center">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 italic">
                                Faculty Batch</p>
                            <p class="text-xl font-black text-white italic">Sr. 2024</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden">
                <h4
                    class="text-xl font-black text-slate-800 tracking-tight italic mb-8 border-b border-indigo-50 pb-6 leading-none">
                    Institutional Security</h4>
                <div class="space-y-6">
                    <button
                        class="w-full py-4.5 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all text-xs font-black uppercase tracking-widest italic flex items-center justify-center space-x-3">
                        <i class="fas fa-lock text-[10px]"></i>
                        <span>Rotate Password</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Workspace Info -->
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden">
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                    <span
                        class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xs italic">C</span>
                    <span>Professional Registry</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-15 mb-15">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Preferred
                            Mailing Hub</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic">
                            <?php echo $faculty['email']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Coordinating
                            Phone</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic">
                            <?php echo $faculty['phone'] ?: 'N/A'; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Employee
                            ID Code</label>
                        <p class="text-xl font-black text-rose-500 tracking-tight italic uppercase">
                            <?php echo $faculty['employee_id']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Major
                            Department</label>
                        <p class="text-xl font-black text-indigo-600 tracking-tight italic uppercase">
                            <?php echo $faculty['department_name']; ?>
                        </p>
                    </div>
                </div>

                <div class="p-12 bg-slate-50 rounded-[3rem] border border-slate-50 italic">
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic leading-none opacity-50">Permanent
                        Home Address Profile</label>
                    <p class="text-lg font-bold text-slate-600 leading-relaxed">
                        <?php echo $faculty['address'] ?: 'Academic residence profile not logged.'; ?>
                    </p>
                </div>
            </div>

            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30 transition-all duration-300">
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                    <span
                        class="w-10 h-10 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 text-xs italic">Q</span>
                    <span>Qualifications Summary Index</span>
                </h4>

                <div class="space-y-10">
                    <div class="p-10 bg-slate-50/50 border border-slate-100 rounded-[3rem] relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
                        <p
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic leading-none">
                            Primary Credential Level</p>
                        <h4
                            class="text-2xl font-black text-slate-800 tracking-tight italic leading-tight uppercase mb-4">
                            <?php echo $faculty['qualification'] ?: 'Ph.D. in Computer Science'; ?>
                        </h4>
                        <p class="text-sm font-medium text-slate-500 italic leading-loose shadow-none">Research Focus:
                            Distributed Cloud Infrastructure and Autonomous Learning Models. 8+ Publications in
                            International Journals of Computing Technology.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>