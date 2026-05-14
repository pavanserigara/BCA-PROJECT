<?php
$page_title = "Logistics Protocol";
require_once 'includes/header.php';

// Handle Hostel Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_hostel'])) {
    csrf_guard();
    $name = sanitize($_POST['name']);
    $location = sanitize($_POST['location']);
    $capacity = (int) $_POST['capacity'];
    $pdo->prepare("INSERT INTO hostels (name, location, capacity) VALUES (?, ?, ?)")->execute([$name, $location, $capacity]);
}

// Handle Bus Route Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_route'])) {
    csrf_guard();
    $name = sanitize($_POST['route_name']);
    $vehicle = sanitize($_POST['vehicle_no']);
    $driver = sanitize($_POST['driver_name']);
    $capacity = (int) $_POST['capacity'];
    $pdo->prepare("INSERT INTO bus_routes (route_name, vehicle_no, driver_name, capacity) VALUES (?, ?, ?, ?)")->execute([$name, $vehicle, $driver, $capacity]);
}

// Handle Assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_logistics'])) {
    csrf_guard();
    $user_id = (int) $_POST['user_id'];
    $room_id = !empty($_POST['room_id']) ? (int) $_POST['room_id'] : null;
    $route_id = !empty($_POST['route_id']) ? (int) $_POST['route_id'] : null;

    // Check if already assigned
    $stmt = $pdo->prepare("SELECT id FROM logistics_assignments WHERE user_id = ?");
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        $pdo->prepare("UPDATE logistics_assignments SET hostel_room_id = ?, bus_route_id = ? WHERE user_id = ?")->execute([$room_id, $route_id, $user_id]);
    } else {
        $pdo->prepare("INSERT INTO logistics_assignments (user_id, hostel_room_id, bus_route_id) VALUES (?, ?, ?)")->execute([$user_id, $room_id, $route_id]);
    }
    set_flash_message('success', 'Logistics assignment updated successfully.');
}

$hostels = $pdo->query("SELECT * FROM hostels ORDER BY name ASC")->fetchAll();
$routes = $pdo->query("SELECT * FROM bus_routes ORDER BY route_name ASC")->fetchAll();
$students = $pdo->query("SELECT s.user_id, u.full_name, s.roll_no FROM students s JOIN users u ON s.user_id = u.id ORDER BY u.full_name ASC")->fetchAll();

$assignments = $pdo->query("SELECT la.*, u.full_name, h.name as hostel_name, hr.room_no, br.route_name 
                            FROM logistics_assignments la 
                            JOIN users u ON la.user_id = u.id 
                            LEFT JOIN hostel_rooms hr ON la.hostel_room_id = hr.id 
                            LEFT JOIN hostels h ON hr.hostel_id = h.id 
                            LEFT JOIN bus_routes br ON la.bus_route_id = br.id")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Logistics Registry</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Managing institutional housing and transportation protocols.</p>
    </div>
    
    <div class="flex items-center space-x-4">
        <button onclick="document.getElementById('assign_modal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-premium transition-all italic">
            Assign Student
        </button>
    </div>
</div>

<?php display_flash_message(); ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-20">
    <!-- Hostel Management -->
    <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
            <h4 class="text-lg font-black text-slate-800 uppercase italic">Hostel Infrastructure</h4>
            <button onclick="document.getElementById('hostel_modal').classList.remove('hidden')" class="text-[10px] font-black text-primary-600 uppercase tracking-widest italic">Add Hostel</button>
        </div>
        <div class="p-8 space-y-4">
            <?php foreach ($hostels as $h): ?>
                <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 flex items-center justify-between group hover:bg-white transition-all">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-primary-600 shadow-sm border border-slate-100 italic font-black text-lg">H</div>
                        <div>
                            <h6 class="text-sm font-black text-slate-800 uppercase italic"><?php echo $h['name']; ?></h6>
                            <p class="text-[10px] font-bold text-slate-400 italic"><?php echo $h['location']; ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-slate-800 italic"><?php echo $h['capacity']; ?> Units</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Capacity</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Transport Management -->
    <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
            <h4 class="text-lg font-black text-slate-800 uppercase italic">Fleet Network</h4>
            <button onclick="document.getElementById('route_modal').classList.remove('hidden')" class="text-[10px] font-black text-primary-600 uppercase tracking-widest italic">Add Route</button>
        </div>
        <div class="p-8 space-y-4">
            <?php foreach ($routes as $r): ?>
                <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 flex items-center justify-between group hover:bg-white transition-all">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm border border-slate-100 italic font-black text-lg">B</div>
                        <div>
                            <h6 class="text-sm font-black text-slate-800 uppercase italic"><?php echo $r['route_name']; ?></h6>
                            <p class="text-[10px] font-bold text-slate-400 italic"><?php echo $r['vehicle_no']; ?> • <?php echo $r['driver_name']; ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-slate-800 italic"><?php echo $r['capacity']; ?> Seats</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Route Limit</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Assignments Table -->
<div class="bg-white rounded-[4rem] shadow-premium border border-slate-100 overflow-hidden mb-20">
    <div class="p-10 border-b border-slate-50 flex items-center justify-between">
        <h4 class="text-xl font-black text-slate-800 uppercase italic">Logistics Assignments</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="py-6 px-10">Personnel</th>
                    <th class="py-6 px-10">Housing</th>
                    <th class="py-6 px-10">Transit</th>
                    <th class="py-6 px-10 text-right pr-10">Assigned On</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($assignments as $a): ?>
                    <tr class="italic">
                        <td class="py-8 px-10">
                            <p class="text-sm font-black text-slate-800 italic"><?php echo $a['full_name']; ?></p>
                        </td>
                        <td class="py-8 px-10">
                            <p class="text-xs font-bold text-slate-600"><?php echo $a['hostel_name'] ?: 'N/A'; ?></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Room: <?php echo $a['room_no'] ?: '—'; ?></p>
                        </td>
                        <td class="py-8 px-10">
                            <p class="text-xs font-bold text-slate-600"><?php echo $a['route_name'] ?: 'Self-Transit'; ?></p>
                        </td>
                        <td class="py-8 px-10 text-right pr-10">
                            <p class="text-[10px] font-black text-slate-400"><?php echo date('M d, Y', strtotime($a['assigned_at'])); ?></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Assignment Modal -->
<div id="assign_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white w-full max-w-xl rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <div class="flex items-center justify-between mb-12">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Protocol Assignment</h3>
            <button onclick="document.getElementById('assign_modal').classList.add('hidden')" class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-600 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="" method="POST" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="assign_logistics" value="1">
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Target Personnel *</label>
                <select name="user_id" required class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                    <?php foreach($students as $s): ?>
                        <option value="<?php echo $s['user_id']; ?>"><?php echo $s['full_name']; ?> (<?php echo $s['roll_no']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Housing (Optional)</label>
                    <select name="room_id" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                        <option value="">No Housing</option>
                        <!-- In a real app, I'd populate rooms here. For now, empty or mock -->
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic ml-2">Transport (Optional)</label>
                    <select name="route_id" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic">
                        <option value="">Self-Transit</option>
                        <?php foreach($routes as $r): ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo $r['route_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6">
                <button type="button" onclick="document.getElementById('assign_modal').classList.add('hidden')" class="flex-1 py-6 bg-slate-50 text-slate-400 font-black rounded-[2rem] hover:bg-slate-100 transition-all uppercase tracking-widest text-[10px] italic">Discard</button>
                <button type="submit" class="flex-2 px-15 py-6 bg-primary-600 text-white font-black rounded-[2rem] shadow-premium hover:bg-primary-700 transition-all uppercase tracking-widest text-[10px] italic">Execute Assignment</button>
            </div>
        </form>
    </div>
</div>

<!-- Hostel Modal -->
<div id="hostel_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white w-full max-w-lg rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <h3 class="text-2xl font-black text-slate-800 uppercase italic mb-8">Register Infrastructure</h3>
        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="add_hostel" value="1">
            <input type="text" name="name" required placeholder="Hostel Name" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 font-black italic outline-none">
            <input type="text" name="location" required placeholder="Location / Block" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 font-black italic outline-none">
            <input type="number" name="capacity" required placeholder="Total Capacity" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 font-black italic outline-none">
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="document.getElementById('hostel_modal').classList.add('hidden')" class="flex-1 py-5 bg-slate-50 text-slate-400 font-black rounded-[2rem] uppercase tracking-widest text-[10px]">Cancel</button>
                <button type="submit" class="flex-1 py-5 bg-primary-600 text-white font-black rounded-[2rem] uppercase tracking-widest text-[10px]">Register</button>
            </div>
        </form>
    </div>
</div>

<!-- Route Modal -->
<div id="route_modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="bg-white w-full max-w-lg rounded-[3.5rem] shadow-2xl p-12 relative animate-in zoom-in duration-300">
        <h3 class="text-2xl font-black text-slate-800 uppercase italic mb-8">Protocol Route</h3>
        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="add_route" value="1">
            <input type="text" name="route_name" required placeholder="Route Name / Destination" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 font-black italic outline-none">
            <input type="text" name="vehicle_no" required placeholder="Vehicle # (e.g. MH-12-1234)" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 font-black italic outline-none">
            <input type="text" name="driver_name" required placeholder="Driver Personnel" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 font-black italic outline-none">
            <input type="number" name="capacity" required placeholder="Seating Capacity" class="w-full px-8 py-5 bg-slate-50 border-none rounded-[2rem] focus:ring-4 focus:ring-primary-500/10 font-black italic outline-none">
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="document.getElementById('route_modal').classList.add('hidden')" class="flex-1 py-5 bg-slate-50 text-slate-400 font-black rounded-[2rem] uppercase tracking-widest text-[10px]">Cancel</button>
                <button type="submit" class="flex-1 py-5 bg-indigo-600 text-white font-black rounded-[2rem] uppercase tracking-widest text-[10px]">Authorize</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
