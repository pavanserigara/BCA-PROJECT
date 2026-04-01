<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!has_role('teacher')) {
    header("Location: ../login.php");
    exit();
}

$me = (int) ($_SESSION['user_id'] ?? 0);
$success_message = '';
$error_message = '';

// Selected contact
$selected_user_id = isset($_GET['user']) ? (int) $_GET['user'] : 0;

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = (int) ($_POST['receiver_id'] ?? 0);
    $text = trim($_POST['message'] ?? '');

    if ($receiver_id <= 0 || $receiver_id === $me) {
        $error_message = "Select a valid recipient.";
    } elseif ($text === '') {
        $error_message = "Message cannot be empty.";
    } else {
        try {
            $stmt_u = $pdo->prepare("SELECT id FROM users WHERE id = ? AND status = 'active' LIMIT 1");
            $stmt_u->execute([$receiver_id]);
            if (!$stmt_u->fetchColumn()) {
                $error_message = "Recipient not found.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
                $stmt->execute([$me, $receiver_id, $text]);
                header("Location: messaging.php?user=" . $receiver_id);
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Failed to send: " . $e->getMessage();
        }
    }
}

$page_title = "Messages";
require_once 'includes/header.php';

// Contacts (teachers can message admin + students + other teachers)
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [$me];
$where = "u.id <> ?";
if ($search !== '') {
    $where .= " AND (u.full_name LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
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
               SUBSTRING_INDEX(GROUP_CONCAT(message ORDER BY created_at DESC SEPARATOR '\\n'), '\\n', 1) AS last_message,
               MAX(created_at) AS last_at
        FROM (
            SELECT CASE
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END AS other_id, message, created_at
            FROM messages
            WHERE sender_id = ? OR receiver_id = ?
        ) t
        GROUP BY other_id
    ) m ON m.other_id = u.id
    LEFT JOIN (
        SELECT sender_id AS other_id, COUNT(*) AS unread_count
        FROM messages
        WHERE receiver_id = ? AND is_read = 0
        GROUP BY sender_id
    ) unread ON unread.other_id = u.id
    WHERE $where
    ORDER BY (m.last_at IS NULL) ASC, m.last_at DESC, u.full_name ASC
");
$stmt_contacts->execute(array_merge([$me, $me, $me, $me], $params));
$contacts = $stmt_contacts->fetchAll();

if ($selected_user_id <= 0 && !empty($contacts)) {
    $selected_user_id = (int) $contacts[0]['id'];
}

$selected_user = null;
if ($selected_user_id > 0) {
    $stmt_sel = $pdo->prepare("SELECT id, full_name, role FROM users WHERE id = ? LIMIT 1");
    $stmt_sel->execute([$selected_user_id]);
    $selected_user = $stmt_sel->fetch();
}

if ($selected_user) {
    $stmt_read = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
    $stmt_read->execute([$selected_user_id, $me]);
}

$messages = [];
if ($selected_user) {
    $stmt_msgs = $pdo->prepare("
        SELECT id, sender_id, receiver_id, message, created_at
        FROM messages
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
        ORDER BY created_at ASC
        LIMIT 300
    ");
    $stmt_msgs->execute([$me, $selected_user_id, $selected_user_id, $me]);
    $messages = $stmt_msgs->fetchAll();
}
?>

<div class="max-w-7xl mx-auto flex flex-col h-full">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Messages</h2>
            <p class="text-slate-500 font-medium mt-1">Direct messages with admins, faculty, and students.</p>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle text-[12px]"></i>
            <span><?php echo htmlspecialchars($success_message); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-error" role="alert">
            <i class="fas fa-triangle-exclamation text-[12px]"></i>
            <span><?php echo htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[700px] mb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-indigo-100/30 overflow-hidden flex flex-col">
            <div class="p-4 border-b border-indigo-50 bg-slate-50/50">
                <form method="GET" class="flex items-center gap-3">
                    <input type="hidden" name="user" value="<?php echo (int) $selected_user_id; ?>">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search users..."
                            class="w-full pl-8 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-[12px] placeholder-slate-400 focus:border-primary-300 focus:ring-1 focus:ring-primary-100 transition-all outline-none">
                    </div>
                    <button class="btn btn-secondary" type="submit">Search</button>
                </form>
            </div>
            <div class="flex-1 overflow-y-auto py-2 sidebar-scroll">
                <?php if (empty($contacts)): ?>
                    <div class="p-6 text-slate-500 text-center">
                        <p class="font-semibold">No users found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($contacts as $c): ?>
                        <?php $active = (int) $c['id'] === (int) $selected_user_id; ?>
                        <a href="messaging.php?user=<?php echo (int) $c['id']; ?><?php echo $search !== '' ? '&q=' . urlencode($search) : ''; ?>"
                            class="flex items-center gap-3 px-4 py-3 border-l-4 <?php echo $active ? 'border-primary-600 bg-primary-50/60' : 'border-transparent hover:bg-slate-50'; ?> transition-all">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-primary-700 font-black text-[12px] shadow-sm">
                                <?php echo htmlspecialchars(strtoupper(substr($c['full_name'], 0, 1))); ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-[12px] font-semibold text-slate-800 truncate"><?php echo htmlspecialchars($c['full_name']); ?></p>
                                    <?php if ((int) $c['unread_count'] > 0): ?>
                                        <span class="badge badge-danger"><?php echo (int) $c['unread_count']; ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate">
                                    <?php echo $c['last_message'] ? htmlspecialchars($c['last_message']) : 'No messages yet'; ?>
                                </p>
                                <p class="text-[10px] text-slate-400 capitalize"><?php echo htmlspecialchars($c['role']); ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-2 bg-slate-900 rounded-2xl shadow-2xl flex flex-col overflow-hidden relative">
            <div class="p-4 border-b border-slate-800 bg-slate-900/50 backdrop-blur-xl flex items-center justify-between z-10 sticky top-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white font-black text-[12px] shadow-xl shadow-indigo-900/40">
                        <?php echo $selected_user ? htmlspecialchars(strtoupper(substr($selected_user['full_name'], 0, 1))) : '—'; ?>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-[14px] font-black text-white tracking-tight truncate">
                            <?php echo $selected_user ? htmlspecialchars($selected_user['full_name']) : 'Select a user'; ?>
                        </h4>
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">
                            <?php echo $selected_user ? htmlspecialchars($selected_user['role']) : ''; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 custom-scroll space-y-4" id="chat-scroll">
                <?php if (!$selected_user): ?>
                    <div class="h-full flex items-center justify-center text-slate-400">
                        <p class="font-semibold">Select a user to start messaging.</p>
                    </div>
                <?php elseif (empty($messages)): ?>
                    <div class="h-full flex items-center justify-center text-slate-400">
                        <p class="font-semibold">No messages yet. Send the first message.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                        <?php $out = (int) $m['sender_id'] === $me; ?>
                        <div class="flex <?php echo $out ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-[85%] sm:max-w-[70%]">
                                <div class="<?php echo $out ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-200 border border-slate-700'; ?> px-4 py-3 rounded-2xl <?php echo $out ? 'rounded-tr-none' : 'rounded-tl-none'; ?> shadow">
                                    <p class="text-[13px] leading-relaxed whitespace-pre-wrap"><?php echo htmlspecialchars($m['message']); ?></p>
                                </div>
                                <p class="mt-1 text-[10px] text-slate-500 <?php echo $out ? 'text-right' : 'text-left'; ?>">
                                    <?php echo date('M d, h:i A', strtotime($m['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="p-4 border-t border-slate-800 bg-slate-900/50 backdrop-blur-xl z-10">
                <form method="POST" class="flex items-center gap-3">
                    <input type="hidden" name="send_message" value="1">
                    <input type="hidden" name="receiver_id" value="<?php echo (int) $selected_user_id; ?>">
                    <input type="text" name="message" autocomplete="off" <?php echo $selected_user ? 'required' : 'disabled'; ?>
                        placeholder="<?php echo $selected_user ? 'Type a message…' : 'Select a user first…'; ?>"
                        class="flex-1 bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-[13px] text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="btn btn-primary <?php echo $selected_user ? '' : 'opacity-50 cursor-not-allowed'; ?>" <?php echo $selected_user ? '' : 'disabled'; ?>>
                        <i class="fas fa-paper-plane text-[11px] mr-2"></i>Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const el = document.getElementById('chat-scroll');
        if (el) el.scrollTop = el.scrollHeight;
    })();
</script>

<?php require_once 'includes/footer.php'; ?>

