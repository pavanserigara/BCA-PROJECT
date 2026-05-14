<?php
$page_title = "Institutional Identity";
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
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present
    FROM attendance WHERE student_id = ?");
$stmt_att->execute([$student_id]);
$att_stats = $stmt_att->fetch();
$att_percent = $att_stats['total'] > 0 ? round(($att_stats['present'] / $att_stats['total']) * 100) : 0;
?>

<div class="mb-10 lg:mb-16">
    <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight uppercase italic">Digital Registry Profile</h2>
    <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Your verified academic identity and institutional credentials.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Profile Sidebar -->
    <div class="space-y-8">
        <div class="bg-slate-950 dark:bg-slate-800 p-10 rounded-[3rem] shadow-premium relative overflow-hidden group">
            <div class="relative z-10">
                <div class="relative w-40 h-40 mx-auto mb-10 group/img">
                    <div class="absolute -inset-2 bg-gradient-to-tr from-primary-600 to-indigo-600 rounded-[3.5rem] opacity-20 blur group-hover/img:opacity-40 transition-all"></div>
                    <?php 
                        $pic_path = !empty($student['profile_pic']) ? "../uploads/profiles/" . $student['profile_pic'] : "../assets/images/default_profile.svg";
                    ?>
                    <img id="profile_display" src="<?php echo $pic_path; ?>" class="relative w-full h-full object-cover rounded-[3rem] border-4 border-white/10 shadow-2xl" alt="Profile pic">
                    
                    <label for="profile_input" class="absolute -bottom-2 -right-2 w-12 h-12 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center text-primary-600 shadow-xl border border-slate-100 dark:border-slate-800 hover:scale-110 transition-transform cursor-pointer">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="profile_input" class="hidden" accept="image/*">
                </div>

                <div class="text-center space-y-2 mb-10">
                    <h3 class="text-2xl font-black text-white italic truncate uppercase tracking-tight"><?php echo $student['full_name']; ?></h3>
                    <p class="text-[10px] font-black text-primary-400 uppercase tracking-[0.3em] opacity-80"><?php echo $student['roll_no']; ?></p>
                </div>

                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-[2.5rem] p-8 text-center">
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-4 italic">Academic Standing</p>
                    <div class="relative inline-flex items-center justify-center mb-4">
                        <svg class="w-24 h-24 transform -rotate-90">
                            <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                            <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-primary-500" stroke-dasharray="<?php echo (2 * 22/7 * 40); ?>" stroke-dashoffset="<?php echo (2 * 22/7 * 40) * (1 - $att_percent/100); ?>" />
                        </svg>
                        <span class="absolute text-xl font-black text-white italic"><?php echo $att_percent; ?>%</span>
                    </div>
                    <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic leading-none">Attendance Rate</p>
                </div>
            </div>
            <i class="fas fa-id-card absolute -bottom-10 -right-10 text-9xl opacity-5 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50">
            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-8 italic">Security Operations</h4>
            <div class="space-y-4">
                <button class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl hover:bg-primary-600 hover:text-white transition-all group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shield-halved text-xs group-hover:animate-pulse"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Rotate Credentials</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </button>
                <button class="w-full flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl hover:bg-primary-600 hover:text-white transition-all group">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-at text-xs"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Sync Mailing Hub</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-10">
        <!-- Data Cards -->
        <div class="bg-white dark:bg-slate-800 p-10 lg:p-14 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50">
            <div class="flex items-center justify-between mb-12">
                <h4 class="text-xl font-black text-slate-800 dark:text-white tracking-tight uppercase italic flex items-center">
                    <i class="fas fa-database text-primary-600 mr-3 text-sm"></i>
                    Academic Registry
                </h4>
                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-black uppercase tracking-widest rounded-lg border border-emerald-100 dark:border-emerald-500/20">Verified Identity</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-12 gap-x-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none">Mailing Contact</label>
                    <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tight lowcase"><?php echo $student['email']; ?></p>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none">Coordinating Phone</label>
                    <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tight leading-none"><?php echo $student['phone'] ?: '+00 000 000 00'; ?></p>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none">Institutional Course</label>
                    <p class="text-lg font-black text-primary-600 italic tracking-tight uppercase leading-none"><?php echo $student['course_name']; ?></p>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none">Major Domain</label>
                    <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tight uppercase leading-none"><?php echo $student['department_name']; ?></p>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none">Semester Cycle</label>
                    <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tight uppercase leading-none">Phase <?php echo $student['semester']; ?></p>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic leading-none">Identity Gender</label>
                    <p class="text-lg font-black text-slate-800 dark:text-white italic tracking-tight uppercase leading-none"><?php echo $student['gender'] ?: 'Binary Undisclosed'; ?></p>
                </div>
            </div>

            <div class="mt-16 p-8 lg:p-10 bg-[#F8FAFC] dark:bg-slate-900/50 rounded-[3rem] border border-slate-50 dark:border-slate-800/50 relative overflow-hidden group/addr">
                <i class="fas fa-map-location-dot absolute -right-6 -top-6 text-7xl opacity-5 group-hover/addr:scale-110 transition-transform"></i>
                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-4 block italic">Permanent Address Hub</label>
                <p class="text-md font-bold text-slate-600 dark:text-slate-400 leading-relaxed italic pr-12"><?php echo $student['address'] ?: 'Verified residence address is yet to be synchronized with the institutional registry.'; ?></p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-10 lg:p-14 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50 flex flex-col md:flex-row items-center gap-10">
            <div class="flex-1 w-full">
                <h4 class="text-xl font-black text-slate-800 dark:text-white tracking-tight uppercase italic mb-8 flex items-center">
                    <i class="fas fa-receipt text-primary-600 mr-3 text-sm"></i>
                    Tuition Status
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-800">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 leading-none">Billable</p>
                        <p class="text-lg font-black text-slate-800 dark:text-white italic">₹85,500</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-500/10 p-6 rounded-3xl border border-emerald-100 dark:border-emerald-500/20">
                        <p class="text-[9px] font-black text-emerald-600/60 uppercase tracking-widest mb-2 leading-none">Paid Flow</p>
                        <p class="text-lg font-black text-emerald-600 italic">₹85,500</p>
                    </div>
                </div>
            </div>
            
            <div class="w-full md:w-auto self-stretch flex items-center justify-center px-10 bg-emerald-600 dark:bg-emerald-500 rounded-[2.5rem] text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden group/badge">
                 <div class="relative z-10 text-center">
                     <i class="fas fa-certificate text-3xl mb-4 group-hover/badge:rotate-12 transition-transform"></i>
                     <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 leading-none mb-1">Financial</p>
                     <p class="text-xl font-black italic uppercase leading-none">Cleared</p>
                 </div>
                 <i class="fas fa-shield-check absolute -bottom-6 -right-6 text-8xl opacity-10"></i>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>