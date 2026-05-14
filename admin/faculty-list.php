<?php
$page_title = "Manage Faculty";
require_once 'includes/header.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$query = "SELECT u.id, u.full_name, u.email, u.status, u.profile_pic, t.employee_id, t.designation, d.name as dept_name 
          FROM teachers t 
          JOIN users u ON t.user_id = u.id 
          JOIN departments d ON t.dept_id = d.id";

if ($search) {
    $query .= " WHERE u.full_name LIKE :search OR t.employee_id LIKE :search OR d.name LIKE :search";
}

$stmt = $pdo->prepare($query);
if ($search) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
$faculties = $stmt->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Faculty Directory</h2>
        <p class="text-slate-500 font-medium">Manage and recruit academic excellence for your institution.</p>
    </div>

    <a href="faculty-add.php"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
        <i class="fas fa-plus-circle"></i>
        <span>Recruit Faculty</span>
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
                placeholder="Search by Name, ID, or Department..."
                class="w-full pl-12 pr-6 py-4 bg-white border border-indigo-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium">
        </form>

        <div class="flex items-center space-x-3">
            <select
                class="bg-white border border-indigo-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-600 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                <option>All Departments</option>
                <option>BCA</option>
                <option>BBA</option>
            </select>
            <button
                class="p-4 bg-white border border-indigo-100 rounded-2xl text-slate-500 hover:text-indigo-600 hover:border-indigo-600 transition-all">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>

    <!-- Faculty Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                    <th class="py-6 px-8">Faculty Detail</th>
                    <th class="py-6 px-8">Employee ID</th>
                    <th class="py-6 px-8">Department</th>
                    <th class="py-6 px-8">Status</th>
                    <th class="py-6 px-8 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-indigo-50/50">
                <?php if (empty($faculties)): ?>
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div
                                class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <i class="fas fa-id-card-clip text-3xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-slate-800">No Faculty Registered</h4>
                            <p class="text-slate-500 mt-2">Start by recruiting new faculty members.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($faculties as $faculty): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 px-8">
                                <div class="flex items-center space-x-4">
                                    <?php 
                                        $pic_url = '../assets/images/default_profile.svg';
                                        if (!empty($faculty['profile_pic'])) {
                                            if (is_file(__DIR__ . '/../../uploads/profiles/' . $faculty['profile_pic'])) {
                                                $pic_url = '../uploads/profiles/' . $faculty['profile_pic'];
                                            } elseif (is_file(__DIR__ . '/../../assets/images/' . $faculty['profile_pic'])) {
                                                $pic_url = '../assets/images/' . $faculty['profile_pic'];
                                            }
                                        }
                                    ?>
                                    <img src="<?php echo $pic_url; ?>" class="w-12 h-12 rounded-2xl object-cover shadow-lg shadow-amber-100" alt="">
                                    <div>
                                        <h6
                                            class="text-base font-bold text-slate-800 tracking-tight leading-none mb-1 group-hover:text-indigo-600 transition-colors">
                                            <?php echo $faculty['full_name']; ?>
                                        </h6>
                                        <p class="text-xs font-bold text-slate-400">
                                            <?php echo $faculty['designation']; ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-8 text-sm font-black text-slate-700">
                                <?php echo $faculty['employee_id']; ?>
                            </td>
                            <td class="py-6 px-8">
                                <span
                                    class="text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-lg">
                                    <?php echo $faculty['dept_name']; ?>
                                </span>
                            </td>
                            <td class="py-6 px-8">
                                <?php if ($faculty['status'] === 'active'): ?>
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
                            </td>
                            <td class="py-6 px-8 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="faculty-view.php?id=<?php echo $faculty['id']; ?>"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-lg transition-all">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="faculty-edit.php?id=<?php echo $faculty['id']; ?>"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-amber-600 hover:border-amber-600 transition-all">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rose-600 hover:border-rose-600 transition-all">
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
</div>

<?php require_once 'includes/footer.php'; ?>