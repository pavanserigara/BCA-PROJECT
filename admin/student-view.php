<?php
$page_title = "Institutional Student Profile";
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch Student detailed info
$stmt = $pdo->prepare("SELECT u.*, s.*, c.name as course_name, d.name as department_name 
                       FROM users u 
                       JOIN students s ON u.id = s.user_id 
                       JOIN courses c ON s.course_id = c.id 
                       JOIN departments d ON c.dept_id = d.id
                       WHERE u.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    echo "<div class='p-20 text-center'><h3 class='text-2xl font-black text-rose-500'>Profile Not Found</h3><a href='students-list.php' class='text-indigo-600 font-bold'>Back to Directory</a></div>";
    require_once 'includes/footer.php';
    exit();
}

// Fetch Attendance Stats
$stmt_att = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Present' OR status = 'Late' THEN 1 ELSE 0 END) as present
    FROM attendance WHERE student_id = ?");
$stmt_att->execute([$id]);
$att_stats = $stmt_att->fetch();
$att_percent = $att_stats['total'] > 0 ? round(($att_stats['present'] / $att_stats['total']) * 100) : 0;

// Financial Telemetry
$stmt_pay = $pdo->prepare("SELECT SUM(amount) FROM fee_payments WHERE student_id = ? AND status IN ('Paid', 'Partial')");
$stmt_pay->execute([$id]);
$paid_so_far = (float) $stmt_pay->fetchColumn();

$stmt_total = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM fees_structure WHERE course_id = ? AND semester = ?");
$stmt_total->execute([(int) $student['course_id'], (int) $student['semester']]);
$total_course_fee = (float) $stmt_total->fetchColumn();
$outstanding = max(0, $total_course_fee - $paid_so_far);

// Fetch Academic Performance
$stmt_marks = $pdo->prepare("SELECT m.*, s.name as subject_name, s.code 
                             FROM marks m 
                             JOIN subjects s ON m.subject_id = s.id 
                             WHERE m.student_id = ? 
                             ORDER BY s.semester ASC");
$stmt_marks->execute([$id]);
$performance = $stmt_marks->fetchAll();

// Fetch Institutional Documents
$stmt_docs = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ?");
$stmt_docs->execute([$id]);
$documents = $stmt_docs->fetchAll();
?>

<div class="flex items-center justify-between mb-15">
    <div class="flex items-center space-x-6">
        <a href="students-list.php"
            class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <?php 
            $pic_url = '../assets/images/default_profile.svg';
            if (!empty($student['profile_pic'])) {
                if (is_file(__DIR__ . '/../../uploads/profiles/' . $student['profile_pic'])) {
                    $pic_url = '../uploads/profiles/' . $student['profile_pic'];
                } elseif (is_file(__DIR__ . '/../../assets/images/' . $student['profile_pic'])) {
                    $pic_url = '../assets/images/' . $student['profile_pic'];
                }
            }
        ?>
        <img src="<?php echo $pic_url; ?>" class="w-20 h-20 rounded-[2rem] object-cover shadow-2xl border-4 border-white" alt="">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">
                <?php echo $student['full_name']; ?>
            </h2>
            <div class="flex items-center space-x-3 mt-3">
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">
                    <?php echo $student['roll_no']; ?>
                </span>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                    <?php echo $student['course_name']; ?> (Sem
                    <?php echo $student['semester']; ?>)
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <a href="student-edit.php?id=<?php echo $id; ?>"
            class="bg-indigo-600 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100/50 hover:-translate-y-1 transition-all active:scale-95 flex items-center space-x-3 italic">
            <i class="fas fa-edit text-sm"></i>
            <span>Modify Record</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-12 pb-20">
    <!-- Left Column: Primary Stats -->
    <div class="space-y-12">
        <div class="bg-slate-900 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-10">
                <div
                    class="w-16 h-16 bg-white/5 border border-white/10 rounded-3xl flex items-center justify-center text-white font-black italic shadow-2xl">
                    P</div>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6 italic">Academic
                    Attendance Profile</p>
                <h3 class="text-6xl font-black text-white tracking-tighter mb-4 italic leading-none">
                    <?php echo $att_percent; ?>%
                </h3>
                <div class="w-full bg-white/10 h-2.5 rounded-full overflow-hidden mb-6">
                    <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000"
                        style="width: <?php echo $att_percent; ?>%"></div>
                </div>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">
                    <?php echo $att_stats['present']; ?> /
                    <?php echo $att_stats['total']; ?> Total Lectures Present
                </p>
            </div>
        </div>

        <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden">
            <h4
                class="text-xl font-black text-slate-800 tracking-tight italic mb-10 border-b border-indigo-50 pb-6 leading-none">
                Institutional Treasury</h4>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Total
                            Tuition</p>
                        <p class="text-lg font-black text-slate-800 leading-none">₹<?php echo number_format($total_course_fee, 0); ?></p>
                    </div>
                    <span
                        class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-black italic text-[10px] leading-none">TR</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Cleared
                            Flow</p>
                        <p class="text-lg font-black text-emerald-500 leading-none">₹<?php echo number_format($paid_so_far, 0); ?></p>
                    </div>
                    <span
                        class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black italic text-[10px] leading-none">CL</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Remaining
                            Balance</p>
                        <p class="text-lg font-black text-rose-500 leading-none">₹<?php echo number_format($outstanding, 0); ?></p>
                    </div>
                    <span
                        class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-black italic text-[10px] leading-none">RM</span>
                </div>
        </div>
    </div>

    <!-- Right Column: Personal & Academic Details -->
    <div class="lg:col-span-2 space-y-12">
        <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                <span
                    class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xs italic">C</span>
                <span>Primary Credentials</span>
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Full
                        Legal Name</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo $student['full_name']; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Institutional
                        Email Flow</label>
                    <p class="text-lg font-black text-indigo-600 tracking-tight italic">
                        <?php echo $student['email']; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Administrative
                        Department</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo $student['department_name']; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Registration
                        Date</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo date('M d, Y', strtotime($student['created_at'])); ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Mobile
                        Coordination</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic">
                        <?php echo $student['phone'] ?: 'Not Assigned'; ?>
                    </p>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Gender
                        Profile</label>
                    <p class="text-lg font-black text-slate-800 tracking-tight italic uppercase">
                        <?php echo $student['gender']; ?>
                    </p>
                </div>
            </div>

            <div class="mt-15 p-10 bg-slate-50/50 rounded-[3rem] border border-slate-50">
                <label
                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Residential
                    Address Profile</label>
                <p class="text-base font-bold text-slate-600 italic leading-loose">
                    <?php echo $student['address'] ?: 'Academic residence profile not logged.'; ?>
                </p>
            </div>
        </div>

        <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                <span
                    class="w-10 h-10 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-xs italic">A</span>
                <span>Academic Performance Index</span>
            </h4>

            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="py-6 px-4">Subject Name</th>
                        <th class="py-6 px-4">Internal</th>
                        <th class="py-6 px-4">Semester</th>
                        <th class="py-6 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/20">
                    <?php if (empty($performance)): ?>
                        <tr><td colspan="4" class="py-10 text-center text-slate-400 italic font-bold">No academic evaluation records synchronized.</td></tr>
                    <?php else: ?>
                        <?php foreach ($performance as $p): ?>
                            <tr class="group hover:bg-slate-50 transition-all">
                                <td class="py-6 px-4">
                                    <p class="text-sm font-black text-slate-800 italic uppercase"><?php echo $p['subject_name']; ?></p>
                                </td>
                                <td class="py-6 px-4"><span class="text-sm font-bold text-slate-500"><?php echo $p['marks_obtained']; ?> / <?php echo $p['max_marks']; ?></span></td>
                                <td class="py-6 px-4"><span class="text-sm font-bold text-slate-500"><?php echo $p['grade'] ?: 'N/A'; ?></span></td>
                                <td class="py-6 px-4 text-right"><span
                                        class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic font-black"><?php echo $p['marks_obtained'] >= ($p['max_marks'] * 0.4) ? 'Qualified' : 'Re-eval'; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30 mt-12">
            <div class="flex items-center justify-between mb-12">
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic flex items-center space-x-4">
                    <span class="w-10 h-10 bg-violet-50 rounded-2xl flex items-center justify-center text-violet-600 text-xs italic">V</span>
                    <span>Institutional Verification Vault</span>
                </h4>
                <a href="verify-documents.php" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest italic hover:underline">Verification Hub <i class="fas fa-arrow-right ml-1"></i></a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (empty($documents)): ?>
                    <div class="col-span-full py-12 bg-slate-50 rounded-[3rem] text-center border border-dashed border-slate-200">
                        <i class="fas fa-file-circle-exclamation text-slate-300 text-4xl mb-4 opacity-50"></i>
                        <p class="text-slate-400 italic font-bold">No institutional certifications synchronized for this identity.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <div class="p-8 bg-slate-50/50 rounded-[2.5rem] border border-slate-100 group hover:border-indigo-200 transition-all">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm border border-slate-50">
                                    <i class="fas fa-file-shield"></i>
                                </div>
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest <?php 
                                    echo $doc['verification_status'] === 'verified' ? 'text-emerald-500 bg-emerald-50' : ($doc['verification_status'] === 'rejected' ? 'text-rose-500 bg-rose-50' : 'text-amber-500 bg-amber-50'); 
                                ?>">
                                    <?php echo $doc['verification_status']; ?>
                                </span>
                            </div>
                            <h6 class="text-sm font-black text-slate-800 uppercase italic mb-1"><?php echo str_replace('_', ' ', $doc['document_type']); ?></h6>
                            <p class="text-[10px] font-bold text-slate-400 mb-6 italic">Protocol Log: <?php echo date('M d, Y', strtotime($doc['uploaded_at'])); ?></p>
                            
                            <a href="../uploads/documents/<?php echo $doc['file_path']; ?>" target="_blank" 
                               class="w-full py-3 bg-white text-slate-600 border border-slate-100 rounded-xl text-[9px] font-black uppercase tracking-widest italic flex items-center justify-center hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                <i class="fas fa-external-link-alt mr-2 text-[8px]"></i> Audit Artifact
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>