<?php
$page_title = "My Profile";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];

// Fetch Faculty detailed info
$stmt = $pdo->prepare("SELECT u.*, t.*, d.name as department_name 
                       FROM users u 
                       JOIN teachers t ON u.id = t.user_id 
                       JOIN departments d ON t.dept_id = d.id
                       WHERE u.id = ?");
$stmt->execute([$teacher_id]);
$faculty = $stmt->fetch();

// Fetch dynamic stats
$stmt_subs = $pdo->prepare("SELECT COUNT(*) FROM teacher_subjects WHERE teacher_id = ?");
$stmt_subs->execute([$teacher_id]);
$subject_count = $stmt_subs->fetchColumn();

$stmt_sems = $pdo->prepare("SELECT DISTINCT s.semester 
                             FROM teacher_subjects ts 
                             JOIN subjects s ON ts.subject_id = s.id 
                             WHERE ts.teacher_id = ?");
$stmt_sems->execute([$teacher_id]);
$semesters = $stmt_sems->fetchAll(PDO::FETCH_COLUMN);
$sem_list = !empty($semesters) ? implode(', ', $semesters) : 'N/A';
?>

<div class="mb-8">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Professional Profile</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Academic credentials and institutional records.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-10">
    <!-- Left Column: Identity Card -->
    <div class="lg:col-span-4 space-y-6">
        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-500/5 rounded-full -mr-16 -mt-16"></div>
            
            <div class="relative mb-6 group/img">
                <div class="w-32 h-32 rounded-full mx-auto p-1 bg-gradient-to-tr from-primary-500 to-indigo-500 shadow-xl overflow-hidden">
                    <?php 
                        $pic_path = '../assets/images/default_profile.svg';
                        if (!empty($faculty['profile_pic'])) {
                            if (is_file(__DIR__ . '/../../uploads/profiles/' . $faculty['profile_pic'])) {
                                $pic_path = '../uploads/profiles/' . $faculty['profile_pic'];
                            } elseif (is_file(__DIR__ . '/../../assets/images/' . $faculty['profile_pic'])) {
                                $pic_path = '../assets/images/' . $faculty['profile_pic'];
                            }
                        }
                    ?>
                    <img id="profile_display" src="<?php echo $pic_path; ?>" class="w-full h-full object-cover rounded-full border-4 border-white dark:border-slate-800" alt="Avatar">
                </div>
                <label for="profile_input" class="absolute bottom-1 right-1/2 translate-x-12 w-8 h-8 bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-full flex items-center justify-center text-primary-600 shadow-lg cursor-pointer hover:scale-110 transition-transform">
                    <i class="fas fa-camera text-[10px]"></i>
                </label>
                <input type="file" id="profile_input" class="hidden" accept="image/*">
            </div>

            <h3 class="text-xl font-extrabold text-slate-800 dark:text-white"><?php echo $faculty['full_name']; ?></h3>
            <p class="text-xs font-bold text-primary-600 uppercase tracking-widest mt-1 mb-6"><?php echo $faculty['designation']; ?></p>

            <div class="grid grid-cols-2 gap-4 py-6 border-t border-slate-50 dark:border-slate-700">
                <div class="text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Emp ID</p>
                    <p class="text-sm font-black text-slate-700 dark:text-white"><?php echo $faculty['employee_id']; ?></p>
                </div>
                <div class="text-center">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Experience</p>
                    <p class="text-sm font-black text-slate-700 dark:text-white"><?php echo $faculty['experience'] ?: 'N/A'; ?></p>
                </div>
            </div>

            <script>
            document.getElementById('profile_input').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('profile_pic', file);
                formData.append('csrf_token', '<?php echo generate_csrf_token(); ?>');

                const display = document.getElementById('profile_display');
                display.classList.add('opacity-50');

                fetch('../includes/handlers/profile_upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    display.classList.remove('opacity-50');
                    if (data.success) {
                        display.src = '../uploads/profiles/' + data.filename;
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    display.classList.remove('opacity-50');
                    console.error('Error:', error);
                    alert('An unexpected error occurred.');
                });
            });
            </script>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-soft border border-slate-100 dark:border-slate-700">
            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest mb-6">Security</h4>
            <button class="w-full py-4 bg-rose-50 dark:bg-rose-500/10 text-rose-600 font-bold rounded-2xl text-[10px] uppercase tracking-widest transition-all flex items-center justify-center space-x-2">
                <i class="fas fa-shield-halved text-xs"></i>
                <span>Change Password</span>
            </button>
        </div>
    </div>

    <!-- Right Column: Details -->
    <div class="lg:col-span-8 space-y-6">
        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700">
            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest mb-8 border-b border-slate-50 dark:border-slate-700 pb-4">Personal Information</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email Address</label>
                    <p class="text-sm font-bold text-slate-700 dark:text-white italic"><?php echo $faculty['email']; ?></p>
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Phone Number</label>
                    <p class="text-sm font-bold text-slate-700 dark:text-white italic"><?php echo $faculty['phone'] ?: 'N/A'; ?></p>
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Department</label>
                    <p class="text-sm font-bold text-slate-700 dark:text-white italic"><?php echo $faculty['department_name']; ?></p>
                </div>
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Qualification</label>
                    <p class="text-sm font-bold text-slate-700 dark:text-white italic"><?php echo $faculty['qualification'] ?: 'Masters in Tech'; ?></p>
                </div>
            </div>

            <div class="mt-10 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">Residential Address</label>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 leading-relaxed italic">
                    <?php echo $faculty['address'] ?: 'Official residence records not found.'; ?>
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700">
            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-widest mb-8 border-b border-slate-50 dark:border-slate-700 pb-4">Current Workload</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-5 bg-primary-50 dark:bg-primary-500/10 rounded-2xl border border-primary-100 dark:border-primary-500/20 text-center">
                    <p class="text-[9px] font-bold text-primary-600 uppercase tracking-widest mb-1">Subjects</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white"><?php echo str_pad($subject_count, 2, '0', STR_PAD_LEFT); ?></p>
                </div>
                <div class="p-5 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl border border-indigo-100 dark:border-indigo-500/20 text-center">
                    <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest mb-1">Weekly Lect.</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white"><?php echo $subject_count * 4; // Mock calculation ?></p>
                </div>
                <div class="p-5 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20 text-center">
                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Assigned Sem</p>
                    <p class="text-xl font-black text-slate-800 dark:text-white"><?php echo $sem_list; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>