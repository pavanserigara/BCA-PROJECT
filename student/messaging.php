<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('student')) {
    header("Location: ../login.php");
    exit();
}

$me = (int) ($_SESSION['user_id'] ?? 0);
$selected_user_id = isset($_GET['user']) ? (int) $_GET['user'] : 0;

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    csrf_guard();
    $receiver_id = (int) ($_POST['receiver_id'] ?? 0);
    $text = trim($_POST['message'] ?? '');

    if ($receiver_id > 0 && $receiver_id !== $me && $text !== '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$me, $receiver_id, $text]);
            header("Location: messaging.php?user=" . $receiver_id);
            exit();
        } catch (PDOException $e) { $error = $e->getMessage(); }
    }
}

$page_title = "Direct Messaging Hub";
require_once 'includes/header.php';

// Contacts (Teachers and Admins)
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [$me];
$where = "u.id <> ? AND u.role IN ('teacher', 'admin')";
if ($search !== '') {
    $where .= " AND (u.full_name LIKE ? OR u.username LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
}

$stmt_contacts = $pdo->prepare("
    SELECT u.id, u.full_name, u.role,
           COALESCE(m.last_message, '') as last_message,
           m.last_at,
           COALESCE(unread.unread_count, 0) as unread_count
    FROM users u
    LEFT JOIN (
        SELECT other_id,
               SUBSTRING_INDEX(GROUP_CONCAT(message ORDER BY created_at DESC SEPARATOR '|'), '|', 1) AS last_message,
               MAX(created_at) AS last_at
        FROM (
            SELECT CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS other_id, message, created_at
            FROM messages WHERE sender_id = ? OR receiver_id = ?
        ) t GROUP BY other_id
    ) m ON m.other_id = u.id
    LEFT JOIN (
        SELECT sender_id AS other_id, COUNT(*) AS unread_count
        FROM messages WHERE receiver_id = ? AND is_read = 0 GROUP BY sender_id
    ) unread ON unread.other_id = u.id
    WHERE $where
    ORDER BY m.last_at DESC, u.full_name ASC
");
$stmt_contacts->execute(array_merge([$me, $me, $me, $me], $params));
$contacts = $stmt_contacts->fetchAll();

if ($selected_user_id <= 0 && !empty($contacts)) $selected_user_id = (int)$contacts[0]['id'];

$selected_user = null;
if ($selected_user_id > 0) {
    $stmt_sel = $pdo->prepare("SELECT id, full_name, role FROM users WHERE id = ? LIMIT 1");
    $stmt_sel->execute([$selected_user_id]);
    $selected_user = $stmt_sel->fetch();
    if ($selected_user) {
        $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0")->execute([$selected_user_id, $me]);
    }
}

$messages = [];
if ($selected_user) {
    $stmt_msgs = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt_msgs->execute([$me, $selected_user_id, $selected_user_id, $me]);
    $messages = $stmt_msgs->fetchAll();
}
?>

<div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none italic uppercase">Communication Hub</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-4 font-medium italic">Direct interface with institutional faculty and administrative personnel.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-10 h-[700px] mb-20 pb-10">
    <!-- User List -->
    <div class="lg:col-span-4 bg-white dark:bg-slate-800 rounded-[3.5rem] shadow-premium border border-slate-100 dark:border-slate-800 flex flex-col overflow-hidden">
        <div class="p-8 border-b border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/50">
            <form method="GET" class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search contacts..."
                    class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
            </form>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scroll">
            <?php foreach ($contacts as $c): ?>
                <?php $active = (int)$c['id'] === $selected_user_id; ?>
                <a href="messaging.php?user=<?php echo $c['id']; ?>" class="flex items-center gap-4 p-5 rounded-[2.5rem] transition-all <?php echo $active ? 'bg-primary-600 text-white shadow-premium' : 'hover:bg-slate-50 dark:hover:bg-slate-900 border border-transparent'; ?>">
                    <div class="w-12 h-12 rounded-2xl <?php echo $active ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900'; ?> flex items-center justify-center font-black text-sm relative">
                        <?php echo strtoupper(substr($c['full_name'], 0, 1)); ?>
                        <?php if ((int)$c['unread_count'] > 0): ?>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-[9px] font-black flex items-center justify-center rounded-full border-2 border-white dark:border-slate-800"><?php echo $c['unread_count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <p class="text-sm font-black uppercase italic truncate"><?php echo $c['full_name']; ?></p>
                        </div>
                        <p class="text-[10px] <?php echo $active ? 'text-white/70' : 'text-slate-400'; ?> font-bold uppercase tracking-widest truncate"><?php echo $c['last_message'] ?: 'Initiate Transmission'; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="lg:col-span-8 bg-slate-950 rounded-[4rem] shadow-premium flex flex-col overflow-hidden relative border border-slate-900">
        <?php if ($selected_user): ?>
            <!-- Chat Header -->
            <div class="p-8 border-b border-slate-900 bg-slate-950/50 backdrop-blur-xl flex items-center justify-between z-10 sticky top-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary-600 flex items-center justify-center text-white font-black text-lg italic shadow-xl">
                        <?php echo strtoupper(substr($selected_user['full_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-white italic uppercase tracking-tight"><?php echo $selected_user['full_name']; ?></h4>
                        <span class="text-[9px] font-black text-primary-500 uppercase tracking-[0.2em] italic"><?php echo $selected_user['role']; ?> Personnel</span>
                    </div>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-10 space-y-8 custom-scroll" id="chat-stream">
                <?php if (empty($messages)): ?>
                    <div class="h-full flex flex-col items-center justify-center text-slate-600">
                        <i class="fas fa-satellite-dish text-5xl mb-6 opacity-20"></i>
                        <p class="text-sm font-black uppercase tracking-widest italic">Secure Link Established</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                        <?php $is_me = (int)$m['sender_id'] === $me; ?>
                        <div class="flex <?php echo $is_me ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-[85%] md:max-w-[70%]">
                                <div class="p-6 rounded-[2.5rem] <?php echo $is_me ? 'bg-primary-600 text-white rounded-tr-none shadow-premium' : 'bg-slate-900 text-slate-300 rounded-tl-none border border-slate-800'; ?>">
                                    <p class="text-sm leading-relaxed font-bold italic"><?php echo htmlspecialchars($m['message']); ?></p>
                                </div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mt-3 <?php echo $is_me ? 'text-right' : 'text-left'; ?>">
                                    <?php echo date('h:i A', strtotime($m['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Chat Footer -->
            <div class="p-8 border-t border-slate-900">
                <form method="POST" class="flex gap-4">
                    <input type="hidden" name="send_message" value="1">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                    <input type="text" name="message" required autocomplete="off" placeholder="Type transmission..."
                        class="flex-1 px-8 py-5 bg-slate-900 border border-slate-800 rounded-[2rem] text-sm font-bold text-white italic outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/20 transition-all">
                    <button type="submit" class="w-16 h-16 bg-primary-600 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl hover:bg-primary-700 transition-all transform active:scale-90">
                        <i class="fas fa-paper-plane text-xl"></i>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="h-full flex flex-col items-center justify-center text-slate-600 p-10 text-center">
                <div class="w-24 h-24 bg-slate-900 rounded-[2.5rem] flex items-center justify-center mb-8 border border-slate-800">
                    <i class="fas fa-user-shield text-4xl opacity-10"></i>
                </div>
                <h4 class="text-xl font-black text-white italic uppercase tracking-tight mb-3">Receiver Undefined</h4>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] max-w-xs">Select a verified institutional personnel from the directory to initiate a secure transmission.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const chat = document.getElementById('chat-stream');
        if (chat) chat.scrollTop = chat.scrollHeight;
    });
</script>

<?php require_once 'includes/footer.php'; ?>
