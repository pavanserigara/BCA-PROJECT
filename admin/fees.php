<?php
$page_title = "Fee Management";
require_once 'includes/header.php';

// Stats
$total_collected = $pdo->query("SELECT SUM(amount) FROM fee_payments")->fetchColumn() ?: 0;
// Pending dues (REAL): expected from fees_structure for each student minus paid
$pending_dues = 0;
$critical_students = 0;
try {
    $rows = $pdo->query("
        SELECT st.user_id,
               COALESCE(SUM(fs.amount), 0) AS expected_amount,
               COALESCE((SELECT SUM(fp.amount) FROM fee_payments fp WHERE fp.student_id = st.user_id AND fp.status IN ('Paid','Partial')), 0) AS paid_amount
        FROM students st
        LEFT JOIN fees_structure fs ON fs.course_id = st.course_id AND fs.semester = st.semester
        GROUP BY st.user_id
    ")->fetchAll();

    foreach ($rows as $r) {
        $due = max(0, (float) $r['expected_amount'] - (float) $r['paid_amount']);
        $pending_dues += $due;
        if ($due > 0) $critical_students++;
    }
} catch (PDOException $e) {
    // keep zeros if fee tables aren't populated
}

$payments = $pdo->query("SELECT p.*, u.full_name, s.roll_no, c.name as course_name 
                         FROM fee_payments p 
                         JOIN users u ON p.student_id = u.id 
                         JOIN students s ON u.id = s.user_id 
                         JOIN courses c ON s.course_id = c.id 
                         ORDER BY p.payment_date DESC LIMIT 20")->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Financial Treasury</h2>
        <p class="text-slate-500 font-medium">Monitor revenue collection and pending student dues.</p>
    </div>

    <div class="flex items-center space-x-4">
        <a href="fees-structure.php"
            class="bg-white border border-indigo-100 text-slate-600 px-6 py-3.5 rounded-2xl font-bold flex items-center space-x-2 transition-all hover:bg-slate-50">
            <i class="fas fa-cog text-xs"></i>
            <span>Configure Structure</span>
        </a>
        <a href="fees-collect.php"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
            <i class="fas fa-hand-holding-dollar"></i>
            <span>Record Payment</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
    <div
        class="bg-indigo-600 p-10 rounded-[3rem] text-white shadow-2xl shadow-indigo-100 relative overflow-hidden group">
        <div class="flex flex-col justify-between h-full relative z-10">
            <h4 class="text-indigo-100 text-[11px] font-black uppercase tracking-widest mb-1">Total Revenue Collected
            </h4>
            <div class="text-5xl font-black tracking-tight mt-2">₹
                <?php echo number_format($total_collected, 2); ?>
            </div>
            <div class="mt-10 flex items-center space-x-2 text-indigo-100/60 font-bold text-xs">
                <i class="fas fa-chart-line"></i>
                <span>+8.2% Growth This Academic Year</span>
            </div>
        </div>
        <div
            class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500 rounded-full opacity-30 group-hover:scale-150 transition-transform">
        </div>
    </div>

    <div
        class="bg-white p-10 rounded-[3rem] border border-indigo-100 shadow-sm flex flex-col justify-between group hover:shadow-xl hover:shadow-indigo-50 transition-all duration-300">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-slate-400 text-[11px] font-black uppercase tracking-widest leading-none">Pending Collections
            </h4>
            <div
                class="w-10 h-10 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center font-bold text-lg">
                !</div>
        </div>
        <div>
            <div class="text-4xl font-black text-slate-800 tracking-tight leading-none mb-1">₹
                <?php echo number_format($pending_dues, 2); ?>
            </div>
            <div class="w-full h-2.5 bg-slate-50 rounded-full mt-6 overflow-hidden">
                <div class="h-full bg-rose-400 rounded-full" style="width: 35%"></div>
            </div>
        </div>
        <div
            class="flex items-center justify-between text-[11px] font-black uppercase tracking-widest text-slate-400 mt-4 leading-none">
            <span>Critical Dues</span>
            <span class="text-rose-500"><?php echo (int) $critical_students; ?> Students</span>
        </div>
    </div>
</div>

<div class="bg-white rounded-[3rem] shadow-sm border border-indigo-100/50 overflow-hidden">
    <div class="p-10 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
        <h4 class="text-xl font-bold text-slate-800">Recent Transactions</h4>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                    <th class="py-6 px-10">Student Profile</th>
                    <th class="py-6 px-10">Receipt #</th>
                    <th class="py-6 px-10">Amount Paid</th>
                    <th class="py-6 px-10 text-center">Status</th>
                    <th class="py-6 px-10 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-50/30">
                <?php foreach ($payments as $pay): ?>
                    <tr class="group hover:bg-indigo-50/20 transition-all">
                        <td class="py-6 px-10">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-indigo-600 font-black text-[10px] shadow-sm">
                                    <?php echo substr($pay['full_name'], 0, 1); ?>
                                </div>
                                <div>
                                    <h6 class="text-sm font-bold text-slate-800 tracking-tight leading-none mb-1">
                                        <?php echo $pay['full_name']; ?>
                                    </h6>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        <?php echo $pay['roll_no']; ?> •
                                        <?php echo $pay['course_name']; ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-10">
                            <span class="text-xs font-black text-slate-400 group-hover:text-slate-700 transition-colors">#
                                <?php echo $pay['receipt_no']; ?>
                            </span>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">
                                <?php echo date('M d, Y', strtotime($pay['payment_date'])); ?>
                            </p>
                        </td>
                        <td class="py-6 px-10">
                            <div class="text-base font-black text-slate-800 tracking-tight">₹
                                <?php echo number_format($pay['amount'], 2); ?>
                            </div>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mt-1">
                                <?php echo $pay['payment_method']; ?>
                            </p>
                        </td>
                        <td class="py-6 px-10 text-center">
                            <span
                                class="px-4 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                <?php echo $pay['status']; ?>
                            </span>
                        </td>
                        <td class="py-6 px-10 text-right">
                            <button
                                class="w-10 h-10 bg-white border border-slate-100 rounded-xl text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all">
                                <i class="fas fa-print"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>