<?php
$page_title = "Register New Student";
require_once 'includes/header.php';

// Fetch Departments and Courses for dropdowns
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
$courses = $pdo->query("SELECT * FROM courses ORDER BY name")->fetchAll();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic Details
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Academic Details
    $roll_no = sanitize($_POST['roll_no']);
    $course_id = $_POST['course_id'];
    $semester = $_POST['semester'];
    $admission_date = $_POST['admission_date'];

    // Personal Details
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $parent_name = sanitize($_POST['parent_name']);
    $parent_phone = sanitize($_POST['parent_phone']);

    try {
        $pdo->beginTransaction();

        // 1. Create User
        $stmt_user = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, 'student', ?)");
        $stmt_user->execute([$username, $email, $password, $full_name]);
        $user_id = $pdo->lastInsertId();

        // 2. Create Student record
        $stmt_student = $pdo->prepare("INSERT INTO students (user_id, roll_no, course_id, semester, admission_date, dob, gender, phone, address, parent_name, parent_phone) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_student->execute([$user_id, $roll_no, $course_id, $semester, $admission_date, $dob, $gender, $phone, $address, $parent_name, $parent_phone]);

        $pdo->commit();
        $success_message = "Student registered successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Registration failed: " . $e->getMessage();
    }
}
?>

<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Register Student</h2>
            <p class="text-slate-500 font-medium">Create a new student profile and access credentials.</p>
        </div>
        <a href="students-list.php"
            class="text-slate-500 hover:text-indigo-600 font-bold flex items-center space-x-2 transition-all">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Directory</span>
        </a>
    </div>

    <?php if ($success_message): ?>
        <div
            class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center animate__animated animate__fadeInDown">
            <i class="fas fa-check-circle text-2xl mr-4"></i>
            <div>
                <p class="font-bold">Success!</p>
                <p class="text-sm">
                    <?php echo $success_message; ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div
            class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-6 rounded-2xl mb-8 flex items-center animate__animated animate__shakeX">
            <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
            <div>
                <p class="font-bold">Error Occurred</p>
                <p class="text-sm">
                    <?php echo $error_message; ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <form action="students-add.php" method="POST" class="space-y-8 pb-20">
        <!-- Account Information -->
        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50">
            <div class="flex items-center space-x-4 mb-8">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-user-lock text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Account Credentials</h3>
                    <p class="text-sm text-slate-500">Authentication details for the student portal.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" required placeholder="e.g. Rahul Sharma"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="rahul@example.com"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="username">Username *</label>
                    <input type="text" id="username" name="username" required placeholder="rahul_2025"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="password">Initial Password *</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
            </div>
        </div>

        <!-- Academic Details -->
        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50">
            <div class="flex items-center space-x-4 mb-8">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Academic Records</h3>
                    <p class="text-sm text-slate-500">Enrollment and course assignment information.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="roll_no">Roll Number *</label>
                    <input type="text" id="roll_no" name="roll_no" required placeholder="BCA/2025/042"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium uppercase">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="course_id">Course Assignment
                        *</label>
                    <select id="course_id" name="course_id" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>">
                                <?php echo $course['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="semester">Current Semester *</label>
                    <select id="semester" name="semester" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                        <option value="3">Semester 3</option>
                        <option value="4">Semester 4</option>
                        <option value="5">Semester 5</option>
                        <option value="6">Semester 6</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="admission_date">Date of Admission
                        *</label>
                    <input type="date" id="admission_date" name="admission_date" required
                        value="<?php echo date('Y-m-d'); ?>"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50">
            <div class="flex items-center space-x-4 mb-8">
                <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                    <i class="fas fa-id-card text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Personal Profile</h3>
                    <p class="text-sm text-slate-500">Contact and demographic information.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="gender">Gender</label>
                    <select id="gender" name="gender"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="phone">Personal Phone</label>
                    <input type="text" id="phone" name="phone" placeholder="+91 00000 00000"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="parent_name">Parent/Guardian
                        Name</label>
                    <input type="text" id="parent_name" name="parent_name" placeholder="Parents Name"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3" for="address">Permanent Address</label>
                    <textarea id="address" name="address" rows="3" placeholder="Enter full residential address..."
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-6">
            <button type="reset"
                class="px-10 py-5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-[2rem] font-black text-xs tracking-widest uppercase transition-all">
                Reset Form
            </button>
            <button type="submit"
                class="px-12 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[2rem] font-black text-xs tracking-widest uppercase shadow-xl shadow-indigo-100 hover:shadow-indigo-200 hover:-translate-y-1 transition-all">
                Finalize Enrollment
            </button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>