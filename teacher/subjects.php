<?php
$page_title = "Institutional Subject Portfolio";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];

// Fetch Assigned Subjects for the teacher
$stmt_subs = $pdo->prepare("SELECT s.*, c.name as course_name 
                            FROM subjects s 
                            JOIN courses c ON s.course_id = c.id 
                            JOIN teacher_subjects ts ON s.id = ts.subject_id 
                            WHERE ts.teacher_id = ? 
                            ORDER BY c.name, s.semester, s.name");
$stmt_subs->execute([$teacher_id]);
$assigned_subjects = $stmt_subs->fetchAll();
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Academic Portfolio</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Monitor and manage your assigned curriculum
                and syllabus flow.</p>
        </div>

        <div class="flex items-center space-x-6">
            <div
                class="bg-indigo-600 px-8 py-4 rounded-[2.5rem] text-white shadow-xl shadow-indigo-100 flex items-center space-x-6">
                <div class="text-right">
                    <p
                        class="text-[10px] font-black text-indigo-300 uppercase tracking-widest leading-none mb-1 leading-none italic">
                        Syllabus Index</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">
                        <?php echo count($assigned_subjects); ?> Subjects
                    </p>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-indigo-400 text-xl">
                    S</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php if (empty($assigned_subjects)): ?>
            <div
                class="md:col-span-3 bg-white p-20 rounded-[4rem] text-center border border-indigo-50 shadow-sm animate__animated animate__fadeIn">
                <div
                    class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                    <i class="fas fa-book-open text-4xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 italic">No Subjects Assigned</h3>
                <p class="text-slate-500 mt-2 italic">Please coordinate with the Administrative Office for subject
                    allocation.</p>
            </div>
        <?php else: ?>
            <?php foreach ($assigned_subjects as $s): ?>
                <div
                    class="bg-white p-10 rounded-[3.5rem] shadow-sm border border-indigo-100/30 group hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-500 relative overflow-hidden flex flex-col justify-between">
                    <div
                        class="absolute top-0 right-0 w-24 h-24 bg-indigo-600/5 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-all">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-8">
                            <span
                                class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 italic uppercase">Cycle
                                <?php echo $s['semester']; ?>
                            </span>
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic font-black">#
                                <?php echo $s['code']; ?>
                            </span>
                        </div>

                        <h3
                            class="text-2xl font-black text-slate-800 tracking-tight leading-tight group-hover:text-indigo-600 transition-colors uppercase italic mb-4">
                            <?php echo $s['name']; ?>
                        </h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-10">•
                            <?php echo $s['course_name']; ?>
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-50 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic">Syllabus
                                    Flow</span>
                            </div>
                            <span class="text-xs font-black text-slate-800 italic">65% Covered</span>
                        </div>

                        <div class="pt-4 flex items-center gap-4">
                            <a href="attendance-take.php?subject_id=<?php echo $s['id']; ?>"
                                class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl flex items-center justify-center font-black text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all italic">Log
                                Presence</a>
                            <button
                                class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-300 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
                                <i class="fas fa-file-alt text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>