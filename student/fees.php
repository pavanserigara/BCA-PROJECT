<?php
$page_title = "Fees & Receipts";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

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

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight">Financial Treasury</h2>
            <p class="text-slate-500 font-medium tracking-tight italic">Monitor your educational investments and clear
                pending dues.</p>
        </div>

        <div class="flex items-center space-x-4">
            <button
                class="bg-white border border-indigo-100 text-indigo-600 px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-sm hover:shadow-indigo-50 transition-all transform active:scale-95">
                <i class="fas fa-hand-holding-dollar mr-2"></i>
                Request Installment
            </button>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-15">
        <div
            class="bg-indigo-600 p-10 rounded-[3rem] text-white shadow-2xl shadow-indigo-100 relative overflow-hidden group">
            <div class="relative z-10">
                <h4 class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-1 leading-none italic">
                    Current Semester Payable</h4>
                <div class="text-5xl font-black tracking-tight mt-4">₹
                    <?php echo number_format($total_course_fee, 2); ?>
                </div>
                <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mt-6 italic">Semester <?php echo $student['semester']; ?> Protocol</p>
            </div>
            <div
                class="absolute -right-8 -bottom-8 w-40 h-40 bg-indigo-500 rounded-full opacity-30 group-hover:scale-150 transition-transform flex items-center justify-center italic text-4xl font-black">
                V</div>
        </div>

        <div
            class="bg-white p-10 rounded-[3rem] border border-indigo-50 shadow-sm flex flex-col justify-between group hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-slate-400 text-[11px] font-black uppercase tracking-widest leading-none">Net Paid
                        Amount</h4>
                    <div
                        class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center font-black italic">
                        P</div>
                </div>
                <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mt-4">₹
                    <?php echo number_format($paid_so_far, 2); ?>
                </div>
            </div>
            <div>
                <div class="w-full h-2.5 bg-slate-50 rounded-full mt-8 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full"
                        style="width: <?php echo $total_course_fee > 0 ? ($paid_so_far / $total_course_fee) * 100 : 0; ?>%"></div>
                </div>
                <p
                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-4 flex items-center justify-between">
                    <span>Investment Progress</span>
                    <span class="text-emerald-500">
                        <?php echo $total_course_fee > 0 ? round(($paid_so_far / $total_course_fee) * 100, 1) : 0; ?>% Clear
                    </span>
                </p>
            </div>
        </div>

        <div
            class="bg-white p-10 rounded-[3rem] border border-rose-100 shadow-sm flex flex-col justify-between group hover:shadow-xl hover:shadow-rose-50 transition-all duration-300">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-rose-400 text-[11px] font-black uppercase tracking-widest leading-none">Outstanding
                        Dues</h4>
                    <div
                        class="w-10 h-10 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center font-black">
                        !</div>
                </div>
                <div class="text-4xl font-black text-rose-600 tracking-tight leading-none mt-4">₹
                    <?php echo number_format($outstanding, 2); ?>
                </div>
            </div>
            <a href="complaints.php"
                class="w-full py-5 bg-rose-600 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest shadow-xl shadow-rose-900/20 hover:bg-rose-700 transition-all transform active:scale-95 flex items-center justify-center gap-3">
                <i class="fas fa-headset"></i> Contact Accounts
            </a>
        </div>
    </div>

    <!-- History -->
    <div
        class="bg-white rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden mb-20 animate__animated animate__fadeInUp">
        <div
            class="p-12 border-b border-indigo-50 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight">Transaction History</h4>
            <div class="bg-white border border-indigo-100 px-6 py-3 rounded-2xl flex items-center space-x-4">
                <i class="fas fa-search text-slate-300 text-xs"></i>
                <input type="text" placeholder="Search by Receipt #"
                    class="bg-transparent border-none focus:ring-0 text-[11px] font-black uppercase tracking-widest text-slate-800 placeholder-slate-300">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="py-8 px-12">Transaction Date</th>
                        <th class="py-8 px-12">Receipt ID</th>
                        <th class="py-8 px-12 text-center">Payment System</th>
                        <th class="py-8 px-12 text-center">Amount (INR)</th>
                        <th class="py-8 px-12 text-right">Confirmation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/20">
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="5" class="p-24 text-center">
                                <i class="fas fa-receipt text-slate-100 text-6xl mb-6 block"></i>
                                <p class="text-slate-400 italic text-xl">No payments recorded in history yet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $pay): ?>
                            <tr class="group hover:bg-slate-50/80 transition-all">
                                <td class="py-8 px-12">
                                    <span class="text-sm font-bold text-slate-700">
                                        <?php echo date('M d, Y', strtotime($pay['payment_date'])); ?>
                                    </span>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">
                                        <?php echo strtoupper($pay['status']); ?>
                                    </p>
                                </td>
                                <td class="py-8 px-12">
                                    <span
                                        class="text-xs font-black text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">REC-
                                        <?php echo $pay['receipt_no']; ?>
                                    </span>
                                </td>
                                <td class="py-8 px-12 text-center">
                                    <span class="text-sm font-black text-slate-800 tracking-tight">
                                        <?php echo $pay['payment_method']; ?>
                                    </span>
                                </td>
                                <td class="py-8 px-12 text-center">
                                    <div class="text-xl font-black text-slate-900 tracking-tight">₹
                                        <?php echo number_format($pay['amount'], 2); ?>
                                    </div>
                                </td>
                                <td class="py-8 px-12 text-right">
                                    <?php
                                    $status = $pay['status'];
                                    $pill = $status === 'Paid'
                                        ? "bg-emerald-50 text-emerald-600 border-emerald-100"
                                        : ($status === 'Partial' ? "bg-amber-50 text-amber-700 border-amber-100" : "bg-rose-50 text-rose-600 border-rose-100");
                                    ?>
                                    <span class="px-4 py-2 <?php echo $pill; ?> border rounded-xl text-[10px] font-black uppercase tracking-widest leading-none">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-12 bg-slate-900 text-slate-400 text-center flex flex-col items-center gap-4">
            <div class="w-16 h-1 w-12 bg-indigo-500 rounded-full mb-4"></div>
            <p class="text-xs font-bold uppercase tracking-widest leading-loose max-w-lg">
                For reconciliation or payment issues, please contact the Financial Department directly with your
                transaction UTR or Receipt ID.
            </p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>