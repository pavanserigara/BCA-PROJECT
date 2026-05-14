<?php
$page_title = "Register New Student";
require_once 'includes/header.php';

// Fetch Departments and Courses for dropdowns
$departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
$courses = $pdo->query("SELECT * FROM courses ORDER BY name")->fetchAll();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
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

        // Handle Profile Picture Upload
        $profile_pic = 'default_profile.svg';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = 'student_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../uploads/profiles/' . $profile_pic);
        }

        // 1. Create User
        $stmt_user = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, profile_pic) VALUES (?, ?, ?, 'student', ?, ?)");
        $stmt_user->execute([$username, $email, $password, $full_name, $profile_pic]);
        $user_id = $pdo->lastInsertId();

        // 2. Create Student record
        $stmt_student = $pdo->prepare("INSERT INTO students (user_id, roll_no, course_id, semester, admission_date, dob, gender, phone, address, parent_name, parent_phone) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_student->execute([$user_id, $roll_no, $course_id, $semester, $admission_date, $dob, $gender, $phone, $address, $parent_name, $parent_phone]);

        // 3. Handle Institutional Documents
        $doc_types = ['aadhaar', 'sslc', 'puc', 'tc', 'photo'];
        foreach ($doc_types as $type) {
            if (isset($_FILES[$type]) && $_FILES[$type]['error'] === 0) {
                $ext = pathinfo($_FILES[$type]['name'], PATHINFO_EXTENSION);
                $doc_filename = 'doc_' . $type . '_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES[$type]['tmp_name'], '../uploads/documents/' . $doc_filename)) {
                    $stmt_doc = $pdo->prepare("INSERT INTO student_documents (student_id, document_type, file_path) VALUES (?, ?, ?)");
                    $stmt_doc->execute([$user_id, $type, $doc_filename]);
                }
            }
        }

        $pdo->commit();
        $success_message = "Student '$full_name' registered successfully with Roll No: $roll_no! Protocols synchronized.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Registration failed: " . $e->getMessage();
    }
}
?>

<div class="max-w-7xl mx-auto pb-20">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
        <div>
            <div class="flex items-center space-x-4 mb-6">
                <a href="students-list.php"
                    class="w-12 h-12 bg-slate-100 hover:bg-indigo-600 rounded-2xl flex items-center justify-center text-slate-400 hover:text-white transition-all group">
                    <i class="fas fa-arrow-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                </a>
                <div>
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.3em] italic leading-none">New
                        Enrollment</p>
                </div>
            </div>
            <h2 class="text-5xl font-black text-slate-800 tracking-tight leading-none italic">Student Registration</h2>
            <p class="text-slate-400 font-medium tracking-tight mt-4 text-lg italic">Create a new student identity and
                institutional access credentials.</p>
        </div>

        <div class="flex items-center space-x-4">
            <div class="bg-slate-900 px-8 py-5 rounded-[2.5rem] text-white shadow-2xl flex items-center space-x-6">
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1 italic">
                        Active Session</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">
                        <?php echo $settings['academic_year']; ?></p>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-indigo-600/30 text-lg">
                    +</div>
            </div>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div
            class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-8 rounded-[2.5rem] mb-12 flex items-center shadow-lg shadow-emerald-100/50 animate__animated animate__fadeInDown">
            <div
                class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mr-6 shadow-lg shadow-emerald-200">
                <i class="fas fa-check text-xl"></i>
            </div>
            <div>
                <p class="font-black text-lg italic">Enrollment Confirmed!</p>
                <p class="text-sm font-medium"><?php echo $success_message; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div
            class="bg-rose-50 border border-rose-200 text-rose-700 p-8 rounded-[2.5rem] mb-12 flex items-center shadow-lg shadow-rose-100/50 animate__animated animate__shakeX">
            <div
                class="w-14 h-14 bg-rose-500 rounded-2xl flex items-center justify-center text-white mr-6 shadow-lg shadow-rose-200">
                <i class="fas fa-times text-xl"></i>
            </div>
            <div>
                <p class="font-black text-lg italic">Registration Error</p>
                <p class="text-sm font-medium"><?php echo $error_message; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form action="students-add.php" method="POST" enctype="multipart/form-data" class="space-y-12" id="enrollmentForm">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <!-- Step Indicator -->
        <div class="flex items-center justify-center gap-4 mb-8">
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-auth')">
                <div
                    class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-indigo-200">
                    1</div>
                <span
                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Auth</span>
            </div>
            <div class="w-10 h-0.5 bg-indigo-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-academic')">
                <div
                    class="w-10 h-10 bg-amber-500 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-amber-200">
                    2</div>
                <span
                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Academic</span>
            </div>
            <div class="w-10 h-0.5 bg-amber-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-personal')">
                <div
                    class="w-10 h-10 bg-rose-500 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-rose-200">
                    3</div>
                <span
                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Personal</span>
            </div>
            <div class="w-10 h-0.5 bg-rose-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-guardian')">
                <div
                    class="w-10 h-10 bg-emerald-500 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-emerald-200">
                    4</div>
                <span
                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Guardian</span>
            </div>
            <div class="w-10 h-0.5 bg-emerald-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-docs')">
                <div
                    class="w-10 h-10 bg-violet-600 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-violet-200">
                    5</div>
                <span
                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Docs</span>
            </div>
        </div>

        <!-- Section 1: Authentication Credentials -->
        <div id="section-auth"
            class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-indigo-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500">
            <div
                class="absolute top-0 right-0 w-40 h-40 bg-indigo-600/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700">
            </div>

            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div
                    class="w-16 h-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-200">
                    <i class="fas fa-user-lock text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Authentication
                        Credentials</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Institutional login identity and secure
                        access parameters.</p>
                </div>
            </div>

            <div class="mb-12 flex flex-col items-center justify-center space-y-6 relative z-10">
                <div class="relative group/avatar">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-slate-100 overflow-hidden border-4 border-white shadow-xl">
                        <img id="avatarPreview" src="../assets/images/default_profile.svg" class="w-full h-full object-cover" alt="Profile Preview">
                    </div>
                    <label for="profile_pic" class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white cursor-pointer shadow-lg hover:scale-110 transition-all border-2 border-white">
                        <i class="fas fa-camera text-sm"></i>
                        <input type="file" id="profile_pic" name="profile_pic" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Identity Visualization (Optional)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="full_name">
                        <i class="fas fa-signature mr-2 text-indigo-400"></i>Full Legal Name *
                    </label>
                    <input type="text" id="full_name" name="full_name" required placeholder="e.g. Rahul Sharma"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="email">
                        <i class="fas fa-at mr-2 text-indigo-400"></i>Email Address Profile *
                    </label>
                    <input type="email" id="email" name="email" required placeholder="rahul@example.com"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="username">
                        <i class="fas fa-fingerprint mr-2 text-indigo-400"></i>Unique Username *
                    </label>
                    <input type="text" id="username" name="username" required placeholder="rahul_2025"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="password">
                        <i class="fas fa-shield-halved mr-2 text-indigo-400"></i>Initial Passphrase *
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required placeholder="Minimum 6 characters"
                            class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base pr-14">
                        <button type="button" onclick="togglePwd()"
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-eye" id="pwdToggle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Academic Records -->
        <div id="section-academic"
            class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-amber-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-amber-50 transition-all duration-500">
            <div
                class="absolute top-0 right-0 w-40 h-40 bg-amber-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700">
            </div>

            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div
                    class="w-16 h-16 bg-amber-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-amber-200">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Academic Assignment
                    </h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Enrollment details, course allocation, and
                        semester designation.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="roll_no">
                        <i class="fas fa-hashtag mr-2 text-amber-500"></i>Roll Number Index *
                    </label>
                    <input type="text" id="roll_no" name="roll_no" required placeholder="BCA/2025/042"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base uppercase">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="course_id">
                        <i class="fas fa-book-bookmark mr-2 text-amber-500"></i>Program Enrollment *
                    </label>
                    <select id="course_id" name="course_id" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <option value="">Select Academic Program</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>">
                                <?php echo $course['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="semester">
                        <i class="fas fa-layer-group mr-2 text-amber-500"></i>Active Semester Cycle *
                    </label>
                    <select id="semester" name="semester" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="admission_date">
                        <i class="fas fa-calendar-plus mr-2 text-amber-500"></i>Admission Date *
                    </label>
                    <input type="date" id="admission_date" name="admission_date" required
                        value="<?php echo date('Y-m-d'); ?>"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
            </div>
        </div>

        <!-- Section 3: Personal Profile -->
        <div id="section-personal"
            class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-rose-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-rose-50 transition-all duration-500">
            <div
                class="absolute top-0 right-0 w-40 h-40 bg-rose-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700">
            </div>

            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div
                    class="w-16 h-16 bg-rose-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-rose-200">
                    <i class="fas fa-id-card text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Personal Demographics
                    </h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Identity documentation and contact
                        coordinates.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="dob">
                        <i class="fas fa-cake-candles mr-2 text-rose-500"></i>Date of Birth
                    </label>
                    <input type="date" id="dob" name="dob"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="gender">
                        <i class="fas fa-venus-mars mr-2 text-rose-500"></i>Gender Identity
                    </label>
                    <select id="gender" name="gender"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="phone">
                        <i class="fas fa-phone mr-2 text-rose-500"></i>Primary Phone
                    </label>
                    <input type="text" id="phone" name="phone" placeholder="+91 00000 00000"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="address">
                        <i class="fas fa-location-dot mr-2 text-rose-500"></i>Permanent Residential Address
                    </label>
                    <textarea id="address" name="address" rows="3" placeholder="Enter full residential address..."
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base"></textarea>
                </div>
            </div>
        </div>

        <!-- Section 4: Guardian Information -->
        <div id="section-guardian"
            class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-emerald-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-emerald-50 transition-all duration-500">
            <div
                class="absolute top-0 right-0 w-40 h-40 bg-emerald-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700">
            </div>

            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div
                    class="w-16 h-16 bg-emerald-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-emerald-200">
                    <i class="fas fa-people-roof text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Guardian Profile</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Parent or legal guardian contact registry.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="parent_name">
                        <i class="fas fa-user-tie mr-2 text-emerald-500"></i>Guardian Full Name
                    </label>
                    <input type="text" id="parent_name" name="parent_name" placeholder="Parent / Guardian Name"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white focus:border-emerald-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic"
                        for="parent_phone">
                        <i class="fas fa-phone-flip mr-2 text-emerald-500"></i>Guardian Contact Number
                    </label>
                    <input type="text" id="parent_phone" name="parent_phone" placeholder="+91 00000 00000"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white focus:border-emerald-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Institutional Documents -->
        <div id="section-docs"
            class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-violet-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-violet-50 transition-all duration-500">
            <div
                class="absolute top-0 right-0 w-40 h-40 bg-violet-600/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700">
            </div>

            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div
                    class="w-16 h-16 bg-violet-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-violet-200">
                    <i class="fas fa-file-shield text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Institutional Documents</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Verification protocols and academic certifications.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 relative z-10">
                <!-- Aadhaar Card -->
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-transparent hover:border-violet-200 transition-all group/doc">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Aadhaar Identification</label>
                    <div class="relative">
                        <input type="file" name="aadhaar" class="hidden" id="doc_aadhaar" accept=".pdf,image/*" onchange="updateDocStatus('aadhaar')">
                        <button type="button" onclick="document.getElementById('doc_aadhaar').click()" class="w-full py-4 bg-white rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-bold text-xs hover:border-violet-500 hover:text-violet-600 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="label_aadhaar">Upload PDF/JPG</span>
                        </button>
                    </div>
                </div>

                <!-- SSLC Marks Card -->
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-transparent hover:border-violet-200 transition-all group/doc">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">SSLC Marks Card</label>
                    <div class="relative">
                        <input type="file" name="sslc" class="hidden" id="doc_sslc" accept=".pdf,image/*" onchange="updateDocStatus('sslc')">
                        <button type="button" onclick="document.getElementById('doc_sslc').click()" class="w-full py-4 bg-white rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-bold text-xs hover:border-violet-500 hover:text-violet-600 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="label_sslc">Upload PDF/JPG</span>
                        </button>
                    </div>
                </div>

                <!-- PUC Marks Card -->
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-transparent hover:border-violet-200 transition-all group/doc">
                    <label class="block text-[10px) font-black text-slate-400 uppercase tracking-widest mb-4 italic">PUC Marks Card</label>
                    <div class="relative">
                        <input type="file" name="puc" class="hidden" id="doc_puc" accept=".pdf,image/*" onchange="updateDocStatus('puc')">
                        <button type="button" onclick="document.getElementById('doc_puc').click()" class="w-full py-4 bg-white rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-bold text-xs hover:border-violet-500 hover:text-violet-600 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="label_puc">Upload PDF/JPG</span>
                        </button>
                    </div>
                </div>

                <!-- Transfer Certificate -->
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-transparent hover:border-violet-200 transition-all group/doc">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Transfer Certificate (TC)</label>
                    <div class="relative">
                        <input type="file" name="tc" class="hidden" id="doc_tc" accept=".pdf,image/*" onchange="updateDocStatus('tc')">
                        <button type="button" onclick="document.getElementById('doc_tc').click()" class="w-full py-4 bg-white rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-bold text-xs hover:border-violet-500 hover:text-violet-600 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="label_tc">Upload PDF/JPG</span>
                        </button>
                    </div>
                </div>

                <!-- Passport Photo -->
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-transparent hover:border-violet-200 transition-all group/doc">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Passport Size Photo</label>
                    <div class="relative">
                        <input type="file" name="photo" class="hidden" id="doc_photo" accept="image/*" onchange="updateDocStatus('photo')">
                        <button type="button" onclick="document.getElementById('doc_photo').click()" class="w-full py-4 bg-white rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-bold text-xs hover:border-violet-500 hover:text-violet-600 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="label_photo">Upload Image</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Actions -->
        <div
            class="bg-slate-900 p-10 rounded-[4rem] shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center space-x-6">
                <div
                    class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black italic text-xl shadow-lg shadow-indigo-600/30">
                    V</div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic leading-none mb-1">
                        Enrollment Authorization</p>
                    <p class="text-white font-black text-lg italic tracking-tight">Ready to finalize this student
                        record?</p>
                </div>
            </div>
            <div class="flex items-center gap-6 w-full md:w-auto">
                <button type="reset"
                    class="flex-1 md:flex-auto px-10 py-5 bg-slate-800 hover:bg-slate-700 text-slate-400 rounded-[2rem] font-black text-[10px] tracking-widest uppercase transition-all border border-slate-700 italic">
                    <i class="fas fa-rotate-left mr-2"></i> Reset All
                </button>
                <button type="submit"
                    class="flex-1 md:flex-auto px-14 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[2rem] font-black text-[10px] tracking-widest uppercase shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:-translate-y-1 transition-all italic">
                    <i class="fas fa-user-plus mr-2"></i> Enroll Student
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function togglePwd() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('pwdToggle');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function scrollToSection(id) {
        document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateDocStatus(type) {
        const input = document.getElementById('doc_' + type);
        const label = document.getElementById('label_' + type);
        if (input.files && input.files.length > 0) {
            label.innerText = 'File Attached: ' + input.files[0].name.substring(0, 15) + '...';
            label.parentElement.classList.replace('border-slate-200', 'border-emerald-500');
            label.parentElement.classList.replace('text-slate-400', 'text-emerald-600');
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>