<?php
$page_title = "Notice Board";
require_once 'includes/header.php';

$success_message = '';
if (isset($_POST['post_notice'])) {
    csrf_guard();
    $title = sanitize($_POST['title']);
    $content = $_POST['content'];
    $target = $_POST['role_target'];
    $dept_id = !empty($_POST['department_id']) ? (int) $_POST['department_id'] : null;
    $admin_id = $_SESSION['user_id'];
    $attachment_path = null;

    // Handle File Upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/attachments/notices/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('notice_') . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attachment_path = $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO notices (title, content, role_target, department_id, attachment, posted_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $target, $dept_id, $attachment_path, $admin_id]);
    $success_message = "Notice published successfully!";
}

$notices = $pdo->query("SELECT n.*, u.full_name as author, d.name as dept_name 
                      FROM notices n 
                      JOIN users u ON n.posted_by = u.id 
                      LEFT JOIN departments d ON n.department_id = d.id
                      ORDER BY n.created_at DESC")->fetchAll();

$departments = $pdo->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Institutional Notices</h2>
        <p class="text-slate-500 font-medium">Broadcast important announcements to the college community.</p>
    </div>

    <button onclick="document.getElementById('notice_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
        <i class="fas fa-plus"></i>
        <span>Post New Notice</span>
    </button>
</div>

<?php if ($success_message): ?>
    <div
        class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center animate__animated animate__fadeIn">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="space-y-6 pb-20">
    <?php if (empty($notices)): ?>
        <div class="bg-white p-20 rounded-[3rem] text-center border border-indigo-50">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                <i class="fas fa-bullhorn text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">The Board is Empty</h3>
            <p class="text-slate-500 mt-2">Publish your first announcement to keep everyone updated.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notices as $notice): ?>
            <div
                class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-indigo-100/50 group hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                            <i class="fas fa-paper-plane text-xl"></i>
                        </div>
                        <div>
                            <h3
                                class="text-2xl font-black text-slate-800 tracking-tight leading-none group-hover:text-indigo-600 transition-colors mb-2">
                                <?php echo $notice['title']; ?>
                            </h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-xs font-bold text-slate-400"><i class="fas fa-user-edit mr-2"></i>
                                    <?php echo $notice['author']; ?>
                                </span>
                                <span class="text-xs font-bold text-slate-400"><i class="fas fa-clock mr-2"></i>
                                    <?php echo date('d M, Y', strtotime($notice['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span
                            class="px-4 py-2 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200">
                            Target:
                            <?php echo ucfirst($notice['role_target']); ?>
                        </span>
                        <?php if ($notice['dept_name']): ?>
                            <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                                Dept: <?php echo $notice['dept_name']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-slate-600 leading-loose text-lg mb-8 bg-slate-50/50 p-8 rounded-3xl border border-slate-100">
                    <?php echo nl2br($notice['content']); ?>
                    <?php if ($notice['attachment']): ?>
                        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-primary-600 shadow-sm border border-slate-100">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Attached Resource</p>
                                    <p class="text-[10px] font-medium text-slate-400">PDF / Document Reference</p>
                                </div>
                            </div>
                            <a href="../assets/attachments/notices/<?php echo $notice['attachment']; ?>" download class="px-4 py-2 bg-white text-primary-600 border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary-600 hover:text-white transition-all shadow-sm">Download</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <button class="text-slate-400 hover:text-indigo-600 font-bold text-sm transition-all"><i
                            class="fas fa-edit mr-2"></i>Edit</button>
                    <div class="w-1.5 h-1.5 bg-slate-200 rounded-full"></div>
                    <button class="text-slate-400 hover:text-rose-600 font-bold text-sm transition-all"><i
                            class="fas fa-trash mr-2"></i>Archive</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Post Notice Modal -->
<div id="notice_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-12 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-3xl font-black text-slate-800">Publish Notice</h3>
            <button onclick="document.getElementById('notice_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 bg-slate-50 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="notices.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="post_notice" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Announcement Title *</label>
                    <input type="text" name="title" required placeholder="e.g. End Semester Examination Schedule 2025"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Notice Visibility *</label>
                    <select name="role_target" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-700">
                        <option value="all">Everyone (All Roles)</option>
                        <option value="students">Students Only</option>
                        <option value="teachers">Faculty Only</option>
                        <option value="staff">Staff Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Target Department (Optional)</label>
                    <select name="department_id"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-700">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Attachment (PDF/Image)</label>
                    <input type="file" name="attachment" 
                        class="w-full px-6 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-700">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Priority Level</label>
                    <select
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium text-slate-700">
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent / Important</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3">Content / Message Body *</label>
                    <textarea name="content" rows="6" required placeholder="Write the announcement details here..."
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-medium"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button type="button" onclick="document.getElementById('notice_modal').classList.add('hidden')"
                    class="flex-1 py-5 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs">Cancel</button>
                <button type="submit"
                    class="flex-1 py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                    Broadcast Now
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>