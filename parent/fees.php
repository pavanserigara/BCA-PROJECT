<?php
$page_title = "Treasury & Fiscal Registry";
require_once 'includes/header.php';

$student_id = $_SESSION['linked_student_id'];

// Get Student info
$stmt_std = $pdo->prepare("SELECT s.*, c.name as course_name FROM students s JOIN courses c ON s.course_id = c.id WHERE s.user_id = ?");
$stmt_std->execute([$student_id]);
$student = $stmt_std->fetch();

// Fetch payment history
$stmt_pay = $pdo->prepare("SELECT * FROM fee_payments WHERE student_id = ? ORDER BY payment_date DESC");
$stmt_pay->execute([$student_id]);
$payments = $stmt_pay->fetchAll();

// Fee details (REAL): use fees_structure as expected amount for current semester
$stmt_total = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM fees_structure WHERE course_id = ? AND semester = ?");
$stmt_total->execute([(int) $student['course_id'], (int) $student['semester']]);
$total_course_fee = (float) $stmt_total->fetchColumn();

$paid_so_far = 0.0;
foreach ($payments as $p) {
    if (in_array($p['status'], ['Paid', 'Partial'], true)) {
        $paid_so_far += (float) $p['amount'];
    }
}
$outstanding = max(0, $total_course_fee - $paid_so_far);
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Institutional Treasury</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Verified fiscal registry for <?php echo $_SESSION['linked_student_name']; ?>.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
    <!-- Payable Card -->
    <div class="bg-slate-900 p-10 rounded-[3.5rem] shadow-premium relative overflow-hidden group">
        <div class="relative z-10">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6 italic">Current Phase Payable</p>
            <h3 class="text-5xl font-black text-white italic tracking-tighter">₹<?php echo number_format($total_course_fee, 0); ?></h3>
            <p class="text-[9px] font-black text-primary-400 uppercase tracking-widest mt-6 italic">Semester <?php echo $student['semester']; ?> Protocol</p>
        </div>
        <i class="fas fa-vault absolute -bottom-10 -right-10 text-9xl text-white/5 group-hover:scale-110 transition-transform"></i>
    </div>

    <!-- Paid Card -->
    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800 flex flex-col justify-between group hover:shadow-xl transition-all">
        <div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Cleared Revenue</p>
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center italic font-black">C</div>
            </div>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white italic tracking-tighter">₹<?php echo number_format($paid_so_far, 0); ?></h3>
        </div>
        <div class="mt-8">
            <div class="w-full h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: <?php echo $total_course_fee > 0 ? ($paid_so_far / $total_course_fee) * 100 : 0; ?>%"></div>
            </div>
            <div class="flex items-center justify-between mt-3 text-[9px] font-black uppercase tracking-widest text-slate-400">
                <span>Fiscal Target</span>
                <span class="text-emerald-500"><?php echo $total_course_fee > 0 ? round(($paid_so_far / $total_course_fee) * 100) : 0; ?>% Clear</span>
            </div>
        </div>
    </div>

    <!-- Outstanding Card -->
    <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-premium border border-rose-100 dark:border-rose-500/20 flex flex-col justify-between group hover:shadow-xl transition-all">
        <div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest italic">Outstanding Dues</p>
                <div class="w-10 h-10 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-2xl flex items-center justify-center font-black animate-pulse">!</div>
            </div>
            <h3 class="text-4xl font-black text-rose-600 italic tracking-tighter">₹<?php echo number_format($outstanding, 0); ?></h3>
        </div>
        <a href="messaging.php" class="w-full py-5 bg-rose-600 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest text-center shadow-xl shadow-rose-500/20 hover:bg-rose-700 transition-all active:scale-95 italic">Interface Accounts</a>
    </div>
</div>

<!-- History -->
<div class="bg-white dark:bg-slate-800 rounded-[4rem] shadow-premium border border-slate-100 dark:border-slate-800 overflow-hidden mb-20">
    <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
        <h4 class="text-xl font-black text-slate-800 dark:text-white uppercase italic">Transaction Registry</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <th class="py-8 px-10">Capture Date</th>
                    <th class="py-8 px-10">Receipt ID</th>
                    <th class="py-8 px-10">Protocol</th>
                    <th class="py-8 px-10 text-center">Value (INR)</th>
                    <th class="py-8 px-10 text-right pr-10">Verification</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                <?php if (empty($payments)): ?>
                    <tr><td colspan="5" class="py-20 text-center text-slate-400 italic font-bold">No institutional revenue captures recorded.</td></tr>
                <?php else: ?>
                    <?php foreach ($payments as $pay): ?>
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-all">
                            <td class="py-8 px-10">
                                <p class="text-sm font-black text-slate-800 dark:text-white italic"><?php echo date('M d, Y', strtotime($pay['payment_date'])); ?></p>
                            </td>
                            <td class="py-8 px-10">
                                <span class="text-[10px] font-black text-primary-600 bg-primary-50 dark:bg-primary-500/10 px-3 py-1.5 rounded-lg border border-primary-100 dark:border-primary-500/20"><?php echo $pay['receipt_no']; ?></span>
                            </td>
                            <td class="py-8 px-10">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo $pay['payment_method']; ?></p>
                            </td>
                            <td class="py-8 px-10 text-center">
                                <p class="text-xl font-black text-slate-800 dark:text-white italic tracking-tighter">₹<?php echo number_format($pay['amount'], 0); ?></p>
                            </td>
                            <td class="py-8 px-10 text-right pr-10">
                                <span class="px-4 py-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border border-emerald-100 dark:border-emerald-500/20 rounded-xl text-[9px] font-black uppercase tracking-widest"><?php echo $pay['status']; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
