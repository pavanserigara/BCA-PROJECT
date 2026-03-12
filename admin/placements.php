<?php
$page_title = "Placement & Career Cell";
require_once 'includes/header.php';

$success_message = '';

// Add Placement Opportunity
if (isset($_POST['add_job'])) {
    $company = sanitize($_POST['company']);
    $role = sanitize($_POST['role']);
    $package = sanitize($_POST['package']);
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // For now we'll just store this as a JSON or separate table? 
    // Let's create a quick table via the update script or assume it exists.
    // I already did update_db.php, let me add jobs table to it.
}

$jobs = [
    ['company' => 'Google Inc.', 'role' => 'Software Engineer L3', 'package' => '₹45.0 LPA', 'deadline' => '2025-06-12'],
    ['company' => 'TATA Consultancy', 'role' => 'Systems Engineer', 'package' => '₹7.5 LPA', 'deadline' => '2025-04-30'],
    ['company' => 'Infosys', 'role' => 'Power Programmer', 'package' => '₹9.2 LPA', 'deadline' => '2025-05-15'],
];
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Institutional Placement Hub</h2>
        <p class="text-slate-500 font-medium tracking-tight">Coordinating campus recruitment and career opportunities.
        </p>
    </div>

    <button
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all hover:-translate-y-1 transform active:scale-95 flex items-center space-x-3">
        <i class="fas fa-briefcase text-sm"></i>
        <span>Post Opportunity</span>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
    <div class="lg:col-span-3 bg-white p-12 rounded-[3.5rem] shadow-sm border border-indigo-50 mb-10">
        <div class="flex items-center justify-between mb-12">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight">Active Recruitment Drives</h4>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-loose">Academic Year
                2024-25 Phase</span>
        </div>

        <div class="space-y-6">
            <?php foreach ($jobs as $job): ?>
                <div
                    class="p-8 bg-slate-50/50 rounded-[2.5rem] border border-slate-50 group hover:bg-indigo-50/50 hover:border-indigo-100 transition-all duration-300 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex items-center space-x-6">
                        <div
                            class="w-16 h-16 bg-white rounded-3xl flex items-center justify-center text-indigo-600 font-black text-xl italic shadow-sm">
                            <?php echo substr($job['company'], 0, 1); ?>
                        </div>
                        <div>
                            <h6
                                class="text-xl font-black text-slate-800 tracking-tight leading-none group-hover:text-indigo-600 transition-colors mb-2">
                                <?php echo $job['company']; ?>
                            </h6>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none italic">
                                <?php echo $job['role']; ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-10">
                        <div class="text-center">
                            <p
                                class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 leading-none italic">
                                Offering</p>
                            <p class="text-lg font-black text-slate-800 tracking-tight leading-none">
                                <?php echo $job['package']; ?>
                            </p>
                        </div>
                        <div class="text-center">
                            <p
                                class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1 leading-none italic">
                                Deadline</p>
                            <p class="text-sm font-bold text-rose-500 leading-none">
                                <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                            </p>
                        </div>
                        <button
                            class="bg-white border border-slate-100 text-indigo-600 px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-sm hover:shadow-indigo-50 hover:bg-indigo-600 hover:text-white transition-all transform active:scale-95">
                            Manage Applications
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>