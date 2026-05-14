<?php
$page_title = "Document Verification Center";
require_once 'includes/header.php';

// Handle Verification Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_doc'])) {
    csrf_guard();
    $doc_id = (int)$_POST['doc_id'];
    $status = $_POST['status']; // 'verified' or 'rejected'
    $remarks = sanitize($_POST['remarks'] ?? '');

    $stmt = $pdo->prepare("UPDATE student_documents SET verification_status = ?, admin_remarks = ? WHERE id = ?");
    $stmt->execute([$status, $remarks, $doc_id]);
    set_flash_message('success', 'Document status updated successfully.');
}

// Fetch students with document uploads
$query = "SELECT u.id, u.full_name, s.roll_no, 
          COUNT(d.id) as total_docs,
          SUM(CASE WHEN d.verification_status = 'pending' THEN 1 ELSE 0 END) as pending_docs
          FROM users u
          JOIN students s ON u.id = s.user_id
          JOIN student_documents d ON u.id = d.student_id
          GROUP BY u.id
          HAVING pending_docs > 0 OR total_docs > 0
          ORDER BY pending_docs DESC";

$students = $pdo->query($query)->fetchAll();
?>

<div class="mb-10 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Institutional Verification Hub</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Audit and verify student credentials for institutional registry compliance.</p>
    </div>
    <div class="flex items-center space-x-4">
         <div class="bg-amber-50 border border-amber-100 px-6 py-4 rounded-[2rem] text-amber-600 shadow-sm">
             <p class="text-[9px] font-black uppercase tracking-widest leading-none mb-1">Queue Size</p>
             <p class="text-xl font-black italic"><?php echo array_sum(array_column($students, 'pending_docs')); ?> Pending</p>
         </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Student List -->
    <div class="lg:col-span-4">
        <div class="bg-white rounded-[3rem] shadow-sm border border-indigo-100/30 overflow-hidden">
            <div class="p-6 border-b border-indigo-50 bg-slate-50/50">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest italic">Submission Registry</h4>
            </div>
            <div class="divide-y divide-indigo-50 max-h-[600px] overflow-y-auto custom-sidebar-scroll">
                <?php if (empty($students)): ?>
                    <div class="p-10 text-center text-slate-400 italic">No document submissions found.</div>
                <?php else: ?>
                    <?php foreach ($students as $std): ?>
                        <button onclick="loadDocuments(<?php echo $std['id']; ?>, '<?php echo addslashes($std['full_name']); ?>')" class="w-full text-left p-6 hover:bg-indigo-50/50 transition-all group border-l-4 border-transparent hover:border-indigo-600">
                            <div class="flex items-center justify-between mb-2">
                                <h6 class="text-sm font-black text-slate-800 group-hover:text-indigo-600 transition-colors"><?php echo $std['full_name']; ?></h6>
                                <?php if ($std['pending_docs'] > 0): ?>
                                    <span class="px-2 py-0.5 bg-amber-500 text-white text-[8px] font-black rounded-full"><?php echo $std['pending_docs']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-[10px] font-bold text-slate-400"><?php echo $std['roll_no']; ?></p>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic"><?php echo $std['total_docs']; ?> Total Logs</p>
                            </div>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Document Viewer -->
    <div class="lg:col-span-8">
        <div id="document_details" class="bg-white rounded-[3.5rem] shadow-sm border border-indigo-100/30 p-12 min-h-[600px] flex flex-col items-center justify-center text-center text-slate-400 italic">
            <i class="fas fa-file-shield text-6xl mb-6 opacity-20"></i>
            <p>Select a student from the submission registry to begin the audit process.</p>
        </div>
    </div>
</div>

<script>
function loadDocuments(studentId, studentName) {
    const container = document.getElementById('document_details');
    container.innerHTML = '<div class="animate-pulse text-indigo-600 font-black italic">ACCESSING REGISTRY...</div>';
    container.classList.remove('items-center', 'justify-center', 'text-center');

    fetch('includes/handlers/get_student_docs.php?student_id=' + studentId)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                container.innerHTML = '<div class="text-rose-500 font-black italic">ERROR: ' + data.message + '</div>';
                return;
            }

            let html = `
                <div class="flex items-center justify-between mb-12">
                    <div>
                        <h4 class="text-2xl font-black text-slate-800 italic uppercase tracking-tight">${studentName}</h4>
                        <p class="text-[10px] font-black text-primary-600 uppercase tracking-widest mt-2 italic">Institutional Credentials Audit</p>
                    </div>
                    <button onclick="location.reload()" class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all"><i class="fas fa-rotate"></i></button>
                </div>
                <div class="grid grid-cols-1 gap-6">
            `;

            data.documents.forEach(doc => {
                const statusColor = doc.verification_status === 'verified' ? 'text-emerald-500 bg-emerald-50' : (doc.verification_status === 'rejected' ? 'text-rose-500 bg-rose-50' : 'text-amber-500 bg-amber-50');
                
                html += `
                    <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 group">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1 italic">Type</p>
                                    <h6 class="text-md font-black text-slate-800 uppercase italic">${doc.document_type}</h6>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest ${statusColor}">${doc.verification_status}</span>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="../uploads/documents/${doc.file_path}" target="_blank" class="px-6 py-3 bg-white text-slate-600 font-black rounded-xl uppercase tracking-widest text-[9px] italic border border-slate-100 flex items-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-external-link-alt mr-2"></i> Preview Artifact
                            </a>
                            
                            ${doc.verification_status === 'pending' ? `
                                <div class="flex-1 flex gap-2">
                                    <form method="POST" class="flex-1 flex gap-2">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="verify_doc" value="1">
                                        <input type="hidden" name="doc_id" value="${doc.id}">
                                        <input type="text" name="remarks" placeholder="Optional remarks..." class="flex-1 bg-white border border-slate-100 rounded-xl px-4 text-[10px] italic outline-none focus:border-indigo-600">
                                        <button name="status" value="verified" class="px-4 py-3 bg-emerald-600 text-white rounded-xl font-black uppercase tracking-widest text-[9px] italic shadow-lg shadow-emerald-500/20">Verify</button>
                                        <button name="status" value="rejected" class="px-4 py-3 bg-rose-600 text-white rounded-xl font-black uppercase tracking-widest text-[9px] italic shadow-lg shadow-rose-500/20">Reject</button>
                                    </form>
                                </div>
                            ` : `
                                <div class="flex-1 p-3 bg-white/50 rounded-xl italic">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Audit Remarks</p>
                                    <p class="text-[10px] text-slate-500">${doc.admin_remarks || 'Registry verified with no supplemental remarks.'}</p>
                                </div>
                            `}
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            container.innerHTML = html;
        })
        .catch(error => {
            container.innerHTML = '<div class="text-rose-500 font-black italic">NETWORK ERROR: RE-AUTHENTICATION REQUIRED</div>';
        });
}
</script>

<?php require_once 'includes/footer.php'; ?>
