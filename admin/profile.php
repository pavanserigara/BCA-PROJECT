<?php
$page_title = "Institutional Admin Identity";
require_once 'includes/header.php';

$admin_id = $_SESSION['user_id'];

// Fetch Admin detailed info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Administrative Command
            </h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Manage your institutional credentials and
                administrative session profile.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-indigo-600 px-8 py-5 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-100 flex items-center space-x-6">
                <div class="text-right">
                    <p
                        class="text-[10px] font-black text-indigo-300 uppercase tracking-widest leading-none mb-1 leading-none italic">
                        Access Tier</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">Root Administrator</p>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-indigo-400 text-xl">
                    R</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Profile Column -->
        <div class="space-y-12">
            <div class="bg-slate-900 p-15 rounded-[4rem] shadow-2xl relative overflow-hidden group">
                <div class="relative z-10 text-center">
                    <div class="relative w-44 h-44 rounded-[4rem] mx-auto mb-10 overflow-hidden ring-4 ring-indigo-500/20 p-2 shadow-2xl group/img">
                        <?php 
                            $pic_path = !empty($admin['profile_pic']) ? "../uploads/profiles/" . $admin['profile_pic'] : "../assets/images/default_profile.svg";
                        ?>
                        <img id="profile_display" src="<?php echo $pic_path; ?>"
                            class="w-full h-full object-cover rounded-[3.5rem]" alt="Profile">
                        
                        <label for="profile_input" class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 flex items-center justify-center transition-opacity cursor-pointer">
                            <i class="fas fa-camera text-white text-3xl"></i>
                        </label>
                        <input type="file" id="profile_input" class="hidden" accept="image/*">
                    </div>
                    <h4 class="text-2xl font-black text-white italic tracking-tight mb-2 uppercase">
                        <?php echo $admin['full_name']; ?>
                    </h4>
                    <p
                        class="text-[11px] font-black text-indigo-400 uppercase tracking-widest mb-12 italic tracking-[0.2em] opacity-80">
                        <?php echo $admin['role']; ?> Access Tier
                    </p>

                    <div class="pt-10 border-t border-slate-800 flex items-center justify-center space-x-6">
                        <div class="text-center">
                            <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 italic">
                                Institutional ID</p>
                            <p class="text-xl font-black text-white italic">ADM-
                                <?php echo str_pad($admin['id'], 3, '0', STR_PAD_LEFT); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden">
                <h4
                    class="text-xl font-black text-slate-800 tracking-tight italic mb-8 border-b border-indigo-50 pb-6 leading-none">
                    Security Actions</h4>
                <div class="space-y-6">
                    <button
                        class="w-full py-4.5 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all text-xs font-black uppercase tracking-widest italic flex items-center justify-center space-x-3">
                        <i class="fas fa-shield-alt text-[10px]"></i>
                        <span>Rotate Session Token</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Details Column -->
        <div class="lg:col-span-2 space-y-12">
            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden">
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                    <span
                        class="w-10 h-10 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 text-xs italic">A</span>
                    <span>Institutional Registry Profile</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-15 mb-15">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Preferred
                            Mailing Hub</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic">
                            <?php echo $admin['email']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Coordinating
                            Phone</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic">
                            <?php echo $admin['phone'] ?: 'N/A'; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Administrative
                            Namespace</label>
                        <p class="text-xl font-black text-slate-800 tracking-tight italic uppercase">
                            <?php echo $admin['username']; ?>
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Last
                            Active Pulse</label>
                        <p class="text-xl font-black text-indigo-600 tracking-tight italic uppercase">
                            <?php echo date('M d, Y - h:i A'); ?>
                        </p>
                    </div>
                </div>

                <div class="p-12 bg-slate-50 rounded-[3rem] border border-slate-50 italic">
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic leading-none opacity-50">Authorized
                        Residence Profile</label>
                    <p class="text-lg font-bold text-slate-600 leading-relaxed">
                        <?php echo $admin['address'] ?: 'Authorized residence profile not registered.'; ?>
                    </p>
                </div>
            </div>

            <div class="bg-white p-15 rounded-[4rem] shadow-sm border border-indigo-100/30">
                <h4 class="text-2xl font-black text-slate-800 tracking-tight italic mb-12 flex items-center space-x-4">
                    <span
                        class="w-10 h-10 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 text-xs italic">S</span>
                    <span>System Session Index</span>
                </h4>

                <div class="flex items-center space-x-12">
                    <div class="text-center group">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 group-hover:border-indigo-400 transition-all shadow-sm">
                            <i class="fas fa-desktop text-slate-300 group-hover:text-indigo-400"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Browser
                            Environment</p>
                        <p class="text-sm font-black text-slate-800 italic">Edge Stable</p>
                    </div>
                    <div class="text-center group">
                        <div
                            class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 group-hover:border-emerald-400 transition-all shadow-sm">
                            <i class="fas fa-network-wired text-slate-300 group-hover:text-emerald-400"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Protocol Tier
                        </p>
                        <p class="text-sm font-black text-slate-800 italic">HTTPS Secured</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>