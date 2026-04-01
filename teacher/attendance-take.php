<?php
$page_title = "Mark Attendance";
require_once 'includes/header.php';

$teacher_id = $_SESSION['user_id'];
$subjects = $pdo->prepare("SELECT s.*, c.name as course_name 
                           FROM subjects s 
                           JOIN courses c ON s.course_id = c.id 
                           JOIN teacher_subjects ts ON s.id = ts.subject_id 
                           WHERE ts.teacher_id = ?");
$subjects->execute([$teacher_id]);
$my_subjects = $subjects->fetchAll();

$students = [];
$attendance_records = [];
$selected_subject = null;
$selected_subject_id = null;
$selected_date = $_GET['date'] ?? date('Y-m-d');

if (isset($_GET['subject_id'])) {
    $selected_subject_id = (int) $_GET['subject_id'];
    
    // Only allow subjects allocated to this faculty
    $stmt_sub = $pdo->prepare("SELECT s.*, c.name as course_name 
                               FROM subjects s 
                               JOIN courses c ON s.course_id = c.id
                               JOIN teacher_subjects ts ON ts.subject_id = s.id
                               WHERE s.id = ? AND ts.teacher_id = ?");
    $stmt_sub->execute([$selected_subject_id, $teacher_id]);
    $selected_subject = $stmt_sub->fetch();

    if ($selected_subject) {
        // Fetch existing attendance records for this date/subject
        $stmt_att = $pdo->prepare("SELECT student_id, status, remarks FROM attendance WHERE subject_id = ? AND date = ?");
        $stmt_att->execute([$selected_subject_id, $selected_date]);
        $rows = $stmt_att->fetchAll();
        foreach ($rows as $row) {
            $attendance_records[$row['student_id']] = $row;
        }

        // Filter students to the subject's course + semester
        $stmt = $pdo->prepare("SELECT s.*, u.full_name 
                               FROM students s 
                               JOIN users u ON s.user_id = u.id 
                               WHERE s.course_id = ? AND s.semester = ?
                               ORDER BY u.full_name ASC");
        $stmt->execute([(int) $selected_subject['course_id'], (int) $selected_subject['semester']]);
        $students = $stmt->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $subject_id = (int) $_POST['subject_id'];
    $date = $_POST['date'];
    $attendance_data = $_POST['attendance']; // Array of student_id => status
    $remarks_data = $_POST['remarks'] ?? []; // Array of student_id => remarks

    try {
        $pdo->beginTransaction();
        
        $valid_status = ['Present' => true, 'Absent' => true, 'Late' => true, 'Leave' => true];

        // Upsert logic
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, subject_id, date, status, marked_by, remarks)
                               VALUES (?, ?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE 
                               status = VALUES(status), 
                               marked_by = VALUES(marked_by),
                               remarks = VALUES(remarks)");

        $written = 0;
        foreach ($attendance_data as $std_id => $status) {
            $sid = (int) $std_id;
            $st = (string) $status;
            $rem = (string) ($remarks_data[$sid] ?? '');
            
            if (!isset($valid_status[$st])) $st = 'Present';

            $stmt->execute([$sid, $subject_id, $date, $st, $teacher_id, $rem]);
            $written++;
        }
        
        $pdo->commit();
        $success = "Saved: Updated " . $written . " records for " . date('d M Y', strtotime($date));
        
        // Refresh local records array to prevent "old" values after POST
        foreach ($attendance_data as $std_id => $status) {
             $attendance_records[$std_id] = [
                 'student_id' => $std_id,
                 'status' => $status,
                 'remarks' => $remarks_data[$std_id] ?? ''
             ];
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Update Error: " . $e->getMessage();
    }
}
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tight italic">Roll Call Sheet</h2>
            <p class="text-slate-500 font-medium italic mt-1">Manage and update attendance with persistent status tracking.</p>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success bg-emerald-500/10 border-emerald-500/20 text-emerald-600 p-6 rounded-3xl mb-10 flex items-center shadow-lg shadow-emerald-500/5 animate__animated animate__fadeInDown">
            <i class="fas fa-check-double text-xl mr-4"></i>
            <span class="font-black italic"><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error bg-rose-500/10 border-rose-500/20 text-rose-500 p-6 rounded-3xl mb-10 flex items-center shadow-lg shadow-rose-500/5">
            <i class="fas fa-exclamation-triangle text-xl mr-4"></i>
            <span class="font-black italic"><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <!-- Session Configuration -->
    <div class="bg-white p-10 rounded-[3rem] border-2 border-slate-50 shadow-2xl shadow-slate-200/50 mb-10">
        <form method="GET" class="flex flex-col lg:flex-row lg:items-end gap-8">
            <div class="flex-1">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Subject / Program Context</label>
                <select name="subject_id" required onchange="this.form.submit()"
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic uppercase text-xs">
                    <option value="">Select Target Subject</option>
                    <?php foreach ($my_subjects as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo ($selected_subject_id == $sub['id']) ? 'selected' : ''; ?>>
                            <?php echo $sub['name']; ?> | <?php echo $sub['course_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-full lg:w-72">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Calendar Date</label>
                <input type="date" name="date" value="<?php echo $selected_date; ?>" onchange="this.form.submit()"
                    class="w-full px-8 py-5 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-black text-slate-800 italic text-xs">
            </div>
        </form>
    </div>

    <?php if ($selected_subject): ?>
        <form action="attendance-take.php?subject_id=<?php echo $selected_subject_id; ?>&date=<?php echo $selected_date; ?>" method="POST"
            class="bg-white rounded-[4rem] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden animate__animated animate__fadeInUp">
            <input type="hidden" name="mark_attendance" value="1">
            <input type="hidden" name="subject_id" value="<?php echo $selected_subject['id']; ?>">
            <input type="hidden" name="date" value="<?php echo $selected_date; ?>">

            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/80">
                            <th class="py-8 px-12">Identification</th>
                            <th class="py-8 px-12 text-center">Status Allocation</th>
                            <th class="py-8 px-12">Justification / Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($students as $student): 
                            $existing = $attendance_records[$student['user_id']] ?? null;
                            $status = $existing['status'] ?? 'Present';
                            $row_class = ($status === 'Absent') ? 'bg-rose-50/30' : '';
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-all <?php echo $row_class; ?>">
                                <td class="py-8 px-12">
                                    <div class="flex items-center space-x-6">
                                        <div class="px-5 py-2.5 bg-slate-100 border border-slate-200/60 rounded-xl flex items-center justify-center text-slate-500 font-black italic text-[11px] tracking-widest min-w-[100px] shadow-sm">
                                            #<?php echo $student['roll_no']; ?>
                                        </div>
                                        <div>
                                            <p class="text-lg font-black text-slate-800 tracking-tight italic"><?php echo $student['full_name']; ?></p>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Student ID #<?php echo $student['user_id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-8 px-12">
                                    <div class="flex items-center justify-center space-x-3">
                                        <?php 
                                        $opts = [
                                            ['Present', 'fa-check-circle', 'bg-emerald-500', 'shadow-emerald-200', 'text-emerald-600', 'bg-emerald-50'],
                                            ['Absent', 'fa-times-circle', 'bg-rose-500', 'shadow-rose-200', 'text-rose-600', 'bg-rose-50'],
                                            ['Late', 'fa-clock', 'bg-amber-500', 'shadow-amber-200', 'text-amber-600', 'bg-amber-50'],
                                            ['Leave', 'fa-info-circle', 'bg-blue-500', 'shadow-blue-200', 'text-blue-600', 'bg-blue-50']
                                        ];
                                        foreach ($opts as $opt):
                                        ?>
                                        <label class="relative flex items-center cursor-pointer group">
                                            <input type="radio" name="attendance[<?php echo $student['user_id']; ?>]"
                                                value="<?php echo $opt[0]; ?>" <?php echo ($status === $opt[0]) ? 'checked' : ''; ?> 
                                                class="peer sr-only">
                                            <div class="flex items-center justify-center space-x-2 px-4 py-3 min-w-[100px] <?php echo $opt[5]; ?> <?php echo $opt[4]; ?> font-black text-[9px] uppercase tracking-widest rounded-2xl transition-all peer-checked:<?php echo $opt[2]; ?> peer-checked:text-white peer-checked:shadow-xl peer-checked:<?php echo $opt[3]; ?> group-hover:scale-105">
                                                <i class="fas <?php echo $opt[1]; ?> text-xs"></i>
                                                <span><?php echo $opt[0]; ?></span>
                                            </div>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td class="py-8 px-12">
                                    <input type="text" name="remarks[<?php echo $student['user_id']; ?>]" 
                                        placeholder="Reason / Note"
                                        value="<?php echo htmlspecialchars($existing['remarks'] ?? ''); ?>"
                                        class="w-full bg-slate-50 border-none rounded-xl px-6 py-3 text-xs font-bold text-slate-700 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:bg-white placeholder-slate-300">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden divide-y divide-slate-100">
                <?php foreach ($students as $student): 
                    $existing = $attendance_records[$student['user_id']] ?? null;
                    $status = $existing['status'] ?? 'Present';
                ?>
                <div class="p-8 space-y-6 <?php echo ($status === 'Absent' ? 'bg-rose-50/30' : ''); ?>">
                    <div class="flex items-center justify-between">
                        <div class="px-5 py-2.5 bg-slate-100 rounded-xl text-slate-500 font-black italic text-[11px] tracking-widest">
                            #<?php echo $student['roll_no']; ?>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ID #<?php echo $student['user_id']; ?></p>
                    </div>
                    <h4 class="text-2xl font-black text-slate-800 tracking-tight italic"><?php echo $student['full_name']; ?></h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <?php foreach ($opts as $opt): ?>
                        <label class="relative flex items-center cursor-pointer group">
                            <input type="radio" name="attendance[<?php echo $student['user_id']; ?>]"
                                value="<?php echo $opt[0]; ?>" <?php echo ($status === $opt[0]) ? 'checked' : ''; ?> 
                                class="peer sr-only">
                            <div class="w-full flex items-center justify-center space-x-2 px-4 py-4 <?php echo $opt[5]; ?> <?php echo $opt[4]; ?> font-black text-[9px] uppercase tracking-widest rounded-2xl transition-all peer-checked:<?php echo $opt[2]; ?> peer-checked:text-white peer-checked:shadow-xl peer-checked:<?php echo $opt[3]; ?>">
                                <i class="fas <?php echo $opt[1]; ?> text-xs"></i>
                                <span><?php echo $opt[0]; ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <input type="text" name="remarks[<?php echo $student['user_id']; ?>]" 
                        placeholder="Reason for change..."
                        value="<?php echo htmlspecialchars($existing['remarks'] ?? ''); ?>"
                        class="w-full bg-slate-50 border-none rounded-2xl px-6 py-5 text-sm font-bold text-slate-700 outline-none placeholder-slate-300">
                </div>
                <?php endforeach; ?>
            </div>

            <div class="p-12 bg-slate-900 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center space-x-6">
                   <div class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center text-indigo-400">
                        <i class="fas fa-file-signature text-2xl"></i>
                   </div>
                   <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic leading-none mb-2">Registry Confirmation</p>
                        <p class="text-white font-black text-lg italic tracking-tight">Review entries before saving updates</p>
                   </div>
                </div>
                <button type="submit"
                    class="w-full md:w-auto px-16 py-6 bg-indigo-600 text-white font-black rounded-[2rem] shadow-2xl shadow-indigo-600/30 hover:bg-indigo-700 hover:-translate-y-1 transition-all uppercase tracking-[0.2em] text-xs">
                    Commit Updates
                </button>
            </div>
        </form>
    <?php elseif (isset($_GET['subject_id'])): ?>
        <div class="py-40 text-center animate__animated animate__fadeIn">
            <div class="w-32 h-32 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 text-slate-300 rotate-12">
                <i class="fas fa-ghost text-5xl"></i>
            </div>
            <h4 class="text-3xl font-black text-slate-900 italic uppercase">Access Limitation</h4>
            <p class="text-slate-500 mt-4 font-medium max-w-sm mx-auto">This resource is either outside your allocation or no students are indexed for this context.</p>
            <a href="attendance-take.php" class="mt-8 inline-block text-xs font-black text-indigo-600 uppercase tracking-widest border-b-2 border-indigo-600">Refresh Configuration</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>