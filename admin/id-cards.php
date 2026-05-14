<?php
$page_title = "Identity Protocol";
require_once 'includes/header.php';

$students = $pdo->query("SELECT s.*, u.full_name, c.name as course_name, u.profile_pic 
                         FROM students s 
                         JOIN users u ON s.user_id = u.id 
                         JOIN courses c ON s.course_id = c.id 
                         ORDER BY u.full_name ASC")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic uppercase">Identity Management</h2>
        <p class="text-slate-500 font-medium mt-4 italic">Verification and generation of institutional digital identity protocols.</p>
    </div>
</div>

<div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-100 overflow-hidden mb-20">
    <div class="p-10 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
        <h4 class="text-xl font-black text-slate-800 uppercase italic">Student Registry</h4>
        <div class="flex items-center space-x-4">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Authorized Credentials</span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="py-6 px-10">Institutional Profile</th>
                    <th class="py-6 px-10">Verification ID</th>
                    <th class="py-6 px-10">Program</th>
                    <th class="py-6 px-10 text-right pr-10">Protocol Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($students as $s): ?>
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="py-8 px-10">
                            <div class="flex items-center space-x-4">
                                <?php 
                                    $pic_url = '../assets/images/default_profile.svg';
                                    if (!empty($s['profile_pic'])) {
                                        if (is_file(__DIR__ . '/../../uploads/profiles/' . $s['profile_pic'])) {
                                            $pic_url = '../uploads/profiles/' . $s['profile_pic'];
                                        } elseif (is_file(__DIR__ . '/../../assets/images/' . $s['profile_pic'])) {
                                            $pic_url = '../assets/images/' . $s['profile_pic'];
                                        }
                                    }
                                ?>
                                <img src="<?php echo $pic_url; ?>" class="w-12 h-12 rounded-2xl object-cover shadow-soft ring-2 ring-white border border-slate-100">
                                <div>
                                    <h6 class="text-base font-black text-slate-800 italic leading-none mb-1"><?php echo $s['full_name']; ?></h6>
                                    <p class="text-[9px] font-bold text-primary-600 uppercase tracking-widest">Active Student</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-8 px-10">
                            <span class="text-xs font-black text-slate-400 font-mono">#<?php echo $s['roll_no']; ?></span>
                        </td>
                        <td class="py-8 px-10">
                            <p class="text-xs font-bold text-slate-600 uppercase tracking-tight italic"><?php echo $s['course_name']; ?></p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Semester <?php echo $s['semester']; ?></p>
                        </td>
                        <td class="py-8 px-10 text-right pr-10">
                            <button onclick="previewID(<?php echo htmlspecialchars(json_encode($s)); ?>)" class="px-6 py-3 bg-slate-900 text-white font-black rounded-xl text-[9px] uppercase tracking-widest hover:bg-primary-600 transition-all shadow-xl shadow-slate-900/10 italic">
                                <i class="fas fa-id-card mr-2"></i>Generate Card
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ID Card Modal -->
<div id="id_modal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[100] hidden items-center justify-center p-6 transition-all duration-500">
    <div class="flex flex-col items-center space-y-10">
        <!-- ID Card Preview (CSS Only) -->
        <div id="id_card_print" class="w-[350px] h-[550px] bg-white rounded-[2.5rem] shadow-2xl overflow-hidden relative border-[8px] border-slate-900/5 print:m-0">
            <!-- Header -->
            <div class="h-40 bg-slate-900 flex flex-col items-center justify-center relative overflow-hidden">
                <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center text-white font-black italic text-xl shadow-lg relative z-10 mb-2">V</div>
                <h2 class="text-white font-black text-xl tracking-tighter uppercase italic relative z-10">VidyaSetu</h2>
                <p class="text-primary-400 text-[8px] font-black uppercase tracking-[0.3em] relative z-10">Identity Protocol</p>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary-600/10 rounded-full blur-3xl"></div>
            </div>
            
            <!-- Profile -->
            <div class="flex flex-col items-center -mt-15 relative z-20 px-8">
                <div class="w-36 h-36 rounded-[2.5rem] border-[6px] border-white shadow-2xl overflow-hidden mb-6 bg-white">
                    <img id="card_pic" src="" class="w-full h-full object-cover">
                </div>
                
                <h3 id="card_name" class="text-2xl font-black text-slate-800 tracking-tight leading-none italic uppercase mb-2"></h3>
                <p id="card_course" class="text-[10px] font-black text-primary-600 uppercase tracking-widest italic mb-8"></p>
                
                <div class="w-full space-y-6 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="text-left">
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic leading-none mb-2">Institutional ID</p>
                            <p id="card_roll" class="text-xs font-black text-slate-800 italic leading-none"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic leading-none mb-2">Academic Phase</p>
                            <p id="card_sem" class="text-xs font-black text-slate-800 italic leading-none"></p>
                        </div>
                    </div>
                    
                    <div class="pt-6 flex justify-center">
                        <!-- Mock QR Code -->
                        <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100">
                            <i class="fas fa-qrcode text-4xl text-slate-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-0 inset-x-0 h-16 bg-slate-50 flex items-center justify-center border-t border-slate-100">
                <p class="text-[7px] font-black text-slate-400 uppercase tracking-[0.2em] italic">Authorized Institutional Credential</p>
            </div>
        </div>

        <div class="flex items-center space-x-6">
            <button onclick="document.getElementById('id_modal').classList.add('hidden')" class="px-10 py-5 bg-white/10 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-white/20 transition-all italic">Dismiss</button>
            <button onclick="window.print()" class="px-15 py-5 bg-primary-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-2xl shadow-primary-500/30 hover:bg-primary-700 transition-all transform active:scale-95 italic">
                <i class="fas fa-print mr-2"></i>Confirm & Print
            </button>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #id_modal, #id_modal * { visibility: visible; }
        #id_modal { position: fixed; left: 0; top: 0; background: white; padding: 0; }
        #id_modal button { display: none; }
        #id_card_print { border: none; box-shadow: none; margin: 0; }
    }
</style>

<script>
function previewID(student) {
    document.getElementById('card_name').textContent = student.full_name;
    document.getElementById('card_course').textContent = student.course_name;
    document.getElementById('card_roll').textContent = '#' + student.roll_no;
    document.getElementById('card_sem').textContent = 'SEM ' + student.semester;
    
    // Resolve Image Path for Preview
    let picPath = '../assets/images/default_profile.svg';
    if (student.profile_pic) {
        // We use a trick here: since we don't know for sure in JS if it's in uploads or assets without checking,
        // and we know where it *should* be based on our convention.
        // For simplicity and parity with PHP, we can pass the resolved URL from the table.
        // But since we pass the raw student object, let's just try to point to uploads first.
        picPath = '../uploads/profiles/' + student.profile_pic;
    }
    document.getElementById('card_pic').src = picPath;
    
    // Fallback if not in uploads
    document.getElementById('card_pic').onerror = function() {
        this.src = '../assets/images/default_profile.svg';
    };
    
    document.getElementById('id_modal').classList.remove('hidden');
    document.getElementById('id_modal').classList.add('flex');
}
</script>

<?php require_once 'includes/footer.php'; ?>
