<?php
$page_title = "Events & Cultural Programs";
require_once 'includes/header.php';

$success_message = '';

// Add Event
if (isset($_POST['add_event'])) {
    $title = sanitize($_POST['title']);
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $location = sanitize($_POST['location']);
    $posted_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, location, posted_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $event_date, $location, $posted_by]);
    $success_message = "'$title' has been added to the institutional calendar.";
}

$events = $pdo->query("SELECT e.*, u.full_name as author FROM events e JOIN users u ON e.posted_by = u.id ORDER BY e.event_date ASC")->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Institutional Events</h2>
        <p class="text-slate-500 font-medium">Manage festivals, workshops, and cultural programs.</p>
    </div>

    <button onclick="document.getElementById('event_modal').classList.remove('hidden')"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
        <i class="fas fa-calendar-plus"></i>
        <span>Schedule Flow</span>
    </button>
</div>

<?php if ($success_message): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
    <?php if (empty($events)): ?>
        <div class="lg:col-span-3 py-40 text-center bg-white rounded-[3rem] border border-indigo-50">
            <h3 class="text-2xl font-black text-slate-300 italic">Institutional Calendar Is Open</h3>
        </div>
    <?php else: ?>
        <?php foreach ($events as $e): ?>
            <div
                class="bg-white p-10 rounded-[3rem] shadow-sm border border-indigo-100/50 group hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-300">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <div
                            class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all transform group-hover:rotate-6 mb-8 shadow-sm">
                            <i class="fas fa-masks-theater text-xl"></i>
                        </div>

                        <div class="flex items-center space-x-3 mb-4">
                            <span
                                class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
                                <?php echo date('M d, Y', strtotime($e['event_date'])); ?>
                            </span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">@
                                <?php echo $e['location']; ?>
                            </span>
                        </div>

                        <h3
                            class="text-2xl font-black text-slate-800 leading-tight mb-4 group-hover:text-indigo-600 transition-colors">
                            <?php echo $e['title']; ?>
                        </h3>
                        <p class="text-sm text-slate-500 leading-relaxed italic line-clamp-3 mb-8">
                            <?php echo strip_tags($e['description']); ?>
                        </p>
                    </div>

                    <div class="pt-8 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex -space-x-3">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-100 border border-white flex items-center justify-center text-[10px] font-black text-slate-400 font-black">
                                CS</div>
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-500 border border-white flex items-center justify-center text-[10px] font-black text-white font-black">
                                BCA</div>
                        </div>
                        <button class="text-slate-300 hover:text-indigo-600 transition-all transform active:scale-95"><i
                                class="fas fa-edit mr-3"></i></button>
                        <button class="text-slate-300 hover:text-rose-600 transition-all transform active:scale-95"><i
                                class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="event_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-12 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-3xl font-black text-slate-800 tracking-tight">Schedule New Flow</h3>
            <button onclick="document.getElementById('event_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 bg-slate-50 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="events.php" method="POST" class="space-y-8">
            <input type="hidden" name="add_event" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Event Title
                        Profile</label>
                    <input type="text" name="title" required placeholder="e.g. Annual Tech Summit 2025"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Target
                        Date</label>
                    <input type="date" name="event_date" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Institutional
                        Venue</label>
                    <input type="text" name="location" required placeholder="Main Auditorium"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Detailed
                        Program Profile</label>
                    <textarea name="description" rows="5" required
                        placeholder="Describe the program flow and participation details..."
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:bg-white transition-all outline-none font-bold text-slate-800"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-4">
                <button type="button" onclick="document.getElementById('event_modal').classList.add('hidden')"
                    class="flex-1 py-5 bg-slate-50 text-slate-500 font-black rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs">Wait
                    / Cancel</button>
                <button type="submit"
                    class="flex-1 py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">
                    Confirm Publication
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>