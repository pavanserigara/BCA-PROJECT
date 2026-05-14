<?php
$page_title = "Manage Students";
require_once 'includes/header.php';

// Search logic
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$query = "SELECT u.id, u.full_name, u.email, u.status, u.profile_pic, s.roll_no, c.name as course_name, s.semester,
          (SELECT COUNT(*) FROM student_documents WHERE student_id = u.id AND verification_status = 'pending') as pending_count
          FROM students s 
          JOIN users u ON s.user_id = u.id 
          JOIN courses c ON s.course_id = c.id";

if ($search) {
    $query .= " WHERE u.full_name LIKE :search OR s.roll_no LIKE :search OR u.email LIKE :search";
}

$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
$students = $stmt->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Student Directory</h2>
        <p class="text-slate-500 font-medium">Manage and monitor
            <?php echo count($students); ?> registered students.
        </p>
    </div>

    <a href="students-add.php"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5 transform active:scale-95">
        <i class="fas fa-user-plus"></i>
        <span>Add New Student</span>
    </a>
</div>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-indigo-100/50 overflow-hidden">
    <!-- Filters & Search -->
    <div
        class="p-8 border-b border-indigo-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
        <form method="GET" class="flex-1 max-w-lg relative group">
            <i
                class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
            <input type="text" name="search" value="<?php echo $search; ?>"
                placeholder="Search by Name, Roll No, or Email..."
                class="w-full pl-12 pr-6 py-4 bg-white border border-indigo-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium">
        </form>

        <div class="flex items-center space-x-3">
            <select
                class="bg-white border border-indigo-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-600 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                <option>All Courses</option>
                <option>BCA</option>
                <option>BBA</option>
            </select>
            <button
                class="p-4 bg-white border border-indigo-100 rounded-2xl text-slate-500 hover:text-indigo-600 hover:border-indigo-600 transition-all">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>

    <!-- Students Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                    <th class="py-6 px-8">Student Detail</th>
                    <th class="py-6 px-8">Roll Number</th>
                    <th class="py-6 px-8">Course & Sem</th>
                    <th class="py-6 px-8">Status</th>
                    <th class="py-6 px-8 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-50/50">
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div
                                class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <i class="fas fa-users-slash text-3xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-slate-800">No Students Found</h4>
                            <p class="text-slate-500 mt-2">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 px-8">
                                <div class="flex items-center space-x-4">
                                    <?php if ($student['profile_pic'] && $student['profile_pic'] !== 'default_profile.svg' && file_exists('../uploads/profiles/' . $student['profile_pic'])): ?>
                                        <img src="../uploads/profiles/<?php echo $student['profile_pic']; ?>" 
                                             class="w-12 h-12 rounded-2xl object-cover shadow-lg shadow-indigo-100" alt="">
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-blue-500 text-white flex items-center justify-center font-black text-lg shadow-lg shadow-indigo-100">
                                            <?php echo substr($student['full_name'], 0, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="text-base font-bold text-slate-800 tracking-tight leading-none mb-1">
                                            <?php echo $student['full_name']; ?>
                                        </h6>
                                        <p class="text-xs font-bold text-slate-400">
                                            <?php echo $student['email']; ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-8">
                                <span
                                    class="text-sm font-black text-slate-700 bg-slate-100 px-4 py-2 rounded-xl border border-slate-200">
                                    <?php echo $student['roll_no']; ?>
                                </span>
                            </td>
                            <td class="py-6 px-8">
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-lg">
                                        <?php echo $student['course_name']; ?>
                                    </span>
                                    <span
                                        class="text-xs font-bold text-slate-500 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">Sem
                                        <?php echo $student['semester']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="py-6 px-8">
                                <?php if ($student['status'] === 'active'): ?>
                                    <span
                                        class="inline-flex items-center space-x-2 text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                        <span>Active</span>
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center space-x-2 text-rose-600 bg-rose-50 border border-rose-100 px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-widest">
                                        <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                                        <span>Inactive</span>
                                    </span>
                                <?php endif; ?>

                                <?php if ($student['pending_count'] > 0): ?>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center space-x-2 text-amber-600 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest italic">
                                            <i class="fas fa-clock text-[8px] animate-spin-slow"></i>
                                            <span><?php echo $student['pending_count']; ?> Pending</span>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-6 px-8 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="student-view.php?id=<?php echo $student['id']; ?>"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-lg hover:shadow-indigo-50 transition-all">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="student-edit.php?id=<?php echo $student['id']; ?>"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-amber-600 hover:border-amber-600 hover:shadow-lg hover:shadow-amber-50 transition-all">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $student['id']; ?>)"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rose-600 hover:border-rose-600 hover:shadow-lg hover:shadow-rose-50 transition-all">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-8 bg-slate-50/50 border-t border-indigo-50 flex items-center justify-between">
        <p class="text-sm font-bold text-slate-500">Showing <span class="text-slate-800">
                <?php echo count($students); ?>
            </span> students</p>
        <div class="flex items-center space-x-2">
            <button
                class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:bg-white transition-all cursor-not-allowed">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <button
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-600 text-white font-bold text-sm shadow-lg shadow-indigo-100">
                1
            </button>
            <button
                class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-white transition-all">
                2
            </button>
            <button
                class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:bg-white transition-all">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
            window.location.href = 'student-delete.php?id=' + id;
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>