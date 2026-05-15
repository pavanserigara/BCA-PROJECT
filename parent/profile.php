<?php
$page_title = "Identity Portfolio";
require_once 'includes/header.php';

$parent_id = $_SESSION['user_id'];
$student_id = $_SESSION['linked_student_id'];

// Fetch Parent info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$parent_id]);
$parent = $stmt->fetch();

// Fetch Parent link info
$stmt = $pdo->prepare("SELECT * FROM parents WHERE user_id = ?");
$stmt->execute([$parent_id]);
$parent_link = $stmt->fetch();

// Fetch Student info
$stmt = $pdo->prepare("SELECT u.*, s.*, c.name as course_name 
                       FROM users u 
                       JOIN students s ON u.id = s.user_id 
                       JOIN courses c ON s.course_id = c.id
                       WHERE u.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();
?>

<div class="mb-10">
    <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Institutional Identity</h2>
    <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Verified guardian credentials and linked student telemetry.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-20">
    <!-- Parent Card -->
    <div class="lg:col-span-1">
        <div class="bg-slate-950 p-10 rounded-[4rem] text-white shadow-premium relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full group-hover:scale-110 transition-transform"></div>
            
            <div class="relative z-10">
                <div class="w-32 h-32 bg-white/5 rounded-[2.5rem] border border-white/10 flex items-center justify-center text-emerald-400 font-black text-4xl italic mb-10 shadow-2xl">
                    <?php echo substr($parent['full_name'], 0, 1); ?>
                </div>
                
                <h3 class="text-2xl font-black italic uppercase tracking-tight mb-2"><?php echo $parent['full_name']; ?></h3>
                <p class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.3em] opacity-80 mb-10">Guardian Profile</p>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-1">Mailing Hub</p>
                        <p class="text-sm font-bold italic opacity-90"><?php echo $parent['email']; ?></p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest mb-1">Relation Protocol</p>
                        <p class="text-sm font-bold italic opacity-90"><?php echo $parent_link['relation']; ?></p>
                    </div>
                </div>
            </div>
            <i class="fas fa-fingerprint absolute top-10 right-10 text-5xl opacity-5"></i>
        </div>
    </div>

    <!-- Student & Operations -->
    <div class="lg:col-span-2 space-y-10">
        <!-- Student Summary -->
        <div class="bg-white dark:bg-slate-800 p-12 rounded-[4rem] shadow-premium border border-slate-100 dark:border-slate-800 relative overflow-hidden group/student">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/5 rounded-full group-hover/student:scale-110 transition-transform duration-1000"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
                <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase italic tracking-tight">Linked Academic Entity</h4>
                <span class="px-5 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border border-emerald-100 dark:border-emerald-500/20 rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-soft">Status: Active Synchronized</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="flex items-center space-x-6">
                    <?php 
                        $stu_pic_url = '../assets/images/default_profile.svg';
                        if (!empty($student['profile_pic'])) {
                            if (is_file(__DIR__ . '/../../uploads/profiles/' . $student['profile_pic'])) {
                                $stu_pic_url = '../uploads/profiles/' . $student['profile_pic'];
                            } elseif (is_file(__DIR__ . '/../../assets/images/' . $student['profile_pic'])) {
                                $stu_pic_url = '../assets/images/' . $student['profile_pic'];
                            }
                        }
                    ?>
                    <img src="<?php echo $stu_pic_url; ?>" class="w-20 h-20 rounded-3xl object-cover border-4 border-slate-50 dark:border-slate-900 shadow-soft" alt="">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Full Name</p>
                        <p class="text-lg font-black text-slate-800 dark:text-white italic uppercase"><?php echo $student['full_name']; ?></p>
                    </div>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Identity Code</p>
                    <p class="text-lg font-black text-indigo-600 italic uppercase"><?php echo $student['roll_no']; ?></p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Institutional Course</p>
                    <p class="text-md font-black text-slate-700 dark:text-slate-300 italic uppercase"><?php echo $student['course_name']; ?></p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-1">Semester Cycle</p>
                    <p class="text-md font-black text-slate-700 dark:text-slate-300 italic uppercase">Phase <?php echo $student['semester']; ?></p>
                </div>
            </div>
        </div>

        <!-- Security / Settings -->
        <div class="bg-white dark:bg-slate-800 p-12 rounded-[4rem] shadow-premium border border-slate-100 dark:border-slate-800">
            <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase italic mb-10 tracking-tight">Security Protocol</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <button class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:bg-emerald-600 hover:text-white transition-all shadow-soft">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 shadow-sm group-hover:bg-white/20 group-hover:text-white">
                            <i class="fas fa-key text-sm"></i>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-widest italic">Update Access Key</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </button>
                <button class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 flex items-center justify-between group hover:bg-emerald-600 hover:text-white transition-all shadow-soft">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 shadow-sm group-hover:bg-white/20 group-hover:text-white">
                            <i class="fas fa-envelope-circle-check text-sm"></i>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-widest italic">Verify Mailing Hub</span>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
