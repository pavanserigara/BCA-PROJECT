<?php
$page_title = "Absence Request Portal";
require_once 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Handle Leave Application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_leave'])) {
    csrf_guard();
    csrf_guard();
    $type = $_POST['leave_type'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $reason = sanitize($_POST['reason']);

    try {
        $stmt = $pdo->prepare("INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $type, $start, $end, $reason]);
        set_flash_message('success', 'Your leave request has been transmitted for administrative approval.');
    } catch (PDOException $e) {
        set_flash_message('error', 'Transmission failed: ' . $e->getMessage());
    }
}

// Fetch user's leave history
$stmt_history = $pdo->prepare("SELECT * FROM leave_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt_history->execute([$user_id]);
$history = $stmt_history->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Institutional Leave Portal</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Professional management of personal and medical absence requests.</p>
    </div>
    
    <button onclick="document.getElementById('leave_modal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-premium transition-all transform active:scale-95 flex items-center space-x-3 italic">
        <i class="fas fa-paper-plane"></i>
        <span>Request New Leave</span>
    </button>
</div>

<?php display_flash_message(); ?>

<div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-premium border border-slate-100 dark:border-slate-700/50 overflow-hidden mb-20">
    <div class="p-8 border-b border-slate-50 dark:border-slate-900/50 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
        <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase italic leading-none">Absence History</h4>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Personal Registry</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-50 dark:border-slate-900/50">
                    <th class="py-6 px-10">Period</th>
                    <th class="py-6 px-10">Classification</th>
                    <th class="py-6 px-10">Current Status</th>
                    <th class="py-6 px-10 text-right pr-10">Protocol Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-900/50">
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="4" class="py-20 text-center italic text-slate-400">No absence requests recorded in the current academic phase.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $row): 
                        $status_colors = [
                            'Pending' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-500 dark:border-amber-500/20',
                            'Approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-500 dark:border-emerald-500/20',
                            'Rejected' => 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-500 dark:border-rose-500/20'
                        ];
                    ?>
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-all duration-300">
                            <td class="py-8 px-10">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800 dark:text-white italic leading-none mb-1"><?php echo date('M d', strtotime($row['start_date'])); ?> - <?php echo date('M d, Y', strtotime($row['end_date'])); ?></span>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">Reference #LR-<?php echo $row['id']; ?></span>
                                </div>
                            </td>
                            <td class="py-8 px-10">
                                <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-slate-200 dark:border-slate-800 italic"><?php echo $row['leave_type']; ?></span>
                            </td>
                            <td class="py-8 px-10">
                                <div class="flex items-center space-x-2">
                                    <span class="px-4 py-2 <?php echo $status_colors[$row['status']]; ?> rounded-xl text-[10px] font-black uppercase tracking-widest border italic">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="py-8 px-10 text-right pr-10">
                                <p class="text-sm italic text-slate-500 dark:text-slate-400"><?php echo $row['admin_remarks'] ?: 'Awaiting review...'; ?></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Request Modal -->
<div id="leave_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500 animate-in fade-in">
    <div class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-[3.5rem] shadow-2xl p-12 relative overflow-hidden group animate-in zoom-in duration-300">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Request Absence</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2 italic leading-none">Institutional Protocol</p>
            </div>
            <button onclick="document.getElementById('leave_modal').classList.add('hidden')" class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-rose-600 transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="apply_leave" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Classification *</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach(['Casual','Sick','Medical','Duty'] as $l_type): ?>
                            <label class="relative cursor-pointer group/opt">
                                <input type="radio" name="leave_type" value="<?php echo $l_type; ?>" required class="peer sr-only">
                                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-2xl text-center peer-checked:bg-primary-600 peer-checked:text-white peer-checked:border-primary-600 transition-all">
                                    <p class="text-[10px] font-black uppercase tracking-widest italic"><?php echo $l_type; ?></p>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Initiation Date *</label>
                    <input type="date" name="start_date" required min="<?php echo date('Y-m-d'); ?>" class="w-full px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-slate-800 transition-all outline-none font-black text-slate-800 dark:text-white italic">
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Termination Date *</label>
                    <input type="date" name="end_date" required min="<?php echo date('Y-m-d'); ?>" class="w-full px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-slate-800 transition-all outline-none font-black text-slate-800 dark:text-white italic">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Justification / Reason *</label>
                    <textarea name="reason" rows="4" required placeholder="Provide a detailed professional justification..." class="w-full px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-slate-800 transition-all outline-none font-bold text-slate-800 dark:text-white italic"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6">
                <button type="button" onclick="document.getElementById('leave_modal').classList.add('hidden')" class="flex-1 py-6 bg-slate-50 dark:bg-slate-800 text-slate-400 font-black rounded-[2rem] hover:bg-slate-100 dark:hover:bg-slate-700 transition-all uppercase tracking-widest text-[10px] italic">Discard</button>
                <button type="submit" class="flex-2 px-15 py-6 bg-primary-600 text-white font-black rounded-[2rem] shadow-premium hover:bg-primary-700 transition-all uppercase tracking-widest text-[10px] italic">Transmit Request</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
