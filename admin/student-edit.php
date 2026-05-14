<?php
$page_title = "Edit Student";
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: students-list.php");
    exit();
}

// Fetch student + user
$stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.username, u.status, u.profile_pic, s.roll_no, s.course_id, s.semester, s.admission_date, s.dob, s.gender, s.phone, s.address, s.parent_name, s.parent_phone
                       FROM users u
                       JOIN students s ON s.user_id = u.id
                       WHERE u.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

// Fetch Documents
$stmt_docs = $pdo->prepare("SELECT * FROM student_documents WHERE student_id = ?");
$stmt_docs->execute([$id]);
$documents = $stmt_docs->fetchAll(PDO::FETCH_ASSOC);
$doc_map = [];
foreach ($documents as $d) {
    $doc_map[$d['document_type']] = $d;
}

if (!$student) {
    header("Location: students-list.php");
    exit();
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY name")->fetchAll();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    csrf_guard();
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $username = sanitize($_POST['username']);
    $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

    $roll_no = sanitize($_POST['roll_no']);
    $course_id = (int) $_POST['course_id'];
    $semester = (int) $_POST['semester'];
    $admission_date = $_POST['admission_date'];

    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?: null;
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $parent_name = sanitize($_POST['parent_name']);
    $parent_phone = sanitize($_POST['parent_phone']);

    try {
        $pdo->beginTransaction();

        // Handle Profile Picture Update
        $profile_pic = $student['profile_pic'];
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = 'student_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], '../uploads/profiles/' . $profile_pic)) {
                // Delete old pic if exists
                if ($student['profile_pic'] && $student['profile_pic'] !== 'default_profile.svg' && file_exists('../uploads/profiles/' . $student['profile_pic'])) {
                    unlink('../uploads/profiles/' . $student['profile_pic']);
                }
            }
        }

        $stmt_u = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, username = ?, status = ?, profile_pic = ? WHERE id = ?");
        $stmt_u->execute([$full_name, $email, $username, $status, $profile_pic, $id]);

        $stmt_s = $pdo->prepare("UPDATE students
                                 SET roll_no = ?, course_id = ?, semester = ?, admission_date = ?, dob = ?, gender = ?, phone = ?, address = ?, parent_name = ?, parent_phone = ?
                                 WHERE user_id = ?");
        $stmt_s->execute([$roll_no, $course_id, $semester, $admission_date, $dob, $gender, $phone, $address, $parent_name, $parent_phone, $id]);

        // Handle Institutional Documents
        $doc_types = ['aadhaar', 'sslc', 'puc', 'tc', 'photo'];
        foreach ($doc_types as $type) {
            if (isset($_FILES[$type]) && $_FILES[$type]['error'] === 0) {
                $ext = pathinfo($_FILES[$type]['name'], PATHINFO_EXTENSION);
                $doc_filename = 'doc_' . $type . '_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES[$type]['tmp_name'], '../uploads/documents/' . $doc_filename)) {
                    // Check if exists
                    if (isset($doc_map[$type])) {
                        // Delete old
                        if (file_exists('../uploads/documents/' . $doc_map[$type]['file_path'])) {
                            unlink('../uploads/documents/' . $doc_map[$type]['file_path']);
                        }
                        $stmt_doc = $pdo->prepare("UPDATE student_documents SET file_path = ?, uploaded_at = CURRENT_TIMESTAMP WHERE student_id = ? AND document_type = ?");
                        $stmt_doc->execute([$doc_filename, $id, $type]);
                    } else {
                        $stmt_doc = $pdo->prepare("INSERT INTO student_documents (student_id, document_type, file_path) VALUES (?, ?, ?)");
                        $stmt_doc->execute([$id, $type, $doc_filename]);
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: student-view.php?id=" . $id . "&updated=1");
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_message = "Update failed: " . $e->getMessage();
    }
}
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-12">
        <div class="flex items-center space-x-6">
            <a href="student-view.php?id=<?php echo (int) $student['id']; ?>"
                class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:shadow-xl hover:shadow-indigo-500/10 transition-all border border-slate-100">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Modify Student Record</h2>
                <p class="text-sm text-slate-400 font-medium italic mt-1">Updating institutional credentials for <?php echo $student['full_name']; ?>.</p>
            </div>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-error" role="alert">
            <i class="fas fa-triangle-exclamation text-[12px]"></i>
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-12 pb-24" id="enrollmentForm">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <input type="hidden" name="update_student" value="1">

        <!-- Step Indicator -->
        <div class="flex items-center justify-center gap-4 mb-8">
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-auth')">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-indigo-200">1</div>
                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Auth</span>
            </div>
            <div class="w-10 h-0.5 bg-indigo-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-academic')">
                <div class="w-10 h-10 bg-amber-500 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-amber-200">2</div>
                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Academic</span>
            </div>
            <div class="w-10 h-0.5 bg-amber-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-personal')">
                <div class="w-10 h-10 bg-rose-500 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-rose-200">3</div>
                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Personal</span>
            </div>
            <div class="w-10 h-0.5 bg-rose-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-guardian')">
                <div class="w-10 h-10 bg-emerald-500 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-emerald-200">4</div>
                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Guardian</span>
            </div>
            <div class="w-10 h-0.5 bg-emerald-200 rounded-full"></div>
            <div class="flex items-center space-x-3 cursor-pointer group" onclick="scrollToSection('section-docs')">
                <div class="w-10 h-10 bg-violet-600 rounded-2xl flex items-center justify-center text-white font-black text-xs shadow-lg shadow-violet-200">5</div>
                <span class="text-[10px] font-black text-slate-800 uppercase tracking-widest hidden sm:block italic">Docs</span>
            </div>
        </div>

        <!-- Section 1: Authentication Credentials -->
        <div id="section-auth" class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-indigo-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500">
            <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-600/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700"></div>
            
            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div class="w-16 h-16 bg-indigo-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-indigo-200">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Authentication Credentials</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Institutional login identity and profile visualization.</p>
                </div>
            </div>

            <div class="mb-12 flex flex-col items-center justify-center space-y-6 relative z-10">
                <div class="relative group/avatar">
                    <div class="w-32 h-32 rounded-[2.5rem] bg-slate-100 overflow-hidden border-4 border-white shadow-xl">
                        <?php 
                            $pic = $student['profile_pic'];
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
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Full Legal Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Institutional Email Flow</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Portal Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="group/field">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Account Status</label>
                    <select name="status" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white focus:border-indigo-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <option value="active" <?php echo $student['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $student['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Academic Assignment -->
        <div id="section-academic" class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-amber-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-amber-50 transition-all duration-500">
            <div class="absolute top-0 right-0 w-40 h-40 bg-amber-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700"></div>
            
            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div class="w-16 h-16 bg-amber-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-amber-200">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Academic Assignment</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Course allocation and institutional registry markers.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Roll Number</label>
                    <input type="text" name="roll_no" value="<?php echo htmlspecialchars($student['roll_no']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Assigned Course</label>
                    <select name="course_id" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <?php foreach ($courses as $c): ?>
                            <option value="<?php echo (int) $c['id']; ?>" <?php echo (int) $student['course_id'] === (int) $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Semester Flow</label>
                    <select name="semester" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo (int) $student['semester'] === $i ? 'selected' : ''; ?>>Semester <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Admission Date Profile</label>
                    <input type="date" name="admission_date" value="<?php echo htmlspecialchars($student['admission_date']); ?>" required
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:bg-white focus:border-amber-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
            </div>
        </div>

        <!-- Section 3: Personal Profile -->
        <div id="section-personal" class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-rose-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-rose-50 transition-all duration-500">
            <div class="absolute top-0 right-0 w-40 h-40 bg-rose-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700"></div>
            
            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div class="w-16 h-16 bg-rose-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-rose-200">
                    <i class="fas fa-user-circle text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Personal Profile</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Identity markers and residential coordination.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Date of Birth</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob'] ?? ''); ?>"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Gender Identity</label>
                    <select name="gender"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                        <option value="Male" <?php echo $student['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $student['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $student['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Primary Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Residential Address Flow</label>
                    <textarea name="address" rows="3"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white focus:border-rose-500/30 transition-all outline-none font-bold text-slate-800 text-base"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Section 4: Guardian Profile -->
        <div id="section-guardian" class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-emerald-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-emerald-50 transition-all duration-500">
            <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-500/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700"></div>
            
            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div class="w-16 h-16 bg-emerald-500 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-emerald-200">
                    <i class="fas fa-people-roof text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Guardian Profile</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Parent or legal guardian contact registry.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Guardian Full Name</label>
                    <input type="text" name="parent_name" value="<?php echo htmlspecialchars($student['parent_name'] ?? ''); ?>"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white focus:border-emerald-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Guardian Contact Flow</label>
                    <input type="text" name="parent_phone" value="<?php echo htmlspecialchars($student['parent_phone'] ?? ''); ?>"
                        class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:bg-white focus:border-emerald-500/30 transition-all outline-none font-bold text-slate-800 text-base">
                </div>
            </div>
        </div>

        <!-- Section 5: Institutional Documents -->
        <div id="section-docs" class="bg-white p-12 md:p-16 rounded-[4rem] shadow-sm border border-violet-100/30 relative overflow-hidden group hover:shadow-2xl hover:shadow-violet-50 transition-all duration-500">
            <div class="absolute top-0 right-0 w-40 h-40 bg-violet-600/5 rounded-full -mr-20 -mt-20 group-hover:scale-150 transition-all duration-700"></div>
            
            <div class="flex items-center space-x-6 mb-12 relative z-10">
                <div class="w-16 h-16 bg-violet-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-violet-200">
                    <i class="fas fa-file-shield text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase">Verification Vault</h3>
                    <p class="text-sm text-slate-400 font-medium italic mt-1">Review and synchronize institutional certifications.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 relative z-10">
                <?php 
                $doc_types = [
                    'aadhaar' => 'Aadhaar Identification',
                    'sslc' => 'SSLC Marks Card',
                    'puc' => 'PUC Marks Card',
                    'tc' => 'Transfer Certificate',
                    'photo' => 'Passport Photo'
                ];
                foreach ($doc_types as $type => $label):
                    $existing_doc = $doc_map[$type] ?? null;
                ?>
                <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-transparent hover:border-violet-200 transition-all group/doc">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic"><?php echo $label; ?></label>
                    <?php if ($existing_doc): ?>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[8px] font-black uppercase tracking-widest italic border border-emerald-100">Synchronized</span>
                            <a href="../uploads/documents/<?php echo $existing_doc['file_path']; ?>" target="_blank" class="text-indigo-600 font-black text-[10px] uppercase italic">View Doc</a>
                        </div>
                    <?php endif; ?>
                    <div class="relative">
                        <input type="file" name="<?php echo $type; ?>" class="hidden" id="doc_<?php echo $type; ?>" accept=".pdf,image/*" onchange="updateDocStatus('<?php echo $type; ?>')">
                        <button type="button" onclick="document.getElementById('doc_<?php echo $type; ?>').click()" 
                                class="w-full py-4 bg-white rounded-2xl border-2 border-dashed <?php echo $existing_doc ? 'border-emerald-200 text-emerald-600' : 'border-slate-200 text-slate-400'; ?> font-bold text-xs hover:border-violet-500 hover:text-violet-600 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="label_<?php echo $type; ?>"><?php echo $existing_doc ? 'Replace Document' : 'Upload PDF/JPG'; ?></span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
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
                <a href="student-view.php?id=<?php echo $id; ?>" 
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
</div>

<?php require_once 'includes/footer.php'; ?>

