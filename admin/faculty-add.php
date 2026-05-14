<?php
$page_title = "Recruit New Faculty";
require_once 'includes/header.php';

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
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Professional Details
    $employee_id = sanitize($_POST['employee_id']);
    $dept_id = $_POST['dept_id'];
    $designation = sanitize($_POST['designation']);
    $qualification = sanitize($_POST['qualification']);
    $experience = sanitize($_POST['experience']);
    $joining_date = $_POST['joining_date'];

    try {
        $pdo->beginTransaction();

        // Handle Profile Picture Upload
        $profile_pic = 'default_profile.svg';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = 'faculty_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../uploads/profiles/' . $profile_pic);
        }

        // 1. Create User
        $stmt_user = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, profile_pic) VALUES (?, ?, ?, 'teacher', ?, ?)");
        $stmt_user->execute([$username, $email, $password, $full_name, $profile_pic]);
        $user_id = $pdo->lastInsertId();

        // 2. Create Teacher record
        $stmt_teacher = $pdo->prepare("INSERT INTO teachers (user_id, employee_id, dept_id, designation, qualification, experience, joining_date) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_teacher->execute([$user_id, $employee_id, $dept_id, $designation, $qualification, $experience, $joining_date]);

        $pdo->commit();
        header("Location: faculty-list.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Recruitment failed: " . $e->getMessage();
    }
}
?>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Recruit Faculty</h2>
            <p class="text-slate-500 font-medium">Capture academic credentials and assign professional responsibilities.
            </p>
        </div>
        <a href="faculty-list.php"
            class="text-slate-500 hover:text-indigo-600 font-bold flex items-center space-x-2 transition-all">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Directory</span>
        </a>
    </div>

    <?php if ($error_message): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-6 rounded-2xl mb-8 flex items-center">
            <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
            <div>
                <p class="font-bold">Error Occurred</p>
                <p class="text-sm">
                    <?php echo $error_message; ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <form action="faculty-add.php" method="POST" enctype="multipart/form-data" class="space-y-8 pb-20">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <!-- Profile Pic & Basic Auth -->
        <div class="bg-white p-10 md:p-12 rounded-[3.5rem] shadow-sm border border-indigo-100/50 relative overflow-hidden">
            <div class="flex items-center space-x-4 mb-10">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Authentication Setup</h3>
                    <p class="text-sm text-slate-500">Credentials and identity visualization.</p>
                </div>
            </div>

            <div class="flex flex-col items-center mb-10 space-y-4">
                <div class="relative group/avatar">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-slate-100 overflow-hidden border-4 border-white shadow-xl">
                        <img id="avatarPreview" src="../assets/images/default_profile.svg" class="w-full h-full object-cover" alt="Profile Preview">
                    </div>
                    <label for="profile_pic" class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white cursor-pointer shadow-lg hover:scale-110 transition-all border-2 border-white">
                        <i class="fas fa-camera text-sm"></i>
                        <input type="file" id="profile_pic" name="profile_pic" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Faculty Portrait (Optional)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 italic" for="full_name">Legal Name *</label>
                    <input type="text" id="full_name" name="full_name" required placeholder="Professor Name"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 italic" for="email">Institutional Email *</label>
                    <input type="email" id="email" name="email" required placeholder="faculty@vidyasetu.ac.in"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 italic" for="username">Username *</label>
                    <input type="text" id="username" name="username" required placeholder="faculty_id"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3 italic" for="password">Portal Password *</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
            </div>
        </div>

        <!-- Professional Details -->
        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50">
            <div class="flex items-center space-x-4 mb-8">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <i class="fas fa-briefcase text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Professional Dossier</h3>
                    <p class="text-sm text-slate-500">Employment details and academic background.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="employee_id">Employee ID *</label>
                    <input type="text" id="employee_id" name="employee_id" required placeholder="FAC/2025/110"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="dept_id">Assigned Department
                        *</label>
                    <select id="dept_id" name="dept_id" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>">
                                <?php echo $dept['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="designation">Academic Designation
                        *</label>
                    <input type="text" id="designation" name="designation" required
                        placeholder="e.g. Assistant Professor"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="joining_date">Date of Joining
                        *</label>
                    <input type="date" id="joining_date" name="joining_date" required
                        value="<?php echo date('Y-m-d'); ?>"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="qualification">Academic
                        Qualifications</label>
                    <textarea id="qualification" name="qualification" rows="2"
                        placeholder="e.g. PhD in Machine Learning, M.Tech from IIT..."
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="experience">Prior Experience</label>
                    <textarea id="experience" name="experience" rows="2"
                        placeholder="Detail any industry or academic experience..."
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-6">
            <button type="reset"
                class="px-10 py-5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-[2rem] font-black text-xs tracking-widest uppercase transition-all italic">
                Discard
            </button>
            <button type="submit"
                class="px-12 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[2rem] font-black text-xs tracking-widest uppercase shadow-xl shadow-indigo-100 hover:shadow-indigo-200 hover:-translate-y-1 transition-all italic">
                Authorize Personnel
            </button>
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