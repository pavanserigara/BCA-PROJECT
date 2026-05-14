<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('student')) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$stmt_std = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt_std->execute([$student_id]);
$student = $stmt_std->fetch();

$page_title = "Academic Discovery";
require_once 'includes/header.php';

$materials = $pdo->prepare("SELECT m.*, s.name as subject_name, s.code, u.full_name as teacher_name 
                            FROM study_materials m 
                            JOIN subjects s ON m.subject_id = s.id 
                            JOIN users u ON m.teacher_id = u.id 
                            WHERE s.course_id = ? AND s.semester = ? 
                            ORDER BY m.created_at DESC");
$materials->execute([$student['course_id'], $student['semester']]);
$all_materials = $materials->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Resource Hub</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Discover and acquire educational assets synchronized with your academic phase.</p>
    </div>
    
    <div class="flex items-center space-x-4">
        <div class="w-14 h-14 bg-primary-600 rounded-3xl flex items-center justify-center text-white shadow-premium animate-pulse">
            <i class="fas fa-satellite-dish"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 pb-20">
    <?php if (empty($all_materials)): ?>
        <div class="md:col-span-2 lg:col-span-3 py-20 bg-white dark:bg-slate-800 rounded-[4rem] text-center border-2 border-dashed border-slate-100 dark:border-slate-700 shadow-premium">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                <i class="fas fa-ghost text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white uppercase italic">Hub Offline</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 font-bold italic">No academic resources have been distributed for this phase yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($all_materials as $m): 
            $icon = 'fa-file-lines';
            $color = 'text-blue-500';
            if($m['material_type'] == 'PPT') { $icon = 'fa-file-powerpoint'; $color = 'text-orange-500'; }
            if($m['material_type'] == 'Link') { $icon = 'fa-link'; $color = 'text-indigo-500'; }
            if($m['material_type'] == 'Assignment') { $icon = 'fa-file-signature'; $color = 'text-emerald-500'; }
        ?>
            <div class="bg-white dark:bg-slate-800 p-10 rounded-[3.5rem] shadow-soft border border-slate-50 dark:border-slate-700/50 group hover:shadow-premium transition-all duration-500 relative flex flex-col justify-between overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-slate-50 dark:bg-slate-900/50 rounded-full group-hover:scale-110 transition-transform"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div class="w-14 h-14 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center <?php echo $color; ?> shadow-inner text-2xl group-hover:scale-110 transition-transform">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest italic"><?php echo $m['code']; ?></span>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight leading-snug mb-4 italic uppercase group-hover:text-primary-600 transition-colors">
                        <?php echo $m['title']; ?>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed italic mb-10 line-clamp-2">
                        <?php echo $m['description']; ?>
                    </p>
                    
                    <div class="flex items-center space-x-3 mb-10">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-400">
                            <?php echo substr($m['teacher_name'], 0, 1); ?>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 italic uppercase"><?php echo $m['teacher_name']; ?></span>
                    </div>
                </div>

                <div class="relative z-10">
                    <?php if ($m['file_path']): ?>
                        <a href="../uploads/<?php echo $m['file_path']; ?>" target="_blank" class="w-full py-5 bg-slate-900 dark:bg-primary-600 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:bg-primary-600 dark:hover:bg-primary-700 transition-all flex items-center justify-center space-x-3 italic active:scale-95">
                            <i class="fas fa-download"></i>
                            <span>Acquire Asset</span>
                        </a>
                    <?php else: ?>
                        <button class="w-full py-5 bg-slate-50 dark:bg-slate-900 text-slate-400 rounded-[2rem] font-black text-[10px] uppercase tracking-widest cursor-not-allowed flex items-center justify-center space-x-3 italic">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Reference Link</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/header.php'; ?>
