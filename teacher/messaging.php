<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$me = (int) ($_SESSION['user_id'] ?? 0);

// Selected contact
$selected_user_id = isset($_GET['user']) ? (int) $_GET['user'] : 0;

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
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

$page_title = "Messages";
require_once 'includes/header.php';

// Contacts
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [$me];
$where = "u.id <> ?";
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

<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Direct Messages</h2>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Communicate with students and colleagues.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-250px)] min-h-[600px]">
    <!-- User List -->
    <div class="lg:col-span-4 bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700 flex flex-col overflow-hidden">
        <div class="p-6 border-b border-slate-50 dark:border-slate-700">
            <form method="GET" class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search contacts..."
                    class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
            </form>
        </div>
        
        <div class="flex-1 overflow-y-auto p-2 space-y-1 custom-scroll">
            <?php foreach ($contacts as $c): ?>
                <?php $active = (int)$c['id'] === $selected_user_id; ?>
                <a href="messaging.php?user=<?php echo $c['id']; ?>" class="flex items-center gap-4 p-4 rounded-2xl transition-all <?php echo $active ? 'bg-primary-50 dark:bg-primary-500/10 border border-primary-100 dark:border-primary-500/20' : 'hover:bg-slate-50 dark:hover:bg-slate-900/50 border border-transparent'; ?>">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-primary-600 font-black text-sm relative">
                        <?php echo strtoupper(substr($c['full_name'], 0, 1)); ?>
                        <?php if ((int)$c['unread_count'] > 0): ?>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-[9px] font-black flex items-center justify-center rounded-full border-2 border-white dark:border-slate-800"><?php echo $c['unread_count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <p class="text-sm font-bold text-slate-800 dark:text-white truncate"><?php echo $c['full_name']; ?></p>
                            <?php if ($c['last_at']): ?>
                                <span class="text-[9px] font-bold text-slate-400 uppercase"><?php echo date('H:i', strtotime($c['last_at'])); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?php echo $c['last_message'] ?: 'No conversation yet'; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="lg:col-span-8 bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-soft border border-slate-100 dark:border-slate-700 flex flex-col overflow-hidden relative">
        <?php if ($selected_user): ?>
            <!-- Chat Header -->
            <div class="p-6 border-b border-slate-50 dark:border-slate-700 flex items-center justify-between bg-white dark:bg-slate-800 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white font-black text-xs">
                        <?php echo strtoupper(substr($selected_user['full_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800 dark:text-white"><?php echo $selected_user['full_name']; ?></h4>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"><?php echo $selected_user['role']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scroll bg-slate-50/50 dark:bg-slate-900/10" id="chat-stream">
                <?php if (empty($messages)): ?>
                    <div class="h-full flex flex-col items-center justify-center text-slate-400">
                        <i class="far fa-comments text-4xl mb-4 opacity-20"></i>
                        <p class="text-sm font-medium">Start the conversation</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                        <?php $is_me = (int)$m['sender_id'] === $me; ?>
                        <div class="flex <?php echo $is_me ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-[80%] md:max-w-[60%]">
                                <div class="p-4 rounded-2xl <?php echo $is_me ? 'bg-primary-600 text-white rounded-tr-none shadow-premium' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 rounded-tl-none border border-slate-100 dark:border-slate-700 shadow-sm'; ?>">
                                    <p class="text-xs leading-relaxed font-medium"><?php echo htmlspecialchars($m['message']); ?></p>
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-2 <?php echo $is_me ? 'text-right' : 'text-left'; ?>">
                                    <?php echo date('h:i A', strtotime($m['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Chat Footer -->
            <div class="p-6 border-t border-slate-50 dark:border-slate-700">
                <form method="POST" class="flex gap-3">
                    <input type="hidden" name="send_message" value="1">
                    <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                    <input type="text" name="message" required autocomplete="off" placeholder="Type your message..."
                        class="flex-1 px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm font-semibold text-slate-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500">
                    <button type="submit" class="w-12 h-12 bg-primary-600 text-white rounded-2xl flex items-center justify-center shadow-premium hover:bg-primary-700 transition-all transform active:scale-90">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-3xl flex items-center justify-center mb-6">
                    <i class="fas fa-inbox text-3xl opacity-20"></i>
                </div>
                <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-2">No Contact Selected</h4>
                <p class="text-xs max-w-xs">Choose a contact from the list on the left to start a conversation.</p>
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

