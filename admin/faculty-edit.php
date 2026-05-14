<?php
$page_title = "Update Faculty Record";
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: faculty-list.php");
    exit();
}

// Fetch Faculty + User
$stmt = $pdo->prepare("SELECT u.*, t.* 
                       FROM users u 
                       JOIN teachers t ON u.id = t.user_id 
                       WHERE u.id = ?");
$stmt->execute([$id]);
$faculty = $stmt->fetch();

if (!$faculty) {
    header("Location: faculty-list.php");
    exit();
}

// Fetch Departments
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    // Basic Details
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $username = sanitize($_POST['username']);
    $status = sanitize($_POST['status']);

    // Professional Details
    $employee_id = sanitize($_POST['employee_id']);
    $dept_id = $_POST['dept_id'];
    $designation = sanitize($_POST['designation']);
    $qualification = sanitize($_POST['qualification']);
    $experience = sanitize($_POST['experience']);
    $joining_date = $_POST['joining_date'];

    try {
        $pdo->beginTransaction();

        // Handle Profile Picture Update
        $profile_pic = $faculty['profile_pic'];
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = 'faculty_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../uploads/profiles/' . $profile_pic)) {
                if ($faculty['profile_pic'] && $faculty['profile_pic'] !== 'default_profile.svg' && file_exists('../uploads/profiles/' . $faculty['profile_pic'])) {
                    unlink('../uploads/profiles/' . $faculty['profile_pic']);
                }
            }
        }

        // 1. Update User
        $stmt_user = $pdo->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, status = ?, profile_pic = ? WHERE id = ?");
        $stmt_user->execute([$username, $email, $full_name, $status, $profile_pic, $id]);

        // 2. Update Teacher record
        $stmt_teacher = $pdo->prepare("UPDATE teachers SET employee_id = ?, dept_id = ?, designation = ?, qualification = ?, experience = ?, joining_date = ? 
                                       WHERE user_id = ?");
        $stmt_teacher->execute([$employee_id, $dept_id, $designation, $qualification, $experience, $joining_date, $id]);

        $pdo->commit();
        header("Location: faculty-view.php?id=" . $id . "&success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Update failed: " . $e->getMessage();
    }
}
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-12">
        <div class="flex items-center space-x-6">
            <a href="faculty-view.php?id=<?php echo $id; ?>"
                class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 transition-all border border-slate-100 shadow-sm">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Modify Faculty Record</h2>
                <p class="text-sm text-slate-400 font-medium italic mt-1">Updating institutional credentials for <?php echo $faculty['full_name']; ?>.</p>
            </div>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-6 rounded-2xl mb-8 flex items-center">
            <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
            <div>
                <p class="font-bold">Protocol Breach</p>
                <p class="text-sm"><?php echo $error_message; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form action="faculty-edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="space-y-10 pb-20">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        
        <!-- Identity & Authentication -->
        <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-600/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700"></div>
            
            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div class="w-16 h-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-200">
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Institutional Identity</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Authentication parameters and profile visualization.</p>
                </div>
            </div>

            <div class="flex flex-col items-center mb-12 space-y-6 relative z-10">
                <div class="relative group/avatar">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-slate-100 overflow-hidden border-4 border-white shadow-xl">
                        <?php 
                            $pic = $faculty['profile_pic'];
                            $pic_url = '../assets/images/default_profile.svg';
                            if ($pic && $pic !== 'default_profile.svg' && file_exists('../uploads/profiles/' . $pic)) {
                                $pic_url = '../uploads/profiles/' . $pic;
                            }
                        ?>
                        <img id="avatarPreview" src="<?php echo $pic_url; ?>" class="w-full h-full object-cover" alt="Profile Preview">
                    </div>
                    <label for="profile_pic" class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white cursor-pointer shadow-lg hover:scale-110 transition-all border-2 border-white">
                        <i class="fas fa-camera text-sm"></i>
                        <input type="file" id="profile_pic" name="profile_pic" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Identity Visualization (Optional)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Legal Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($faculty['full_name']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Institutional Email Flow</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($faculty['email']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Username Identity</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($faculty['username']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Employment Status</label>
                    <select name="status" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <option value="active" <?php echo $faculty['status'] === 'active' ? 'selected' : ''; ?>>Active Duty</option>
                        <option value="inactive" <?php echo $faculty['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive / On Leave</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Professional Dossier -->
        <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-amber-100/30 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-40 h-40 bg-amber-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700"></div>
            
            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div class="w-16 h-16 bg-amber-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-amber-200">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Professional Dossier</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Employment details and academic responsibilities.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Employee Identifier</label>
                    <input type="text" name="employee_id" value="<?php echo htmlspecialchars($faculty['employee_id']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Assigned Department</label>
                    <select name="dept_id" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo $faculty['dept_id'] == $dept['id'] ? 'selected' : ''; ?>>
                                <?php echo $dept['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Academic Designation</label>
                    <input type="text" name="designation" value="<?php echo htmlspecialchars($faculty['designation']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Institutional Joining Date</label>
                    <input type="date" name="joining_date" value="<?php echo $faculty['joining_date']; ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Academic Qualifications</label>
                    <textarea name="qualification" rows="2"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base"><?php echo htmlspecialchars($faculty['qualification']); ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Prior Experience Repository</label>
                    <textarea name="experience" rows="2"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base"><?php echo htmlspecialchars($faculty['experience']); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="bg-slate-900 p-10 rounded-[4rem] shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center space-x-6">
                <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black italic text-xl shadow-lg shadow-indigo-600/30">V</div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic leading-none mb-1">Administrative Authorization</p>
                    <p class="text-white font-black text-lg italic tracking-tight">Synchronize changes to institutional registry?</p>
                </div>
            </div>
            <div class="flex items-center gap-6 w-full md:w-auto">
                <a href="faculty-view.php?id=<?php echo $id; ?>" 
                   class="flex-1 md:flex-auto px-10 py-5 bg-slate-800 hover:bg-slate-700 text-slate-400 rounded-[2rem] font-black text-[10px] tracking-widest uppercase transition-all border border-slate-700 italic text-center">
                    Discard
                </a>
                <button type="submit" 
                        class="flex-1 md:flex-auto px-14 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[2rem] font-black text-[10px] tracking-widest uppercase shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:-translate-y-1 transition-all italic">
                    <i class="fas fa-sync mr-2"></i> Update Record
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>
