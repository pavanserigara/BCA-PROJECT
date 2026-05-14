<?php
$page_title = "Absence Protocol Management";
require_once 'includes/header.php';

// Handle Approval/Rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_leave'])) {
    csrf_guard();
    $request_id = (int) $_POST['request_id'];
    $status = $_POST['status'];
    $remarks = sanitize($_POST['admin_remarks']);
    $admin_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("UPDATE leave_requests SET status = ?, admin_remarks = ?, approved_by = ? WHERE id = ?");
        $stmt->execute([$status, $remarks, $admin_id, $request_id]);
        set_flash_message('success', "Absence Protocol #LR-$request_id processed as $status.");
    } catch (PDOException $e) {
        set_flash_message('error', 'Processing failed: ' . $e->getMessage());
    }
}

// Fetch pending and recent requests
$stmt_pending = $pdo->query("SELECT lr.*, u.full_name, u.role FROM leave_requests lr JOIN users u ON lr.user_id = u.id WHERE lr.status = 'Pending' ORDER BY lr.created_at ASC");
$pending_requests = $stmt_pending->fetchAll();

$stmt_processed = $pdo->query("SELECT lr.*, u.full_name, u.role FROM leave_requests lr JOIN users u ON lr.user_id = u.id WHERE lr.status != 'Pending' ORDER BY lr.created_at DESC LIMIT 20");
$processed_requests = $stmt_processed->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Absence Protocol Registry</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Institutional review and authorization of faculty/staff absence requests.</p>
    </div>
    
    <div class="flex items-center space-x-6">
        <div class="bg-amber-50 px-8 py-5 rounded-[2.5rem] border border-amber-100 flex items-center space-x-6 shadow-premium">
            <div class="text-right">
                <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest leading-none mb-1 italic">Pending Review</p>
                <p class="text-2xl font-black text-slate-800 tracking-tight leading-none italic"><?php echo count($pending_requests); ?></p>
            </div>
            <div class="w-12 h-12 bg-amber-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-400">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>
</div>

<?php display_flash_message(); ?>

<!-- Pending Requests -->
<div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-100 overflow-hidden mb-15">
    <div class="p-10 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
        <h4 class="text-xl font-black text-slate-800 uppercase italic leading-none">Incoming Requests</h4>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic leading-none">Awaiting Protocol</span>
    </div>
    
    <?php if (empty($pending_requests)): ?>
        <div class="py-24 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                <i class="fas fa-check-double text-3xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 italic uppercase">Registry Clear</h3>
            <p class="text-slate-400 mt-2 italic">All absence protocols have been processed.</p>
        </div>
    <?php else: ?>
        <div class="divide-y divide-slate-50">
            <?php foreach ($pending_requests as $row): ?>
                <div class="p-10 hover:bg-slate-50/30 transition-all group">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-6">
                                <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white font-black italic shadow-xl"><?php echo substr($row['full_name'], 0, 1); ?></div>
                                <div>
                                    <h5 class="text-xl font-black text-slate-800 tracking-tight leading-none italic"><?php echo $row['full_name']; ?></h5>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2 italic leading-none"><?php echo $row['role']; ?> • #LR-<?php echo $row['id']; ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-2">Classification</p>
                                    <p class="text-sm font-black text-slate-800 italic"><?php echo $row['leave_type']; ?></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-2">Duration</p>
                                    <p class="text-sm font-black text-slate-800 italic"><?php echo date('M d', strtotime($row['start_date'])); ?> — <?php echo date('M d, Y', strtotime($row['end_date'])); ?></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-2">Submitted</p>
                                    <p class="text-sm font-bold text-slate-500 italic"><?php echo date('M d, H:i', strtotime($row['created_at'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="mt-6 px-6">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic mb-3">Justification</p>
                                <p class="text-sm font-medium text-slate-600 leading-relaxed italic italic">"<?php echo $row['reason']; ?>"</p>
                            </div>
                        </div>
                        
                        <div class="lg:w-80">
                            <form action="" method="POST" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="process_leave" value="1">
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                
                                <textarea name="admin_remarks" rows="2" placeholder="Protocol Remarks..." class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm italic font-medium focus:ring-4 focus:ring-primary-500/10 outline-none transition-all"></textarea>
                                
                                <div class="flex gap-3">
                                    <button type="submit" name="status" value="Approved" class="flex-1 py-4 bg-emerald-600 text-white font-black rounded-2xl shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-all uppercase tracking-widest text-[9px] italic">Authorize</button>
                                    <button type="submit" name="status" value="Rejected" class="flex-1 py-4 bg-rose-600 text-white font-black rounded-2xl shadow-lg shadow-rose-500/20 hover:bg-rose-700 transition-all uppercase tracking-widest text-[9px] italic">Decline</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Registry -->
<div class="bg-white rounded-[3.5rem] shadow-soft border border-slate-100 overflow-hidden mb-20 opacity-80">
    <div class="p-10 border-b border-slate-50 flex items-center justify-between">
        <h4 class="text-lg font-black text-slate-800 uppercase italic leading-none">Registry Logs</h4>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic leading-none">Last 20 Actions</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="py-6 px-10">Personnel</th>
                    <th class="py-6 px-10">Decision</th>
                    <th class="py-6 px-10">Remarks</th>
                    <th class="py-6 px-10 text-right pr-10">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($processed_requests as $row): ?>
                    <tr class="italic">
                        <td class="py-6 px-10">
                            <p class="text-sm font-black text-slate-800"><?php echo $row['full_name']; ?></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><?php echo $row['role']; ?></p>
                        </td>
                        <td class="py-6 px-10">
                            <span class="px-3 py-1 <?php echo $row['status'] == 'Approved' ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'; ?> rounded-lg text-[9px] font-black uppercase tracking-widest"><?php echo $row['status']; ?></span>
                        </td>
                        <td class="py-6 px-10 text-sm text-slate-500 font-medium"><?php echo $row['admin_remarks'] ?: '—'; ?></td>
                        <td class="py-6 px-10 text-right pr-10 text-[10px] font-bold text-slate-400"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
