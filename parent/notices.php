<?php
$page_title = "Broadcast Center";
require_once 'includes/header.php';

$student_id = $_SESSION['linked_student_id'];
$parent_role = 'all'; // Parents see 'all' notices, and maybe we can add a 'parents' role later

// Fetch student's course/dept for filtered notices
$stmt_std = $pdo->prepare("SELECT c.dept_id FROM students s JOIN courses c ON s.course_id = c.id WHERE s.user_id = ?");
$stmt_std->execute([$student_id]);
$std_info = $stmt_std->fetch();
$dept_id = $std_info['dept_id'] ?? 0;

$notices = $pdo->prepare("SELECT n.*, u.full_name as author 
                          FROM notices n 
                          JOIN users u ON n.posted_by = u.id 
                          WHERE n.role_target IN ('all', 'students', 'parents') 
                          AND (n.department_id IS NULL OR n.department_id = ?)
                          ORDER BY n.created_at DESC");
$notices->execute([$dept_id]);
$all_notices = $notices->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Institutional Broadcasts</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Official announcements and academic circulars regarding <?php echo $_SESSION['linked_student_name']; ?>.</p>
    </div>
    
    <div class="bg-emerald-600 px-8 py-4 rounded-[2rem] text-white shadow-premium flex items-center space-x-4 relative overflow-hidden group hover:scale-[1.02] transition-all">
        <div class="relative z-10 text-right">
            <p class="text-[10px] font-black text-emerald-200 uppercase tracking-widest leading-none mb-1">Active Stream</p>
            <p class="text-lg font-black text-white tracking-tight italic"><?php echo count($all_notices); ?> Updates</p>
        </div>
        <i class="fas fa-bullhorn text-5xl absolute -right-4 -bottom-4 opacity-10 group-hover:rotate-12 transition-transform"></i>
    </div>
</div>

<div class="space-y-10 mb-20 pb-10">
    <?php if (empty($all_notices)): ?>
        <div class="bg-white dark:bg-slate-800 p-20 rounded-[4rem] text-center border-2 border-dashed border-slate-100 dark:border-slate-700 shadow-premium">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                <i class="fas fa-satellite-dish text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white italic uppercase tracking-widest">Board is Silent</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium italic">No active circulars found for this academic cycle.</p>
        </div>
    <?php else: ?>
        <?php foreach ($all_notices as $n): ?>
            <div class="bg-white dark:bg-slate-800 p-10 lg:p-14 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50 hover:border-emerald-500 transition-all duration-500 relative overflow-hidden group">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-10 mb-10 relative z-10">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3 mb-6">
                            <span class="px-3 py-1 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-lg shadow-emerald-500/20">Official Circular</span>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">• <?php echo date('M d, Y', strtotime($n['created_at'])); ?></span>
                        </div>
                        <h3 class="text-3xl lg:text-4xl font-black text-slate-800 dark:text-white tracking-tight leading-tight group-hover:text-emerald-600 transition-colors uppercase italic mb-8"><?php echo $n['title']; ?></h3>
                    </div>
                    
                    <div class="flex items-center space-x-4 bg-slate-50 dark:bg-slate-900/50 p-4 rounded-3xl border border-slate-100 dark:border-slate-800 self-start">
                        <div class="w-12 h-12 rounded-2xl bg-slate-950 flex items-center justify-center text-white ring-4 ring-white dark:ring-slate-800 shadow-xl font-black italic">
                            <?php echo strtoupper(substr($n['author'], 0, 1)); ?>
                        </div>
                        <div class="pr-4">
                            <p class="text-xs font-black text-slate-800 dark:text-white italic mb-1"><?php echo $n['author']; ?></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Admin Personnel</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#F8FAFC] dark:bg-slate-900/50 p-10 rounded-[2.5rem] border border-slate-50 dark:border-slate-800/50 text-slate-600 dark:text-slate-400 text-lg leading-relaxed italic relative shadow-inner">
                    <?php echo nl2br($n['content']); ?>
                    <?php if ($n['attachment']): ?>
                        <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-emerald-600 shadow-premium border border-slate-50 dark:border-slate-800">
                                    <i class="fas fa-file-pdf text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-800 dark:text-white italic">Attached Resource</p>
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mt-1">Official Reference</p>
                                </div>
                            </div>
                            <a href="../assets/attachments/notices/<?php echo $n['attachment']; ?>" download class="px-8 py-3 bg-white dark:bg-slate-800 text-emerald-600 border-2 border-emerald-600/10 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-premium">Save File</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-10 flex flex-wrap items-center justify-between gap-6 pt-6 border-t border-slate-50 dark:border-slate-800/50">
                    <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-widest italic">
                        <i class="fas fa-fingerprint"></i>
                        <span>Ref #NS-0<?php echo $n['id']; ?>-2026</span>
                    </div>
                    <?php if ($n['attachment']): ?>
                        <a href="../assets/attachments/notices/<?php echo $n['attachment']; ?>" download class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all transform active:scale-95 shadow-lg shadow-emerald-500/20">
                            <i class="fas fa-file-arrow-down mr-2"></i>Download Resource
                        </a>
                    <?php else: ?>
                        <button class="bg-slate-100 dark:bg-slate-900 text-slate-400 px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest cursor-not-allowed opacity-50">
                            <i class="fas fa-circle-info mr-2"></i>Information Only
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
