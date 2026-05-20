<?php
$page_title = "Grievance Redressal";
require_once 'includes/header.php';

$success_message = '';

// Resolve Complaint
if (isset($_POST['resolve_complaint'])) {
    $complaint_id = $_POST['complaint_id'];
    $reply = sanitize($_POST['admin_reply']);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE complaints SET admin_reply = ?, status = ? WHERE id = ?");
    $stmt->execute([$reply, $status, $complaint_id]);
    $success_message = "Ticket #$complaint_id updated successfully!";
}

$complaints = $pdo->query("SELECT c.*, u.full_name as student_name, u.email as student_email FROM complaints c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC")->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Institutional Complaints Board</h2>
        <p class="text-slate-500 font-medium">Monitoring student feedback and resolving institutional grievances.</p>
    </div>
</div>

<?php if ($success_message): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 gap-6 pb-20">
    <?php if (empty($complaints)): ?>
        <div class="bg-white p-20 rounded-[3rem] text-center border border-indigo-50">
            <h3 class="text-xl font-bold text-slate-400">No Pending Grievances</h3>
        </div>
    <?php else: ?>
        <?php foreach ($complaints as $c): ?>
            <div
                class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50 group hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-300">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8">
                    <div class="flex items-start space-x-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400">
                            <i class="fas fa-ticket-alt text-2xl"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <span
                                    class="px-3 py-1.5 <?php echo $c['status'] == 'Pending' ? 'bg-rose-50 text-rose-500 border-rose-100' : ($c['status'] == 'Resolved' ? 'bg-emerald-50 text-emerald-500 border-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200'); ?> border rounded-lg text-[10px] font-black uppercase tracking-widest leading-none">
                                    <?php echo $c['status']; ?>
                                </span>
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Ticket #
                                    <?php echo $c['id']; ?>
                                </span>
                            </div>
                            <h3
                                class="text-2xl font-black text-slate-800 tracking-tight leading-tight group-hover:text-indigo-600 transition-colors mb-2">
                                <?php echo $c['subject']; ?>
                            </h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-xs font-bold text-indigo-400"><i class="fas fa-user-circle mr-2"></i>
                                    <?php echo $c['student_name']; ?>
                                </span>
                                <span class="text-xs font-bold text-slate-400"><i class="fas fa-clock mr-2"></i>
                                    <?php echo date('d M, Y', strtotime($c['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                            <button
                                onclick='openResolveModal(<?php echo $c["id"]; ?>, <?php echo json_encode($c["subject"]); ?>, <?php echo json_encode($c["admin_reply"]); ?>)'
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
                                <i class="fas fa-reply"></i>
                                <span>Respond</span>
                            </button>
                        </div>
                    </div>

                    <div class="text-slate-600 leading-loose text-lg mb-8 bg-slate-50/50 p-8 rounded-3xl border border-slate-100">
                        <?php echo nl2br($c['message']); ?>
                    </div>

                    <?php if ($c['admin_reply']): ?>
                        <div class="mt-8 pt-8 border-t border-indigo-50">
                            <div class="flex items-center space-x-3 mb-4">
                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Admin Resolution
                                    Profile</span>
                            </div>
                            <div
                                class="bg-indigo-50/50 p-8 rounded-3xl border border-indigo-100 text-slate-700 italic font-medium leading-relaxed">
                                <?php echo nl2br($c['admin_reply']); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Resolve Modal -->
<div id="resolve_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl p-10 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-slate-800">Redressal Action</h3>
            <button onclick="document.getElementById('resolve_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="complaints.php" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="resolve_complaint" value="1">
            <input type="hidden" name="complaint_id" id="modal_complaint_id">

            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Subject Profile
                </p>
                <p id="modal_subject" class="text-sm font-bold text-slate-800 mb-6"></p>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Institutional
                    Response / Result</label>
                <textarea name="admin_reply" id="modal_reply" rows="6" required
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-800"></textarea>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Update
                    Status</label>
                <select name="status"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-800">
                    <option value="Pending">Still Pending / Investigating</option>
                    <option value="Resolved">Resolved / Solution Provided</option>
                    <option value="Closed">Closed / No Action Required</option>
                </select>
            </div>

            <button type="submit"
                class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">Record
                Decision</button>
        </form>
    </div>
</div>

<script>
    function openResolveModal(id, subject, reply) {
        document.getElementById('modal_complaint_id').value = id;
        document.getElementById('modal_subject').textContent = subject;
        document.getElementById('modal_reply').value = reply;
        document.getElementById('resolve_modal').classList.remove('hidden');
    }
</script>

<?php require_once 'includes/footer.php'; ?>