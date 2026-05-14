<?php
$page_title = "Record Payment";
require_once 'includes/header.php';

// Handle Payment Recording
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_payment'])) {
    csrf_guard();
    $student_id = (int) $_POST['student_id'];
    $amount = (float) $_POST['amount'];
    $method = sanitize($_POST['payment_method']);
    $date = date('Y-m-d');
    $receipt_no = 'PAY-' . strtoupper(uniqid());

    try {
        $stmt = $pdo->prepare("INSERT INTO fee_payments (student_id, amount, payment_date, receipt_no, payment_method, status) VALUES (?, ?, ?, ?, ?, 'Paid')");
        $stmt->execute([$student_id, $amount, $date, $receipt_no, $method]);
        set_flash_message('success', "Payment of ₹" . number_format($amount, 2) . " recorded. Receipt: $receipt_no");
    } catch (PDOException $e) {
        set_flash_message('error', 'Payment recording failed: ' . $e->getMessage());
    }
}

$students = $pdo->query("SELECT s.user_id, u.full_name, s.roll_no, c.name as course_name 
                         FROM students s 
                         JOIN users u ON s.user_id = u.id 
                         JOIN courses c ON s.course_id = c.id 
                         ORDER BY u.full_name ASC")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Fiscal Intake</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Recording institutional revenue and student financial contributions.</p>
    </div>
    
    <a href="fees.php" class="bg-white border border-slate-100 text-slate-400 px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-soft hover:text-primary-600 transition-all flex items-center space-x-3 italic">
        <i class="fas fa-arrow-left"></i>
        <span>Return to Treasury</span>
    </a>
</div>

<?php display_flash_message(); ?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-[4rem] shadow-premium border border-slate-100 overflow-hidden relative group">
        <div class="p-15">
            <div class="flex items-center space-x-4 mb-15">
                <div class="w-14 h-14 bg-emerald-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl shadow-emerald-500/20 italic font-black text-xl">₹</div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Record Transaction</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic">Authorized Personnel Only</p>
                </div>
            </div>

            <form action="" method="POST" class="space-y-10">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="record_payment" value="1">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Select Student *</label>
                        <select name="student_id" required class="w-full px-8 py-6 bg-slate-50 border-none rounded-[2.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                            <option value="">Select Target Student Profile</option>
                            <?php foreach($students as $s): ?>
                                <option value="<?php echo $s['user_id']; ?>"><?php echo $s['full_name']; ?> (<?php echo $s['roll_no']; ?>) - <?php echo $s['course_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Transaction Value (₹) *</label>
                        <input type="number" name="amount" required step="0.01" placeholder="0.00" class="w-full px-8 py-6 bg-slate-50 border-none rounded-[2.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Payment Instrument *</label>
                        <select name="payment_method" required class="w-full px-8 py-6 bg-slate-50 border-none rounded-[2.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                            <option value="Cash">Cash</option>
                            <option value="UPI / QR">UPI / QR Scan</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Card">Credit / Debit Card</option>
                            <option value="Cheque">Institutional Cheque</option>
                        </select>
                    </div>
                </div>

                <div class="pt-10 flex items-center justify-center">
                    <button type="submit" class="w-full md:w-auto px-20 py-7 bg-emerald-600 text-white font-black rounded-[2.5rem] shadow-xl shadow-emerald-500/30 hover:bg-emerald-700 transition-all transform active:scale-95 uppercase tracking-widest text-xs italic flex items-center justify-center space-x-4">
                        <i class="fas fa-receipt text-lg"></i>
                        <span>Transmit Payment Record</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="h-2 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-500"></div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
