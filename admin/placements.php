<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('admin')) {
    header("Location: ../login.php");
    exit();
}

// Handle Job Posting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_job'])) {
    csrf_guard();
    $company = sanitize($_POST['company']);
    $role = sanitize($_POST['role']);
    $package = sanitize($_POST['package']);
    $deadline = $_POST['deadline'];
    $description = sanitize($_POST['description']);
    $requirements = sanitize($_POST['requirements']);

    try {
        $stmt = $pdo->prepare("INSERT INTO placements (company, role, package, description, requirements, deadline) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$company, $role, $package, $description, $requirements, $deadline]);
        set_flash_message('success', 'Placement opportunity published.');
    } catch (PDOException $e) {
        set_flash_message('error', "Post failed: " . $e->getMessage());
    }
}

// Handle Status Update
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = sanitize($_GET['status']);
    $pdo->prepare("UPDATE placement_applications SET status = ? WHERE id = ?")->execute([$status, $id]);
    set_flash_message('success', 'Application status updated.');
    header("Location: placements.php");
    exit();
}

// Handle AJAX Applicant Fetch
if (isset($_GET['fetch_apps'])) {
    $job_id = (int)$_GET['fetch_apps'];
    $stmt = $pdo->prepare("SELECT pa.*, u.full_name, u.email, s.roll_no 
                            FROM placement_applications pa 
                            JOIN users u ON pa.student_id = u.id 
                            JOIN students s ON u.id = s.user_id 
                            WHERE pa.placement_id = ? 
                            ORDER BY pa.applied_at DESC");
    $stmt->execute([$job_id]);
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($apps);
    exit();
}

$page_title = "Placement Hub";
require_once 'includes/header.php';

$jobs = $pdo->query("SELECT *, (SELECT COUNT(*) FROM placement_applications WHERE placement_id = p.id) as app_count 
                     FROM placements p ORDER BY created_at DESC")->fetchAll();
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Career Treasury</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Managing institutional corporate linkages and student career trajectories.</p>
    </div>
    
    <button onclick="document.getElementById('post_modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-premium transition-all transform active:scale-95 flex items-center space-x-3 italic">
        <i class="fas fa-briefcase"></i>
        <span>Post Opportunity</span>
    </button>
</div>

<?php display_flash_message(); ?>

<div class="space-y-10">
    <?php if (empty($jobs)): ?>
        <div class="py-20 bg-white rounded-[4rem] text-center border-2 border-dashed border-slate-100 shadow-premium">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                <i class="fas fa-building-circle-exclamation text-3xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 uppercase italic">Hub Dormant</h3>
            <p class="text-slate-500 mt-2 italic">No active recruitment drives are currently synchronized.</p>
        </div>
    <?php else: ?>
        <?php foreach ($jobs as $j): ?>
            <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-100 overflow-hidden group">
                <div class="p-10 md:p-12 border-b border-slate-50 bg-slate-50/30 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex items-center space-x-6">
                        <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-indigo-600 font-black text-3xl italic shadow-soft border border-slate-100 group-hover:scale-110 transition-transform">
                            <?php echo substr($j['company'], 0, 1); ?>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight italic uppercase leading-none mb-3"><?php echo $j['company']; ?></h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest italic"><?php echo $j['role']; ?></span>
                                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">₹<?php echo $j['package']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic mb-1">Applications</p>
                            <p class="text-xl font-black text-slate-800"><?php echo $j['app_count']; ?></p>
                        </div>
                        <button onclick="viewApps(<?php echo $j['id']; ?>, '<?php echo $j['company']; ?>')" class="px-8 py-4 bg-slate-900 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-slate-900/20 hover:bg-indigo-600 transition-all italic">
                            Review Stream
                        </button>
                    </div>
                </div>
                
                <div class="p-10 md:p-12 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Opportunity Brief</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed italic"><?php echo $j['description']; ?></p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Candidate Protocol</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed italic"><?php echo $j['requirements']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Post Modal -->
<div id="post_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white w-full max-w-2xl rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <div class="flex items-center justify-between mb-12">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Post Opportunity</h3>
            <button onclick="document.getElementById('post_modal').classList.add('hidden')" class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-600 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="post_job" value="1">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Corporate Entity *</label>
                    <input type="text" name="company" required placeholder="e.g. Google India" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-black text-slate-800 italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Designation *</label>
                    <input type="text" name="role" required placeholder="e.g. SDE-1" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-black text-slate-800 italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Compensation Portfolio</label>
                    <input type="text" name="package" placeholder="e.g. 12.5 LPA" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-black text-slate-800 italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Closing Protocol *</label>
                    <input type="date" name="deadline" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-black text-slate-800 italic">
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Opportunity Description</label>
                    <textarea name="description" rows="3" placeholder="Summary of the role..." class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-black text-slate-800 italic"></textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Requirement Matrix</label>
                    <textarea name="requirements" rows="3" placeholder="Eligibility criteria..." class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-black text-slate-800 italic"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6">
                <button type="button" onclick="document.getElementById('post_modal').classList.add('hidden')" class="flex-1 py-6 bg-slate-50 text-slate-400 font-black rounded-[2rem] hover:bg-slate-100 transition-all uppercase tracking-widest text-[10px] italic">Discard</button>
                <button type="submit" class="flex-2 px-15 py-6 bg-indigo-600 text-white font-black rounded-[2rem] shadow-premium hover:bg-indigo-700 transition-all uppercase tracking-widest text-[10px] italic">Publish Opportunity</button>
            </div>
        </form>
    </div>
</div>

<!-- Applications Modal -->
<div id="apps_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white w-full max-w-4xl rounded-[4rem] shadow-2xl p-12 relative animate-in zoom-in duration-300 max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-12">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase" id="modal_company_name"></h3>
            <button onclick="document.getElementById('apps_modal').classList.add('hidden')" class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-600 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="apps_container" class="space-y-6">
            <!-- Dynamic Apps here -->
        </div>
    </div>
</div>

<script>
async function viewApps(jobId, company) {
    document.getElementById('modal_company_name').textContent = company + ' Applicants';
    document.getElementById('apps_modal').classList.remove('hidden');
    document.getElementById('apps_modal').classList.add('flex');
    
    const container = document.getElementById('apps_container');
    container.innerHTML = '<div class="py-10 text-center font-black text-slate-300 italic uppercase tracking-widest animate-pulse">Synchronizing Stream...</div>';
    
    try {
        const response = await fetch(`placements.php?fetch_apps=${jobId}`);
        const apps = await response.json();
        
        if (apps.length === 0) {
            container.innerHTML = '<div class="py-10 text-center font-black text-slate-300 italic uppercase tracking-widest">No candidates have applied yet.</div>';
            return;
        }
        
        let html = '';
        apps.forEach(app => {
            const statusColors = {
                'Applied': 'bg-slate-100 text-slate-500',
                'Shortlisted': 'bg-indigo-50 text-indigo-600',
                'Rejected': 'bg-rose-50 text-rose-600',
                'Hired': 'bg-emerald-50 text-emerald-600'
            };
            const color = statusColors[app.status] || 'bg-slate-100 text-slate-500';
            
            html += `
                <div class="p-8 bg-slate-50 rounded-[2.5rem] border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 group hover:bg-white transition-all">
                    <div class="flex items-center space-x-6">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-400 shadow-soft border border-slate-100 italic font-black">
                            ${app.full_name.charAt(0)}
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-800 uppercase italic leading-none mb-2">${app.full_name}</h5>
                            <p class="text-[10px] font-bold text-slate-400 italic">${app.roll_no} • ${app.email}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        ${app.resume_path ? `
                            <a href="../uploads/${app.resume_path}" target="_blank" class="px-5 py-2.5 bg-white text-indigo-600 border border-indigo-100 rounded-xl text-[9px] font-black uppercase tracking-widest italic hover:bg-indigo-600 hover:text-white transition-all">Resume</a>
                        ` : ''}
                        
                        <div class="flex items-center space-x-1">
                            <select onchange="updateStatus(${app.id}, this.value)" class="px-4 py-2.5 ${color} rounded-xl text-[9px] font-black uppercase tracking-widest italic outline-none border-none cursor-pointer">
                                <option value="Applied" ${app.status === 'Applied' ? 'selected' : ''}>Applied</option>
                                <option value="Shortlisted" ${app.status === 'Shortlisted' ? 'selected' : ''}>Shortlisted</option>
                                <option value="Rejected" ${app.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                                <option value="Hired" ${app.status === 'Hired' ? 'selected' : ''}>Hired</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    } catch (e) {
        container.innerHTML = '<div class="py-10 text-center font-black text-rose-300 italic uppercase tracking-widest text-xs">Synchronization Error.</div>';
    }
}

function updateStatus(appId, status) {
    if (confirm(`Confirm status transition to: ${status}?`)) {
        window.location.href = `placements.php?id=${appId}&status=${status}`;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>