<?php
$page_title = "Edit Student";
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: students-list.php");
    exit();
}

// Fetch student + user
$stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.username, u.status, s.roll_no, s.course_id, s.semester, s.admission_date, s.dob, s.gender, s.phone, s.address, s.parent_name, s.parent_phone
                       FROM users u
                       JOIN students s ON s.user_id = u.id
                       WHERE u.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

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

        $stmt_u = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, username = ?, status = ? WHERE id = ?");
        $stmt_u->execute([$full_name, $email, $username, $status, $id]);

        $stmt_s = $pdo->prepare("UPDATE students
                                 SET roll_no = ?, course_id = ?, semester = ?, admission_date = ?, dob = ?, gender = ?, phone = ?, address = ?, parent_name = ?, parent_phone = ?
                                 WHERE user_id = ?");
        $stmt_s->execute([$roll_no, $course_id, $semester, $admission_date, $dob, $gender, $phone, $address, $parent_name, $parent_phone, $id]);

        $pdo->commit();
        header("Location: student-view.php?id=" . $id . "&updated=1");
        exit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error_message = "Update failed: " . $e->getMessage();
    }
}
?>

<div class="max-w-5xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="student-view.php?id=<?php echo (int) $student['id']; ?>"
                class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Edit Student</h2>
                <p class="text-slate-500 font-medium text-[13px]">Update course/semester to assign subjects automatically.</p>
            </div>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-error" role="alert">
            <i class="fas fa-triangle-exclamation text-[12px]"></i>
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-6">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <input type="hidden" name="update_student" value="1">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Full name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" required>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" required>
                    <option value="active" <?php echo $student['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $student['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Roll No</label>
                <input type="text" name="roll_no" value="<?php echo htmlspecialchars($student['roll_no']); ?>" required>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Course</label>
                <select name="course_id" required>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo (int) $c['id']; ?>" <?php echo (int) $student['course_id'] === (int) $c['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Semester</label>
                <select name="semester" required>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo (int) $student['semester'] === $i ? 'selected' : ''; ?>>
                            Semester <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Admission date</label>
                <input type="date" name="admission_date" value="<?php echo htmlspecialchars($student['admission_date']); ?>" required>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">DOB</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob'] ?? ''); ?>">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Gender</label>
                <select name="gender">
                    <option value="" <?php echo empty($student['gender']) ? 'selected' : ''; ?>>Not set</option>
                    <option value="Male" <?php echo $student['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $student['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $student['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Parent phone</label>
                <input type="text" name="parent_phone" value="<?php echo htmlspecialchars($student['parent_phone'] ?? ''); ?>">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Parent name</label>
                <input type="text" name="parent_name" value="<?php echo htmlspecialchars($student['parent_name'] ?? ''); ?>">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Address</label>
                <textarea name="address" rows="3"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="student-view.php?id=<?php echo (int) $student['id']; ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>

