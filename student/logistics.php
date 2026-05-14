<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('student')) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$page_title = "Logistics Status";
require_once 'includes/header.php';

// Fetch logistics assignment
$stmt = $pdo->prepare("SELECT la.*, h.name as hostel_name, hr.room_no, br.route_name, br.vehicle_no, br.driver_name 
                        FROM logistics_assignments la 
                        LEFT JOIN hostel_rooms hr ON la.hostel_room_id = hr.id 
                        LEFT JOIN hostels h ON hr.hostel_id = h.id 
                        LEFT JOIN bus_routes br ON la.bus_route_id = br.id 
                        WHERE la.user_id = ?");
$stmt->execute([$student_id]);
$logistics = $stmt->fetch();
?>

<div class="mb-10">
    <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Logistics Portfolio</h2>
    <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Verified institutional housing and transit protocols assigned to your profile.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    <!-- Housing Status -->
    <div class="bg-white dark:bg-slate-800 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50 overflow-hidden group">
        <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-indigo-600 shadow-soft border border-slate-100 dark:border-slate-700">
                    <i class="fas fa-hotel text-lg"></i>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase italic">Residential Hub</h4>
            </div>
            <?php if ($logistics && $logistics['hostel_name']): ?>
                <span class="px-4 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 border border-emerald-100 dark:border-emerald-500/20 rounded-xl text-[9px] font-black uppercase tracking-widest italic">Authorized</span>
            <?php else: ?>
                <span class="px-4 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-400 rounded-xl text-[9px] font-black uppercase tracking-widest italic">Not Assigned</span>
            <?php endif; ?>
        </div>
        
        <div class="p-10">
            <?php if ($logistics && $logistics['hostel_name']): ?>
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 italic">Facility Entity</p>
                            <p class="text-xl font-black text-slate-800 dark:text-white italic uppercase"><?php echo $logistics['hostel_name']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 italic">Unit Identifier</p>
                            <p class="text-xl font-black text-indigo-600 italic uppercase"><?php echo $logistics['room_no']; ?></p>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-50 dark:border-slate-700/50">
                        <div class="flex items-center space-x-4 text-slate-500 dark:text-slate-400 italic">
                            <i class="fas fa-info-circle text-indigo-500"></i>
                            <p class="text-xs leading-relaxed">Your residential access is synchronized with the central administration. For maintenance requests, please use the Support portal.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 dark:text-slate-700">
                        <i class="fas fa-house-circle-xmark text-3xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-400 italic">No institutional housing records found for your profile.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Transport Status -->
    <div class="bg-white dark:bg-slate-800 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50 overflow-hidden group">
        <div class="p-10 border-b border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-indigo-600 shadow-soft border border-slate-100 dark:border-slate-700">
                    <i class="fas fa-bus-alt text-lg"></i>
                </div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white uppercase italic">Transit Network</h4>
            </div>
            <?php if ($logistics && $logistics['route_name']): ?>
                <span class="px-4 py-1.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 border border-indigo-100 dark:border-indigo-500/20 rounded-xl text-[9px] font-black uppercase tracking-widest italic">Active Route</span>
            <?php else: ?>
                <span class="px-4 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-400 rounded-xl text-[9px] font-black uppercase tracking-widest italic">Self-Transit</span>
            <?php endif; ?>
        </div>

        <div class="p-10">
            <?php if ($logistics && $logistics['route_name']): ?>
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 italic">Assigned Route</p>
                            <p class="text-xl font-black text-slate-800 dark:text-white italic uppercase"><?php echo $logistics['route_name']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 italic">Vehicle Registry</p>
                            <p class="text-xl font-black text-indigo-600 italic uppercase"><?php echo $logistics['vehicle_no']; ?></p>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-400">
                                <i class="fas fa-id-badge text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest italic">Driver Personnel</p>
                                <p class="text-xs font-black text-slate-700 dark:text-slate-300 italic uppercase"><?php echo $logistics['driver_name']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <div class="flex items-center space-x-4 text-slate-500 dark:text-slate-400 italic">
                            <i class="fas fa-location-crosshairs text-indigo-500"></i>
                            <p class="text-xs leading-relaxed text-[10px]">Real-time GPS synchronization is active during institutional hours.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200 dark:text-slate-700">
                        <i class="fas fa-car text-3xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-400 italic">No active institutional transit subscription detected.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mt-10 bg-indigo-600 rounded-[3.5rem] p-12 text-white relative overflow-hidden group shadow-premium shadow-indigo-500/20">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32 group-hover:scale-110 transition-transform duration-700"></div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="max-w-xl">
            <h3 class="text-3xl font-black tracking-tight italic uppercase mb-4">Emergency Protocol</h3>
            <p class="text-indigo-100 font-medium italic leading-relaxed">In case of residential emergencies or transit delays, please contact the 24/7 Logistics Control Room immediately.</p>
        </div>
        <a href="#" class="px-10 py-6 bg-white text-indigo-600 font-black rounded-[2rem] text-xs uppercase tracking-[0.2em] italic hover:bg-indigo-50 transition-all flex items-center space-x-3 shadow-xl active:scale-95">
            <i class="fas fa-phone-volume"></i>
            <span>Call Control</span>
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
