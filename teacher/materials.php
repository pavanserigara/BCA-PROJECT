<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_material'])) {
    csrf_guard();
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $subject_id = (int)$_POST['subject_id'];
    $type = sanitize($_POST['material_type']);
    
    $file_path = NULL;
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] === 0) {
        $upload_dir = '../uploads/materials/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = 'material_' . time() . '_' . $_FILES['material_file']['name'];
        if (move_uploaded_file($_FILES['material_file']['tmp_name'], $upload_dir . $filename)) {
            $file_path = 'materials/' . $filename;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO study_materials (subject_id, teacher_id, title, description, file_path, material_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$subject_id, $teacher_id, $title, $description, $file_path, $type]);
        set_flash_message('success', 'Study material published successfully!');
    } catch (PDOException $e) {
        set_flash_message('error', "Upload failed: " . $e->getMessage());
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM study_materials WHERE id = ? AND teacher_id = ?")->execute([$id, $teacher_id]);
    header("Location: materials.php");
    exit();
}

$page_title = "Resource Repository";
require_once 'includes/header.php';

$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

$materials = $pdo->prepare("SELECT m.*, s.name as subject_name 
                            FROM study_materials m 
                            JOIN subjects s ON m.subject_id = s.id 
                            WHERE m.teacher_id = ? ORDER BY m.created_at DESC");
$materials->execute([$teacher_id]);
$my_materials = $materials->fetchAll();
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Academic Vault</h2>
        <p class="text-slate-500 dark:text-slate-400 font-medium mt-4 italic">Distributing high-fidelity educational protocols and reference assets.</p>
    </div>
    
    <button onclick="document.getElementById('upload_modal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-[2.5rem] font-black text-xs uppercase tracking-widest shadow-premium transition-all transform active:scale-95 flex items-center space-x-3 italic">
        <i class="fas fa-cloud-arrow-up"></i>
        <span>Publish Resource</span>
    </button>
</div>

<?php display_flash_message(); ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (empty($my_materials)): ?>
        <div class="md:col-span-2 lg:col-span-3 py-20 bg-white dark:bg-slate-800 rounded-[3.5rem] text-center border-2 border-dashed border-slate-100 dark:border-slate-700 shadow-premium">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                <i class="fas fa-book-open-reader text-3xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase italic">Vault Depleted</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 italic">Start contributing to the academic knowledge base.</p>
        </div>
    <?php else: ?>
        <?php foreach ($my_materials as $m): ?>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-[3rem] shadow-soft border border-slate-50 dark:border-slate-700/50 group hover:shadow-premium hover:-translate-y-1 transition-all duration-500 relative flex flex-col justify-between overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-600/5 rounded-full group-hover:scale-150 transition-transform"></div>
                
                <div>
                    <div class="flex items-center justify-between mb-6 relative z-10">
                        <span class="text-[9px] font-black text-primary-600 uppercase tracking-widest bg-primary-50 dark:bg-primary-500/10 px-3 py-1.5 rounded-xl border border-primary-100 dark:border-primary-500/20 italic">
                            <?php echo $m['subject_name']; ?>
                        </span>
                        <div class="flex items-center space-x-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic"><?php echo $m['material_type']; ?></span>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-black text-slate-800 dark:text-white leading-tight mb-3 italic uppercase group-hover:text-primary-600 transition-colors">
                        <?php echo $m['title']; ?>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed italic mb-8 line-clamp-3">
                        <?php echo $m['description']; ?>
                    </p>
                </div>

                <div class="pt-6 border-t border-slate-50 dark:border-slate-700/50 flex items-center justify-between relative z-10">
                    <div class="flex items-center space-x-3">
                        <?php if ($m['file_path']): ?>
                            <a href="../uploads/<?php echo $m['file_path']; ?>" target="_blank" class="w-10 h-10 bg-slate-900 dark:bg-primary-600 text-white rounded-xl flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
                                <i class="fas fa-download text-xs"></i>
                            </a>
                        <?php else: ?>
                            <div class="w-10 h-10 bg-slate-50 dark:bg-slate-900 text-slate-300 rounded-xl flex items-center justify-center">
                                <i class="fas fa-link text-xs"></i>
                            </div>
                        <?php endif; ?>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic"><?php echo date('M d, Y', strtotime($m['created_at'])); ?></span>
                    </div>
                    
                    <a href="?delete=<?php echo $m['id']; ?>" onclick="return confirm('Purge this resource from the vault?')" class="w-9 h-9 text-slate-300 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl flex items-center justify-center transition-all">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="upload_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white dark:bg-slate-800 w-full max-w-xl rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <div class="flex items-center justify-between mb-12">
            <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight italic uppercase">Publish Resource</h3>
            <button onclick="document.getElementById('upload_modal').classList.add('hidden')" class="w-12 h-12 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-400 hover:text-rose-600 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="upload_material" value="1">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Target Subject *</label>
                    <select name="subject_id" required class="w-full px-8 py-5 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-slate-900 transition-all outline-none font-black text-slate-800 dark:text-white italic">
                        <?php foreach($my_subjects as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?> (<?php echo $s['course_name']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Material Type *</label>
                        <select name="material_type" required class="w-full px-8 py-5 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 transition-all outline-none font-black text-slate-800 dark:text-white italic">
                            <option value="Notes">Lecture Notes</option>
                            <option value="PPT">Presentation</option>
                            <option value="Link">External Resource</option>
                            <option value="Assignment">Task Brief</option>
                            <option value="Other">Miscellaneous</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">File Attachment</label>
                        <input type="file" name="material_file" class="w-full px-4 py-4 text-xs font-black text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Resource Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Unit 1: Introduction to Calculus" class="w-full px-8 py-5 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 transition-all outline-none font-black text-slate-800 dark:text-white italic">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Description / Instructions</label>
                    <textarea name="description" rows="4" placeholder="Summary of the resource..." class="w-full px-8 py-5 bg-slate-50 dark:bg-slate-900 border-none rounded-[2.5rem] focus:ring-4 focus:ring-primary-500/10 transition-all outline-none font-black text-slate-800 dark:text-white italic"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6">
                <button type="button" onclick="document.getElementById('upload_modal').classList.add('hidden')" class="flex-1 py-6 bg-slate-50 dark:bg-slate-900 text-slate-400 font-black rounded-[2rem] hover:bg-slate-100 transition-all uppercase tracking-widest text-[10px] italic">Discard</button>
                <button type="submit" class="flex-2 px-15 py-6 bg-primary-600 text-white font-black rounded-[2rem] shadow-premium hover:bg-primary-700 transition-all uppercase tracking-widest text-[10px] italic">Publish to Vault</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
