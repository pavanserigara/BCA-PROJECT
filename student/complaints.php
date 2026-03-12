<?php
$page_title = "Lodge Grievance";
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_POST['submit_complaint'])) {
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO complaints (user_id, subject, message, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->execute([$user_id, $subject, $message]);
        $success_message = "Your grievance has been logged. Support will review it shortly.";
    } catch (PDOException $e) {
        $error_message = "Submission failed: " . $e->getMessage();
    }
}

$my_complaints = $pdo->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
$my_complaints->execute([$_SESSION['user_id']]);
$complaints = $my_complaints->fetchAll();
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Help Desk & Grievance</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Direct channel to institutional
                administration for support and feedback.</p>
        </div>

        <button onclick="document.getElementById('complaint_modal').classList.remove('hidden')"
            class="bg-rose-600 hover:bg-rose-700 text-white px-10 py-4.5 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-rose-100 transition-all hover:-translate-y-1 transform active:scale-95 flex items-center space-x-3">
            <i class="fas fa-bullhorn text-sm"></i>
            <span>Log New Ticket</span>
        </button>
    </div>

    <?php if ($success_message): ?>
        <div
            class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-8 rounded-[2.5rem] mb-12 flex items-center animate__animated animate__fadeInDown">
            <i class="fas fa-check-circle text-2xl mr-6"></i>
            <p class="text-base font-bold">
                <?php echo $success_message; ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="space-y-10">
        <?php if (empty($complaints)): ?>
            <div class="bg-white p-20 rounded-[3rem] text-center border border-indigo-50 shadow-sm">
                <div
                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                    <i class="fas fa-shield-heart text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800">No Active Tickets</h3>
                <p class="text-slate-500 mt-2">Your history is clear. Use the button above to log a new grievance.</p>
            </div>
        <?php else: ?>
            <?php foreach ($complaints as $c): ?>
                <div
                    class="bg-white p-12 rounded-[3.5rem] shadow-sm border border-indigo-100/30 group hover:shadow-2xl transition-all duration-300">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-8 mb-10">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-4">
                                <span
                                    class="px-3 py-1.5 <?php echo $c['status'] == 'Pending' ? 'bg-rose-50 text-rose-500 border-rose-100' : 'bg-emerald-100 text-emerald-700 border-emerald-200'; ?> border rounded-lg text-[9px] font-black uppercase tracking-widest">
                                    <?php echo $c['status']; ?>
                                </span>
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Ref: #
                                    <?php echo str_pad($c['id'], 5, '0', STR_PAD_LEFT); ?>
                                </span>
                            </div>
                            <h3
                                class="text-2xl font-black text-slate-800 tracking-tight leading-tight group-hover:text-indigo-600 transition-colors mb-4">
                                <?php echo $c['subject']; ?>
                            </h3>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 leading-none italic">
                                Submission Date</p>
                            <p class="text-sm font-bold text-slate-500">
                                <?php echo date('M d, Y', strtotime($c['created_at'])); ?>
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50/50 p-10 rounded-[2.5rem] border border-slate-50 text-slate-600 italic leading-loose mb-10">
                        <?php echo nl2br($c['message']); ?>
                    </div>

                    <?php if ($c['admin_reply']): ?>
                        <div class="pt-10 border-t border-indigo-50 flex flex-col md:flex-row gap-10">
                            <div class="w-20 hidden md:block">
                                <div
                                    class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-black italic shadow-lg shadow-indigo-100">
                                    A</div>
                            </div>
                            <div
                                class="flex-1 bg-indigo-50/50 p-10 rounded-[2.5rem] border border-indigo-100 relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                                <p
                                    class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-4 italic leading-none">
                                    Institutional Resolution</p>
                                <p class="text-slate-800 font-medium leading-relaxed italic">
                                    <?php echo nl2br($c['admin_reply']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="complaint_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-12 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">Express Your Grievance</h3>
            <button onclick="document.getElementById('complaint_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 bg-slate-50 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="complaints.php" method="POST" class="space-y-8">
            <input type="hidden" name="submit_complaint" value="1">

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Subject
                    Profile *</label>
                <input type="text" name="subject" required placeholder="e.g. Laboratory Equipment Malfunction"
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
            </div>

            <div>
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Detailed
                    Message *</label>
                <textarea name="message" rows="6" required
                    placeholder="Describe the issue in detail for our support team..."
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white transition-all outline-none font-medium text-slate-800"></textarea>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button type="button" onclick="document.getElementById('complaint_modal').classList.add('hidden')"
                    class="flex-1 py-5 bg-slate-50 text-slate-500 font-black rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs">Wait
                    / Back</button>
                <button type="submit"
                    class="flex-1 py-5 bg-rose-600 text-white font-black rounded-2xl shadow-xl shadow-rose-100 hover:bg-rose-700 transition-all uppercase tracking-widest text-xs">
                    Confirm Submission
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>