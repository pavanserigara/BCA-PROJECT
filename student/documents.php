<?php
$page_title = "Digital Document Vault";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Fetch existing documents
$stmt = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ?");
$stmt->execute([$student_id]);
$docs = $stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

$doc_types = [
    'aadhaar' => 'Aadhaar Card',
    'sslc' => 'SSLC Marks Card',
    'puc' => 'PUC Marks Card',
    'tc' => 'Transfer Certificate',
    'photo' => 'Passport Photo',
    'other' => 'Other Documents'
];
?>

<div class="mb-10 lg:mb-16">
    <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight uppercase italic">Institutional Document Vault</h2>
    <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Secure digital repository for your academic and identification credentials.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php foreach ($doc_types as $key => $label): ?>
        <?php 
            $doc = $docs[$key] ?? null;
            $status = $doc['verification_status'] ?? 'missing';
            $status_class = '';
            $status_icon = '';
            
            switch($status) {
                case 'verified':
                    $status_class = 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100 dark:border-emerald-500/20';
                    $status_icon = 'fa-circle-check';
                    break;
                case 'rejected':
                    $status_class = 'text-rose-500 bg-rose-50 dark:bg-rose-500/10 border-rose-100 dark:border-rose-500/20';
                    $status_icon = 'fa-circle-xmark';
                    break;
                case 'pending':
                    $status_class = 'text-amber-500 bg-amber-50 dark:bg-amber-500/10 border-amber-100 dark:border-amber-500/20';
                    $status_icon = 'fa-clock';
                    break;
                default:
                    $status_class = 'text-slate-400 bg-slate-50 dark:bg-slate-900 border-slate-100 dark:border-slate-800';
                    $status_icon = 'fa-file-circle-plus';
            }
        ?>
        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 border border-slate-100 dark:border-slate-700/50 shadow-soft hover:shadow-premium transition-all group">
            <div class="flex items-center justify-between mb-8">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-primary-600 group-hover:scale-110 transition-transform">
                    <i class="fas <?php echo $status === 'missing' ? 'fa-file-arrow-up' : 'fa-file-shield'; ?> text-lg"></i>
                </div>
                <span class="px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest <?php echo $status_class; ?> flex items-center">
                    <i class="fas <?php echo $status_icon; ?> mr-1.5"></i>
                    <?php echo $status; ?>
                </span>
            </div>
            
            <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase italic mb-2"><?php echo $label; ?></h4>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest mb-8">
                <?php echo $doc ? 'Uploaded on ' . date('M d, Y', strtotime($doc['uploaded_at'])) : 'Registry entry required'; ?>
            </p>

            <?php if ($doc && $status === 'rejected'): ?>
                <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-500/5 rounded-xl border border-rose-100 dark:border-rose-500/10">
                    <p class="text-[9px] font-black text-rose-600 uppercase tracking-widest mb-1 italic">Admin Remarks:</p>
                    <p class="text-[10px] text-rose-500 italic"><?php echo htmlspecialchars($doc['admin_remarks']); ?></p>
                </div>
            <?php endif; ?>

            <div class="flex gap-4">
                <?php if ($doc): ?>
                    <a href="../uploads/documents/<?php echo $doc['file_path']; ?>" target="_blank" class="flex-1 py-4 bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 font-black rounded-2xl uppercase tracking-widest text-[9px] italic border border-slate-100 dark:border-slate-700 flex items-center justify-center hover:bg-primary-600 hover:text-white transition-all">
                        <i class="fas fa-eye mr-2"></i> Preview
                    </a>
                <?php endif; ?>
                
                <?php if ($status !== 'verified'): ?>
                    <label class="flex-1 py-4 bg-primary-600 text-white font-black rounded-2xl uppercase tracking-widest text-[9px] italic shadow-lg shadow-primary-500/20 flex items-center justify-center cursor-pointer hover:-translate-y-1 transition-all">
                        <i class="fas fa-upload mr-2"></i> <?php echo $doc ? 'Replace' : 'Upload'; ?>
                        <input type="file" class="hidden doc-upload" data-type="<?php echo $key; ?>" accept=".pdf,image/*">
                    </label>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.doc-upload').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const type = this.dataset.type;
        if (!file) return;

        const formData = new FormData();
        formData.append('document_file', file);
        formData.append('document_type', type);
        formData.append('csrf_token', '<?php echo generate_csrf_token(); ?>');

        // Loading state
        const card = this.closest('.group');
        card.classList.add('opacity-50', 'pointer-events-none');

        fetch('includes/handlers/document_upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            card.classList.remove('opacity-50', 'pointer-events-none');
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            card.classList.remove('opacity-50', 'pointer-events-none');
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
