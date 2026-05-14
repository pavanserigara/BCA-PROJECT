<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('admin')) {
    header("Location: ../login.php");
    exit();
}

// Handle Parent Creation & Linking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_parent'])) {
    csrf_guard();
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $full_name = sanitize($_POST['full_name']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $student_id = (int)$_POST['student_id'];
    $relation = sanitize($_POST['relation']);

    try {
        $pdo->beginTransaction();
        
        // Create user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, 'parent', ?)");
        $stmt->execute([$username, $email, $password, $full_name]);
        $parent_user_id = $pdo->lastInsertId();
        
        // Create parent link
        $stmt = $pdo->prepare("INSERT INTO parents (user_id, student_id, relation) VALUES (?, ?, ?)");
        $stmt->execute([$parent_user_id, $student_id, $relation]);
        
        $pdo->commit();
        set_flash_message('success', 'Parent account created and linked successfully.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_flash_message('error', 'Operation failed: ' . $e->getMessage());
    }
}

$page_title = "Guardian Management";
require_once 'includes/header.php';

$parents = $pdo->query("SELECT p.*, u.full_name as parent_name, u.username, u.email, u.profile_pic, s_u.full_name as student_name, s.roll_no 
                        FROM parents p 
                        JOIN users u ON p.user_id = u.id 
                        JOIN students s ON p.student_id = s.user_id 
                        JOIN users s_u ON s.user_id = s_u.id 
                        ORDER BY u.created_at DESC")->fetchAll();

$students = $pdo->query("SELECT s.user_id, u.full_name, s.roll_no 
                         FROM students s 
                         JOIN users u ON s.user_id = u.id 
                         ORDER BY u.full_name ASC")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Stakeholder Registry</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Managing parent accounts and institutional-student link protocols.</p>
    </div>
    
    <button onclick="document.getElementById('parent_modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-premium transition-all italic flex items-center space-x-3">
        <i class="fas fa-user-plus"></i>
        <span>Register Guardian</span>
    </button>
</div>

<?php display_flash_message(); ?>

<div class="bg-white rounded-[4rem] shadow-premium border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="py-8 px-10">Guardian Profile</th>
                    <th class="py-8 px-10">Linked Student</th>
                    <th class="py-8 px-10">Relation</th>
                    <th class="py-8 px-10 text-right pr-10">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($parents)): ?>
                    <tr>
                        <td colspan="4" class="py-20 text-center font-black text-slate-300 italic uppercase tracking-widest">No guardian protocols established.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($parents as $p): ?>
                        <tr class="group hover:bg-slate-50/50 transition-all">
                            <td class="py-8 px-10">
                                <div class="flex items-center space-x-4">
                                    <?php 
                                        $pic_url = '../assets/images/default_profile.svg';
                                        if (!empty($p['profile_pic'])) {
                                            if (is_file(__DIR__ . '/../../uploads/profiles/' . $p['profile_pic'])) {
                                                $pic_url = '../uploads/profiles/' . $p['profile_pic'];
                                            } elseif (is_file(__DIR__ . '/../../assets/images/' . $p['profile_pic'])) {
                                                $pic_url = '../assets/images/' . $p['profile_pic'];
                                            }
                                        }
                                    ?>
                                    <img src="<?php echo $pic_url; ?>" class="w-10 h-10 rounded-xl object-cover shadow-sm" alt="">
                                    <div>
                                        <p class="text-sm font-black text-slate-800 uppercase italic leading-none mb-1"><?php echo $p['parent_name']; ?></p>
                                        <p class="text-[10px] font-bold text-slate-400 italic"><?php echo $p['email']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-8 px-10">
                                <p class="text-xs font-black text-slate-700 uppercase italic leading-none mb-1"><?php echo $p['student_name']; ?></p>
                                <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-widest"><?php echo $p['roll_no']; ?></p>
                            </td>
                            <td class="py-8 px-10">
                                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase tracking-widest italic"><?php echo $p['relation']; ?></span>
                            </td>
                            <td class="py-8 px-10 text-right pr-10">
                                <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-300 hover:text-rose-500 transition-all">
                                    <i class="fas fa-trash-can text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Parent Modal -->
<div id="parent_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white w-full max-w-2xl rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase leading-none">Register Guardian</h3>
            <button onclick="document.getElementById('parent_modal').classList.add('hidden')" class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-600 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="add_parent" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Full Name *</label>
                    <input type="text" name="full_name" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 font-black italic outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Username *</label>
                    <input type="text" name="username" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 font-black italic outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Email Address *</label>
                    <input type="email" name="email" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 font-black italic outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Initial Password *</label>
                    <input type="password" name="password" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 font-black italic outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Linked Student *</label>
                    <select name="student_id" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 font-black italic outline-none">
                        <?php foreach($students as $s): ?>
                            <option value="<?php echo $s['user_id']; ?>"><?php echo $s['full_name']; ?> (<?php echo $s['roll_no']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Relation Protocol *</label>
                    <select name="relation" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 font-black italic outline-none">
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6">
                <button type="button" onclick="document.getElementById('parent_modal').classList.add('hidden')" class="flex-1 py-6 bg-slate-50 text-slate-400 font-black rounded-[2rem] hover:bg-slate-100 transition-all uppercase tracking-widest text-[10px] italic">Discard</button>
                <button type="submit" class="flex-2 px-15 py-6 bg-indigo-600 text-white font-black rounded-[2rem] shadow-premium hover:bg-indigo-700 transition-all uppercase tracking-widest text-[10px] italic">Authorize Access</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
