<?php
$page_title = "Fee Configuration";
require_once 'includes/header.php';

// Handle Fee Component Addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fee_component'])) {
    csrf_guard();
    $course_id = (int) $_POST['course_id'];
    $semester = (int) $_POST['semester'];
    $title = sanitize($_POST['title']);
    $amount = (float) $_POST['amount'];

    try {
        $stmt = $pdo->prepare("INSERT INTO fees_structure (course_id, semester, title, amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_id, $semester, $title, $amount]);
        set_flash_message('success', "Fee component '$title' added to protocol.");
    } catch (PDOException $e) {
        set_flash_message('error', 'Configuration error: ' . $e->getMessage());
    }
}

// Handle Deletion
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $pdo->prepare("DELETE FROM fees_structure WHERE id = ?")->execute([$id]);
    header("Location: fees-structure.php");
    exit();
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY name ASC")->fetchAll();
$structures = $pdo->query("SELECT fs.*, c.name as course_name FROM fees_structure fs JOIN courses c ON fs.course_id = c.id ORDER BY c.name, fs.semester")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Fiscal Architecture</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Defining mandatory financial components for academic synchronization.</p>
    </div>
    
    <button onclick="document.getElementById('fee_modal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-[2.5rem] font-black text-xs uppercase tracking-widest shadow-premium transition-all transform active:scale-95 flex items-center space-x-3 italic">
        <i class="fas fa-plus-circle"></i>
        <span>Define New Component</span>
    </button>
</div>

<?php display_flash_message(); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <div class="lg:col-span-2 space-y-8">
        <?php 
        $grouped = [];
        foreach ($structures as $s) {
            $grouped[$s['course_name']][$s['semester']][] = $s;
        }

        if (empty($grouped)):
        ?>
            <div class="bg-white p-20 rounded-[4rem] text-center border border-slate-100 shadow-premium">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                    <i class="fas fa-box-archive text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 italic uppercase">Treasury Empty</h3>
                <p class="text-slate-400 mt-2 italic">No fee components have been architected yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($grouped as $course => $semesters): ?>
                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                        <h4 class="text-lg font-black text-slate-800 uppercase italic leading-none"><?php echo $course; ?></h4>
                        <span class="text-[10px] font-black text-primary-600 uppercase tracking-widest italic">Academic Protocol</span>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <?php foreach ($semesters as $sem => $components): 
                            $sem_total = array_sum(array_column($components, 'amount'));
                        ?>
                            <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 group hover:bg-white hover:shadow-xl transition-all duration-500">
                                <div class="flex items-center justify-between mb-6">
                                    <h5 class="text-sm font-black text-slate-400 uppercase tracking-widest italic">Semester <?php echo $sem; ?></h5>
                                    <span class="text-base font-black text-slate-800 tracking-tight italic">Total: ₹<?php echo number_format($sem_total, 2); ?></span>
                                </div>
                                
                                <div class="space-y-3">
                                    <?php foreach ($components as $c): ?>
                                        <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-50 group-hover:border-slate-100 transition-all">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-2 h-2 bg-primary-600 rounded-full"></div>
                                                <span class="text-xs font-bold text-slate-600 italic uppercase"><?php echo $c['title']; ?></span>
                                            </div>
                                            <div class="flex items-center space-x-6">
                                                <span class="text-sm font-black text-slate-800 italic">₹<?php echo number_format($c['amount'], 2); ?></span>
                                                <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Terminate this fiscal component?')" class="text-slate-300 hover:text-rose-600 transition-colors">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="space-y-8">
        <div class="bg-indigo-600 p-10 rounded-[3rem] text-white shadow-premium relative overflow-hidden group">
            <h4 class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-6 italic">Fiscal Overview</h4>
            <div class="text-4xl font-black tracking-tight italic leading-none mb-10">Institutional Treasury</div>
            <p class="text-xs font-medium text-indigo-100 leading-relaxed italic opacity-80 mb-8">Maintain strict financial synchronization by defining clear fee components for each academic period.</p>
            <div class="w-20 h-20 bg-white/10 rounded-full absolute -right-4 -bottom-4 animate-pulse"></div>
        </div>
        
        <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-soft">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic">Protocol Guidelines</h4>
            <ul class="space-y-4">
                <li class="flex items-start space-x-3 text-[11px] font-bold text-slate-500 italic">
                    <i class="fas fa-circle-check text-emerald-500 mt-0.5"></i>
                    <span>Fees are calculated per semester.</span>
                </li>
                <li class="flex items-start space-x-3 text-[11px] font-bold text-slate-500 italic">
                    <i class="fas fa-circle-check text-emerald-500 mt-0.5"></i>
                    <span>Ensure titles are clear (e.g. Tuition Fee).</span>
                </li>
                <li class="flex items-start space-x-3 text-[11px] font-bold text-slate-500 italic">
                    <i class="fas fa-circle-check text-emerald-500 mt-0.5"></i>
                    <span>Values must be in valid numeric format.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="fee_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white w-full max-w-xl rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <div class="flex items-center justify-between mb-12">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Define Fiscal Component</h3>
            <button onclick="document.getElementById('fee_modal').classList.add('hidden')" class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-600 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="add_fee_component" value="1">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Target Course *</label>
                    <select name="course_id" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                        <?php foreach($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Semester *</label>
                        <select name="semester" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                            <?php for($i=1;$i<=8;$i++): ?>
                                <option value="<?php echo $i; ?>">Sem <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Fiscal Value (₹) *</label>
                        <input type="number" name="amount" required step="0.01" placeholder="0.00" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Component Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Tuition Fee" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6">
                <button type="button" onclick="document.getElementById('fee_modal').classList.add('hidden')" class="flex-1 py-6 bg-slate-50 text-slate-400 font-black rounded-[2rem] hover:bg-slate-100 transition-all uppercase tracking-widest text-[10px] italic">Discard</button>
                <button type="submit" class="flex-2 px-15 py-6 bg-primary-600 text-white font-black rounded-[2rem] shadow-premium hover:bg-primary-700 transition-all uppercase tracking-widest text-[10px] italic">Architect Component</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
