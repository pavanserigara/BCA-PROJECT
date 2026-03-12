<?php
$page_title = "Institutional Events Calendar";
require_once 'includes/header.php';

$events = $pdo->query("SELECT e.*, u.full_name as author FROM events e JOIN users u ON e.posted_by = u.id ORDER BY e.event_date ASC")->fetchAll();
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Cultural & Academic
                Calendar</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Discover and participate in upcoming
                festivals, workshops, and institutional meetings.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-amber-600 px-8 py-4 rounded-[2rem] text-white shadow-xl shadow-amber-100 flex items-center space-x-4">
                <div class="text-right">
                    <p
                        class="text-[10px] font-black text-amber-200 uppercase tracking-widest leading-none mb-1 leading-none italic">
                        Active Schedule</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">
                        <?php echo count($events); ?> Programs
                    </p>
                </div>
                <div
                    class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white font-black italic shadow-lg shadow-amber-400">
                    EV</div>
            </div>
        </div>
    </div>

    <!-- Event Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php if (empty($events)): ?>
            <div
                class="lg:col-span-3 py-40 text-center bg-white rounded-[4rem] border border-indigo-50 shadow-sm animate__animated animate__fadeIn">
                <div
                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                    <i class="fas fa-calendar-day text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 italic">No Programs Scheduled</h3>
                <p class="text-slate-500 mt-2">Institutional calendar is currently stationary. Check back for upcoming
                    updates.</p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $e):
                $is_upcoming = strtotime($e['event_date']) >= time();
                ?>
                <div
                    class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 group hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
                    <?php if ($is_upcoming): ?>
                        <div
                            class="absolute top-10 right-10 flex items-center space-x-2 text-emerald-500 font-black text-[9px] uppercase tracking-widest italic font-black">
                            <i class="fas fa-bolt text-xs mr-2 border border-emerald-500/30 rounded-full p-1 leading-none"></i>
                            <span>Upcoming Program</span>
                        </div>
                    <?php else: ?>
                        <div
                            class="absolute top-10 right-10 flex items-center space-x-2 text-slate-300 font-black text-[9px] uppercase tracking-widest italic font-black">
                            <i class="fas fa-history text-xs mr-2 border border-slate-300/30 rounded-full p-1 leading-none"></i>
                            <span>Historical Flow</span>
                        </div>
                    <?php endif; ?>

                    <div>
                        <div
                            class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all transform group-hover:rotate-6 mb-10 shadow-sm">
                            <i class="fas fa-masks-theater text-xl"></i>
                        </div>

                        <div class="flex items-center space-x-4 mb-6">
                            <span
                                class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 italic">
                                <?php echo date('M d, Y', strtotime($e['event_date'])); ?>
                            </span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none italic">@
                                <?php echo $e['location']; ?>
                            </span>
                        </div>

                        <h3
                            class="text-2xl font-black text-slate-800 leading-tight mb-6 group-hover:text-indigo-600 transition-colors italic">
                            <?php echo $e['title']; ?>
                        </h3>
                        <div
                            class="text-sm text-slate-500 leading-relaxed italic line-clamp-4 bg-slate-50/50 p-6 rounded-3xl border border-slate-50">
                            <?php echo nl2br($e['description']); ?>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-900 border border-white flex items-center justify-center text-[8px] font-black text-white italic">
                                A</div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">Posted by
                                Institutional Admin</p>
                        </div>
                        <button
                            class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-black text-[9px] uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all transform active:scale-95 italic">
                            Join Event Desk
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>