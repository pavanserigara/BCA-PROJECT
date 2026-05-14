<?php
$page_title = "Institutional Staff Directory";
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

// Add Staff
if (isset($_POST['add_staff'])) {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $status = 'active';

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $username, $password, $role, $status]);
        $success_message = "$full_name added to the $role directory.";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

$staff_members = $pdo->query("SELECT * FROM users WHERE role IN ('staff', 'librarian', 'accountant') ORDER BY role, full_name")->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Support Staff & Admin</h2>
        <p class="text-slate-500 font-medium">Manage institutional support roles including Librarians and Accountants.
        </p>
    </div>

    <button onclick="document.getElementById('staff_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5 transform active:scale-95">
        <i class="fas fa-user-plus text-xs"></i>
        <span>Register Staff</span>
    </button>
</div>

<?php if ($success_message): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="bg-white rounded-[3rem] shadow-sm border border-indigo-100/50 overflow-hidden mb-20">
    <table class="w-full text-left">
        <thead>
            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                <th class="py-8 px-10">Staff Profile</th>
                <th class="py-8 px-10">Functional Role</th>
                <th class="py-8 px-10">Institutional ID</th>
                <th class="py-8 px-10 text-right">Actions</th>
            </tr>
        </thead>
    <tbody class="divide-y divide-indigo-50/20">
        <?php if (empty($staff_members)): ?>
            <tr>
                <td colspan="4" class="p-20 text-center italic text-slate-400">Support directory is currently empty.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($staff_members as $s): ?>
                <tr class="group hover:bg-slate-50 transition-all">
                    <td class="py-8 px-10">
                        <div class="flex items-center space-x-4">
                            <?php 
                                $pic_url = '../assets/images/default_profile.svg';
                                if (!empty($s['profile_pic'])) {
                                    if (is_file(__DIR__ . '/../../uploads/profiles/' . $s['profile_pic'])) {
                                        $pic_url = '../uploads/profiles/' . $s['profile_pic'];
                                    } elseif (is_file(__DIR__ . '/../../assets/images/' . $s['profile_pic'])) {
                                        $pic_url = '../assets/images/' . $s['profile_pic'];
                                    }
                                }
                            ?>
                            <img src="<?php echo $pic_url; ?>" class="w-10 h-10 rounded-xl object-cover shadow-sm" alt="">
                            <div>
                                <h6
                                    class="text-sm font-bold text-slate-800 tracking-tight leading-none mb-1 text-slate-800">
                                    <?php echo $s['full_name']; ?>
                                </h6>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">
                                        <?php echo $s['email']; ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-8 px-10">
                            <span
                                class="px-3 py-1.5 <?php echo $s['role'] == 'accountant' ? 'bg-amber-50 text-amber-600 border-amber-100' : ($s['role'] == 'staff' ? 'bg-slate-50 text-slate-500 border-slate-200' : 'bg-indigo-50 text-indigo-500 border-indigo-100'); ?> border rounded-lg text-[10px] font-black uppercase tracking-widest leading-none shadow-sm">
                                <?php echo $s['role']; ?>
                            </span>
                        </td>
                        <td class="py-8 px-10">
                            <span class="text-xs font-black text-slate-400 tracking-widest">ID-VST-
                                <?php echo str_pad($s['id'], 3, '0', STR_PAD_LEFT); ?>
                            </span>
                        </td>
                        <td class="py-8 px-10 text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <button
                                    class="w-10 h-10 border border-slate-100 bg-white rounded-xl text-slate-300 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </button>
                                <button
                                    class="w-10 h-10 border border-slate-100 bg-white rounded-xl text-slate-300 hover:text-rose-600 hover:border-rose-600 transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="staff_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl p-10 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-2xl font-black text-slate-800">Support Registration</h3>
            <button onclick="document.getElementById('staff_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="staff-list.php" method="POST" class="space-y-6">
            <input type="hidden" name="add_staff" value="1">
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Full Legal
                        Name</label>
                    <input type="text" name="full_name" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Institutional
                        Email</label>
                    <input type="email" name="email" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Username
                        Flow</label>
                    <input type="text" name="username" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Functional
                        Role</label>
                    <select name="role" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-700">
                        <option value="staff">Administrative Staff</option>
                        <option value="librarian">Chief Librarian</option>
                        <option value="accountant">Senior Accountant</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Portal
                        Password</label>
                    <input type="password" name="password" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                </div>
            </div>
            <button type="submit"
                class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">Authorize
                Enrollment</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>