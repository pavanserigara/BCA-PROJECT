<?php
$page_title = "Notices";
require_once 'includes/header.php';

$teacher_role = 'teachers';
$notices = $pdo->prepare("SELECT n.*, u.full_name as author 
                          FROM notices n 
                          JOIN users u ON n.posted_by = u.id 
                          LEFT JOIN teachers t ON t.user_id = ?
                          WHERE n.role_target IN ('all', ?) 
                          AND (n.department_id IS NULL OR n.department_id = t.dept_id)
                          ORDER BY n.created_at DESC");
$notices->execute([$_SESSION['user_id'], $teacher_role]);
$all_notices = $notices->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Notice Board</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Official institutional announcements and professional broadcasts.</p>
    </div>
    <div class="bg-indigo-50 dark:bg-indigo-500/10 px-6 py-3 rounded-2xl border border-indigo-100 dark:border-indigo-500/20">
        <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest leading-none mb-1">Active Notices</p>
        <p class="text-lg font-black text-slate-800 dark:text-white"><?php echo count($all_notices); ?> Updates</p>
    </div>
</div>

<div class="space-y-6 pb-10">
    <?php if (empty($all_notices)): ?>
        <div class="py-20 bg-white dark:bg-slate-800 rounded-[2.5rem] text-center border border-dashed border-slate-200 dark:border-slate-700">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="fas fa-bullhorn text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Board is Silent</h3>
            <p class="text-sm text-slate-500 mt-1">No active announcements for your profile at this moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($all_notices as $n): ?>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700 group hover:border-primary-500 transition-all duration-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <span class="text-[9px] font-bold text-primary-600 bg-primary-50 dark:bg-primary-500/10 px-2.5 py-1 rounded-lg border border-primary-100 dark:border-primary-500/20 uppercase tracking-widest">Official</span>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Posted on <?php echo date('M d, Y', strtotime($n['created_at'])); ?></span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-900 dark:bg-slate-700 flex items-center justify-center text-white text-[10px] font-bold">A</div>
                        <p class="text-xs font-bold text-slate-700 dark:text-white"><?php echo $n['author']; ?></p>
                    </div>
                </div>

                <h3 class="text-xl font-extrabold text-slate-800 dark:text-white leading-tight mb-4 group-hover:text-primary-600 transition-colors uppercase">
                    <?php echo $n['title']; ?>
                </h3>

                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-6 border border-slate-50 dark:border-slate-700">
                    <?php echo nl2br($n['content']); ?>
                    <?php if ($n['attachment']): ?>
                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-primary-600 shadow-sm border border-slate-100 dark:border-slate-700">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">Resource Attachment</p>
                                    <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500">Official Document</p>
                                </div>
                            </div>
                            <a href="../assets/attachments/notices/<?php echo $n['attachment']; ?>" download class="px-5 py-2 bg-white dark:bg-slate-800 text-primary-600 border border-slate-200 dark:border-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary-600 hover:text-white transition-all shadow-sm">Download</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-slate-50 dark:border-slate-700">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ref: #NOTICE-<?php echo str_pad($n['id'], 3, '0', STR_PAD_LEFT); ?></span>
                    <button class="px-6 py-2.5 bg-primary-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-primary-700 transition-all transform active:scale-95 shadow-soft">Acknowledge</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>