<?php
$page_title = "Institutional Faculty Notices";
require_once 'includes/header.php';

$teacher_role = 'teachers';
$notices = $pdo->prepare("SELECT n.*, u.full_name as author 
                          FROM notices n 
                          JOIN users u ON n.posted_by = u.id 
                          WHERE n.role_target IN ('all', ?) 
                          ORDER BY n.created_at DESC");
$notices->execute([$teacher_role]);
$all_notices = $notices->fetchAll();
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Faculty Notice Board</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Official institutional announcements and
                professional broadcasts.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-rose-600 px-8 py-5 rounded-[2.5rem] text-white shadow-2xl shadow-rose-100 flex items-center space-x-6">
                <div class="text-right">
                    <p
                        class="text-[10px] font-black text-rose-200 uppercase tracking-widest leading-none mb-1 leading-none italic">
                        Active Stream</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">
                        <?php echo count($all_notices); ?> Broadcasts
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-rose-400 text-xl">
                    N</div>
            </div>
        </div>
    </div>

    <!-- Notices Grid -->
    <div class="space-y-12">
        <?php if (empty($all_notices)): ?>
            <div
                class="bg-white p-20 rounded-[4rem] text-center border border-indigo-50 shadow-sm animate__animated animate__fadeIn">
                <div
                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                    <i class="fas fa-bullhorn text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 italic">Board is Silent</h3>
                <p class="text-slate-500 mt-2">No active announcements for your profile at this moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($all_notices as $n): ?>
                <div
                    class="bg-white p-15 rounded-[4rem] shadow-sm border border-rose-100/30 group hover:shadow-2xl transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-rose-600/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-all">
                    </div>

                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-10 mb-10 relative z-10">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-6 leading-none">
                                <span
                                    class="text-[10px] font-black text-rose-500 bg-rose-50/50 px-4 py-2 rounded-xl border border-rose-100 italic">Professional
                                    Announcement</span>
                                <span
                                    class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic font-black">•
                                    <?php echo date('M d, Y', strtotime($n['created_at'])); ?>
                                </span>
                            </div>
                            <h3
                                class="text-3xl font-black text-slate-800 tracking-tight leading-tight group-hover:text-rose-600 transition-colors uppercase italic mb-6">
                                <?php echo $n['title']; ?>
                            </h3>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-3 justify-end mb-2">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-slate-900 flex items-center justify-center text-white font-black italic shadow-lg text-[10px]">
                                    A</div>
                                <p class="text-sm font-black text-slate-800 italic">
                                    <?php echo $n['author']; ?>
                                </p>
                            </div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic leading-none">
                                Institutional Admin Personnel</p>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50/50 p-12 rounded-[3rem] border border-slate-50 text-slate-600 text-xl leading-loose italic relative overflow-hidden group/content shadow-inner">
                        <?php echo nl2br($n['content']); ?>
                    </div>

                    <div class="mt-12 flex items-center justify-between pt-8 border-t border-slate-50">
                        <div class="flex items-center space-x-4">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic font-black">Ref:
                                #NOTICE-
                                <?php echo str_pad($n['id'], 3, '0', STR_PAD_LEFT); ?>
                            </span>
                        </div>
                        <button
                            class="bg-rose-600 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 transition-all transform active:scale-95 italic font-black shadow-xl shadow-rose-100">
                            Acknowledge Announcement
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../admin/includes/header.php'; ?>