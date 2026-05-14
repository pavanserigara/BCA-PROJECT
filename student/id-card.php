<?php
$page_title = "Digital Identity";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT s.*, u.full_name, c.name as course_name, u.profile_pic 
                       FROM students s 
                       JOIN users u ON s.user_id = u.id 
                       JOIN courses c ON s.course_id = c.id 
                       WHERE s.user_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Institutional Identity</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Your synchronized digital credentials and academic authorization.</p>
    </div>
    
    <button onclick="window.print()" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-premium transition-all transform active:scale-95 flex items-center space-x-3 italic">
        <i class="fas fa-print"></i>
        <span>Print Credentials</span>
    </button>
</div>

<div class="flex justify-center pb-20">
    <!-- ID Card (CSS Only) -->
    <div id="id_card_print" class="w-[350px] h-[550px] bg-white dark:bg-slate-900 rounded-[3rem] shadow-2xl overflow-hidden relative border-[10px] border-slate-950/5 dark:border-white/5 group hover:scale-[1.02] transition-transform duration-500">
        <!-- Header -->
        <div class="h-40 bg-slate-900 dark:bg-black flex flex-col items-center justify-center relative overflow-hidden">
            <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center text-white font-black italic text-xl shadow-lg relative z-10 mb-2">V</div>
            <h2 class="text-white font-black text-xl tracking-tighter uppercase italic relative z-10">VidyaSetu</h2>
            <p class="text-primary-400 text-[8px] font-black uppercase tracking-[0.3em] relative z-10">Identity Protocol</p>
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary-600/10 rounded-full blur-3xl"></div>
        </div>
        
        <!-- Profile -->
        <div class="flex flex-col items-center -mt-15 relative z-20 px-8 text-center">
            <div class="w-36 h-36 rounded-[2.5rem] border-[6px] border-white dark:border-slate-800 shadow-2xl overflow-hidden mb-6 bg-white dark:bg-slate-800">
                <img src="../assets/images/<?php echo $student['profile_pic'] ?: 'default_profile.svg'; ?>" class="w-full h-full object-cover">
            </div>
            
            <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase mb-2"><?php echo $student['full_name']; ?></h3>
            <p class="text-[10px] font-black text-primary-600 uppercase tracking-widest italic mb-8"><?php echo $student['course_name']; ?></p>
            
            <div class="w-full space-y-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <p class="text-[8px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest italic leading-none mb-2">Institutional ID</p>
                        <p class="text-xs font-black text-slate-800 dark:text-white italic leading-none">#<?php echo $student['roll_no']; ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest italic leading-none mb-2">Academic Phase</p>
                        <p class="text-xs font-black text-slate-800 dark:text-white italic leading-none">SEM <?php echo $student['semester']; ?></p>
                    </div>
                </div>
                
                <div class="pt-6 flex justify-center">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-2xl flex items-center justify-center border border-slate-100 dark:border-slate-800 group-hover:bg-white dark:group-hover:bg-slate-800 transition-colors">
                        <i class="fas fa-qrcode text-4xl text-slate-300 dark:text-slate-700 group-hover:text-primary-600/50 transition-colors"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="absolute bottom-0 inset-x-0 h-16 bg-slate-50 dark:bg-black/20 flex items-center justify-center border-t border-slate-100 dark:border-slate-800">
            <p class="text-[7px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.2em] italic">Authorized Institutional Credential</p>
        </div>
    </div>
</div>

<style>
    @media print {
        header, aside, .mb-10 { display: none !important; }
        main { padding: 0 !important; }
        .flex { display: block !important; }
        #id_card_print { 
            margin: 0 auto; 
            box-shadow: none !important; 
            transform: none !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>
