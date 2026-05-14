<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('student')) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Handle Application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    csrf_guard();
    $placement_id = (int)$_POST['placement_id'];
    
    // Check if already applied
    $stmt = $pdo->prepare("SELECT id FROM placement_applications WHERE placement_id = ? AND student_id = ?");
    $stmt->execute([$placement_id, $student_id]);
    if ($stmt->fetch()) {
        set_flash_message('error', 'You have already applied for this opportunity.');
    } else {
        $resume_path = NULL;
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
            $upload_dir = '../uploads/resumes/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $filename = 'resume_' . $student_id . '_' . time() . '_' . $_FILES['resume']['name'];
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $upload_dir . $filename)) {
                $resume_path = 'resumes/' . $filename;
            }
        }
        
        $pdo->prepare("INSERT INTO placement_applications (placement_id, student_id, resume_path) VALUES (?, ?, ?)")->execute([$placement_id, $student_id, $resume_path]);
        set_flash_message('success', 'Application transmitted successfully!');
    }
}

$page_title = "Career Hub";
require_once 'includes/header.php';

$jobs = $pdo->query("SELECT p.*, (SELECT status FROM placement_applications WHERE placement_id = p.id AND student_id = $student_id) as my_status 
                     FROM placements p WHERE p.status = 'Open' ORDER BY p.deadline ASC")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Career Launchpad</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Discover institutional career drives and apply to top corporate entities.</p>
    </div>
</div>

<?php display_flash_message(); ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-10 pb-20">
    <?php if (empty($jobs)): ?>
        <div class="md:col-span-2 py-20 bg-white dark:bg-slate-800 rounded-[4rem] text-center border-2 border-dashed border-slate-100 dark:border-slate-700 shadow-premium">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                <i class="fas fa-briefcase-clock text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic">Silence in the Hub</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 font-bold italic">No active recruitment drives are currently synchronized with your profile.</p>
        </div>
    <?php else: ?>
        <?php foreach ($jobs as $j): ?>
            <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-soft border border-slate-50 dark:border-slate-700/50 group hover:shadow-premium transition-all duration-500 relative flex flex-col justify-between overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-600/5 rounded-full group-hover:scale-110 transition-transform"></div>
                
                <div>
                    <div class="flex items-center justify-between mb-8">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-3xl flex items-center justify-center text-indigo-600 font-black text-2xl italic shadow-inner border border-slate-100 dark:border-slate-800">
                            <?php echo substr($j['company'], 0, 1); ?>
                        </div>
                        <?php if ($j['my_status']): ?>
                            <span class="px-4 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border border-emerald-100 dark:border-emerald-500/20 rounded-xl text-[9px] font-black uppercase tracking-widest italic"><?php echo $j['my_status']; ?></span>
                        <?php else: ?>
                            <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest italic">Ends <?php echo date('M d', strtotime($j['deadline'])); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight leading-none mb-3 italic uppercase group-hover:text-indigo-600 transition-colors">
                        <?php echo $j['company']; ?>
                    </h3>
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest italic mb-8"><?php echo $j['role']; ?> • ₹<?php echo $j['package']; ?></p>
                    
                    <div class="space-y-6 mb-10">
                        <div>
                            <p class="text-[8px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest mb-2 italic">Brief</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 italic line-clamp-2"><?php echo $j['description']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 pt-6">
                    <?php if ($j['my_status']): ?>
                        <div class="w-full py-5 bg-slate-50 dark:bg-slate-900 text-slate-400 rounded-[2.5rem] font-black text-[10px] uppercase tracking-widest flex items-center justify-center space-x-3 italic">
                            <i class="fas fa-circle-check"></i>
                            <span>Application Logged</span>
                        </div>
                    <?php else: ?>
                        <button onclick="openApplyModal(<?php echo $j['id']; ?>, '<?php echo $j['company']; ?>')" class="w-full py-5 bg-slate-900 dark:bg-indigo-600 text-white rounded-[2.5rem] font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:bg-indigo-600 dark:hover:bg-indigo-700 transition-all flex items-center justify-center space-x-3 italic active:scale-95">
                            <i class="fas fa-paper-plane"></i>
                            <span>Initiate Application</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Apply Modal -->
<div id="apply_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic mb-8" id="modal_title"></h3>
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="apply_job" value="1">
            <input type="hidden" name="placement_id" id="modal_placement_id">
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic ml-2">Resume Submission (PDF) *</label>
                <div class="p-8 border-2 border-dashed border-slate-100 dark:border-slate-700 rounded-[2.5rem] bg-slate-50 dark:bg-slate-900 text-center relative group">
                    <input type="file" name="resume" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <i class="fas fa-file-pdf text-3xl text-slate-200 dark:text-slate-700 group-hover:text-indigo-600 transition-colors mb-4"></i>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic group-hover:text-indigo-600">Select Digital Credentials</p>
                </div>
            </div>

            <div class="flex gap-6 pt-4">
                <button type="button" onclick="document.getElementById('apply_modal').classList.add('hidden')" class="flex-1 py-5 bg-slate-50 dark:bg-slate-900 text-slate-400 font-black rounded-[2.5rem] uppercase tracking-widest text-[10px] italic">Discard</button>
                <button type="submit" class="flex-1 py-5 bg-indigo-600 text-white font-black rounded-[2.5rem] uppercase tracking-widest text-[10px] italic shadow-xl shadow-indigo-500/30">Submit Profile</button>
            </div>
        </form>
    </div>
</div>

<script>
function openApplyModal(id, company) {
    document.getElementById('modal_placement_id').value = id;
    document.getElementById('modal_title').textContent = 'Apply to ' + company;
    document.getElementById('apply_modal').classList.remove('hidden');
    document.getElementById('apply_modal').classList.add('flex');
}
</script>

<?php require_once 'includes/footer.php'; ?>
