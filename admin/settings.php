<?php
$page_title = "Institutional Global Configuration";
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_POST['update_settings'])) {
    csrf_guard();
    $college_name = sanitize($_POST['college_name']);
    $college_email = sanitize($_POST['college_email']);
    $college_phone = sanitize($_POST['college_phone']);
    $college_address = sanitize($_POST['college_address']);
    $academic_year = sanitize($_POST['academic_year']);
    $current_semester = (int) $_POST['current_semester'];
    $primary_color = sanitize($_POST['primary_color']);
    $secondary_color = sanitize($_POST['secondary_color']);

    $logo = $settings['logo'];
    $favicon = $settings['favicon'];

    // Handle Logo Upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        
        if (in_array($file_ext, $allowed_exts)) {
            $logo_name = 'logo_' . time() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name)) {
                $logo = $logo_name;
                $success_message .= " Logo artifact synchronized.";
            } else {
                $error_message .= " Logo transfer failed: Check directory permissions.";
            }
        } else {
            $error_message .= " Invalid logo format (Allowed: " . implode(', ', $allowed_exts) . ").";
        }
    }

    // Handle Favicon Upload
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['ico', 'png', 'svg', 'webp'];
        
        if (in_array($file_ext, $allowed_exts)) {
            $favicon_name = 'favicon_' . time() . '.' . $file_ext;
            if (move_uploaded_file($_FILES['favicon']['tmp_name'], $upload_dir . $favicon_name)) {
                $favicon = $favicon_name;
                $success_message .= " Favicon identity resolved.";
            } else {
                $error_message .= " Favicon transfer failed.";
            }
        } else {
            $error_message .= " Invalid favicon format.";
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE settings SET college_name = ?, college_email = ?, college_phone = ?, college_address = ?, academic_year = ?, current_semester = ?, primary_color = ?, secondary_color = ?, logo = ?, favicon = ? WHERE id = 1");
        $stmt->execute([$college_name, $college_email, $college_phone, $college_address, $academic_year, $current_semester, $primary_color, $secondary_color, $logo, $favicon]);
        $success_message = "Institutional Global Configuration updated successfully!" . $success_message;
        // Refresh local settings variable
        $settings = get_college_settings($pdo);
    } catch (PDOException $e) {
        $error_message = "Update failed: " . $e->getMessage();
    }
}
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Global Configuration</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Configure core institutional metadata and
                system-wide academic phases.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-slate-900 px-8 py-5 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-100 flex items-center space-x-6">
                <div class="text-right">
                    <p
                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1 italic">
                        Academic Phase</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">
                        <?php echo $settings['academic_year']; ?>
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-indigo-400 text-xl">
                    AY</div>
            </div>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div
            class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-8 rounded-[2.5rem] mb-12 flex items-center animate__animated animate__fadeInDown italic">
            <i class="fas fa-check-circle text-2xl mr-6"></i>
            <p class="text-base font-bold">
                <?php echo $success_message; ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div
            class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-8 rounded-[2.5rem] mb-12 flex items-center animate__animated animate__fadeInDown italic">
            <i class="fas fa-exclamation-circle text-2xl mr-6"></i>
            <p class="text-base font-bold">
                <?php echo $error_message; ?>
            </p>
        </div>
    <?php endif; ?>

    <form action="settings.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <input type="hidden" name="update_settings" value="1">

        <!-- Left Column: Branding -->
        <div class="space-y-12">
            <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden">
                <h4
                    class="text-xl font-black text-slate-800 tracking-tight italic mb-10 border-b border-indigo-50 pb-6 leading-none uppercase">
                    Identity & Brand</h4>

                <div class="space-y-8">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">College
                            Name Index *</label>
                        <input type="text" name="college_name" value="<?php echo $settings['college_name']; ?>" required
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    </div>

                    <div class="pt-6">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic leading-none">Institutional
                            Signature (Logo)</label>
                        <div class="relative group w-32 h-32">
                            <div class="w-32 h-32 bg-indigo-600 rounded-[2.5rem] flex items-center justify-center text-white font-black text-4xl italic shadow-2xl overflow-hidden">
                                <?php if ($settings['logo']): ?>
                                    <img id="logo_preview" src="../assets/images/<?php echo $settings['logo']; ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span id="logo_placeholder">V</span>
                                <?php endif; ?>
                            </div>
                            <label for="logo_input" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer rounded-[2.5rem]">
                                <i class="fas fa-camera text-white text-xl"></i>
                            </label>
                            <input type="file" id="logo_input" name="logo" class="hidden" accept="image/*">
                        </div>
                    </div>

                    <div class="pt-6">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic leading-none">Browser Identity (Favicon)</label>
                        <div class="relative group w-16 h-16">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center border-2 border-dashed border-slate-300 overflow-hidden">
                                <?php if ($settings['favicon']): ?>
                                    <img id="favicon_preview" src="../assets/images/<?php echo $settings['favicon']; ?>" class="w-8 h-8 object-contain">
                                <?php else: ?>
                                    <i class="fas fa-globe text-slate-300"></i>
                                <?php endif; ?>
                            </div>
                            <label for="favicon_input" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer rounded-2xl">
                                <i class="fas fa-upload text-white text-xs"></i>
                            </label>
                            <input type="file" id="favicon_input" name="favicon" class="hidden" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 p-12 rounded-[4rem] shadow-2xl">
                <h4
                    class="text-xl font-black text-white tracking-tight italic mb-10 border-b border-white/10 pb-6 leading-none uppercase">
                    System Theme Palette</h4>
                
                <div class="space-y-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 italic">Primary Neural Core</label>
                        <div class="flex items-center space-x-4">
                            <input type="color" name="primary_color" value="<?php echo $settings['primary_color'] ?? '#6366f1'; ?>" class="w-12 h-12 rounded-xl bg-transparent border-none cursor-pointer">
                            <span class="text-xs font-bold text-white/50 mono"><?php echo strtoupper($settings['primary_color'] ?? '#6366f1'); ?></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 italic">Secondary Overlay</label>
                        <div class="flex items-center space-x-4">
                            <input type="color" name="secondary_color" value="<?php echo $settings['secondary_color'] ?? '#4f46e5'; ?>" class="w-12 h-12 rounded-xl bg-transparent border-none cursor-pointer">
                            <span class="text-xs font-bold text-white/50 mono"><?php echo strtoupper($settings['secondary_color'] ?? '#4f46e5'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Formal Config -->
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
                <h4
                    class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4 uppercase">
                    <span
                        class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xs italic">C</span>
                    <span>Contact Registry Profile</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Support
                            Mail Hub *</label>
                        <input type="email" name="college_email" value="<?php echo $settings['college_email']; ?>"
                            required
                            class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Coordinating
                            Phone Hub *</label>
                        <input type="text" name="college_phone" value="<?php echo $settings['college_phone']; ?>"
                            required
                            class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Physical
                            Residence Address Profile *</label>
                        <textarea name="college_address" rows="3" required
                            class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800"><?php echo $settings['college_address']; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
                <h4
                    class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4 uppercase">
                    <span
                        class="w-10 h-10 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 text-xs italic">A</span>
                    <span>Academic Cycle Configuration</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-15">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Active
                            Academic Year Profile *</label>
                        <input type="text" name="academic_year" value="<?php echo $settings['academic_year']; ?>"
                            required placeholder="e.g. 2024-25"
                            class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Current
                            Semester Cycle *</label>
                        <select name="current_semester" required
                            class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $settings['current_semester'] == $i ? 'selected' : ''; ?>>Active Semester
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="pt-8 border-t border-indigo-50 flex items-center gap-8">
                    <button type="submit"
                        class="flex-1 py-6 bg-indigo-600 text-white font-black rounded-[2.5rem] shadow-2xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all uppercase tracking-widest text-xs italic">
                        Authorize Global Update
                    </button>
                    <button type="reset"
                        class="px-12 py-6 bg-slate-50 text-slate-400 font-black rounded-[2.5rem] hover:bg-slate-100 transition-all uppercase tracking-widest text-xs italic">Discard
                        Pulse</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('logo_input').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('logo_preview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                var container = document.getElementById('logo_placeholder').parentElement;
                container.innerHTML = '<img id="logo_preview" src="' + e.target.result + '" class="w-full h-full object-cover">';
            }
        }
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('favicon_input').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('favicon_preview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                var container = document.getElementById('favicon_preview').parentElement;
                container.innerHTML = '<img id="favicon_preview" src="' + e.target.result + '" class="w-8 h-8 object-contain">';
            }
        }
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>