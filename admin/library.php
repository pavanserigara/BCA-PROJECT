<?php
$page_title = "Library Catalog";
require_once 'includes/header.php';

$success_message = '';
$error_message = '';

// Add Book
if (isset($_POST['add_book'])) {
    $title = sanitize($_POST['title']);
    $author = sanitize($_POST['author']);
    $isbn = sanitize($_POST['isbn']);
    $category = sanitize($_POST['category']);
    $quantity = (int) $_POST['quantity'];

    try {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, category, quantity, available) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $isbn, $category, $quantity, $quantity]);
        $success_message = "New book '$title' added to catalog.";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Issue Book
if (isset($_POST['issue_book'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];
    $issue_date = $_POST['issue_date'];

    try {
        $pdo->beginTransaction();

        // Check availability
        $stmt_check = $pdo->prepare("SELECT available FROM books WHERE id = ?");
        $stmt_check->execute([$book_id]);
        $book = $stmt_check->fetch();

        if ($book && $book['available'] > 0) {
            $stmt = $pdo->prepare("INSERT INTO issued_books (book_id, user_id, issue_date, status) VALUES (?, ?, ?, 'Issued')");
            $stmt->execute([$book_id, $user_id, $issue_date]);

            $stmt_update = $pdo->prepare("UPDATE books SET available = available - 1 WHERE id = ?");
            $stmt_update->execute([$book_id]);

            $pdo->commit();
            $success_message = "Book issued successfully!";
        } else {
            $pdo->rollBack();
            $error_message = "Book not available in library.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Return Book
if (isset($_GET['return'])) {
    $issue_id = (int) $_GET['return'];
    try {
        $pdo->beginTransaction();

        $stmt_issue = $pdo->prepare("SELECT book_id FROM issued_books WHERE id = ?");
        $stmt_issue->execute([$issue_id]);
        $issued = $stmt_issue->fetch();

        if ($issued) {
            $stmt = $pdo->prepare("UPDATE issued_books SET status = 'Returned', return_date = CURDATE() WHERE id = ?");
            $stmt->execute([$issue_id]);

            $stmt_update = $pdo->prepare("UPDATE books SET available = available + 1 WHERE id = ?");
            $stmt_update->execute([$issued['book_id']]);

            $pdo->commit();
            $success_message = "Book returned and inventory updated.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}

$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();
$issued = $pdo->query("SELECT ib.*, b.title as book_title, u.full_name as user_name FROM issued_books ib JOIN books b ON ib.book_id = b.id JOIN users u ON ib.user_id = u.id WHERE ib.status = 'Issued' ORDER BY ib.issue_date DESC")->fetchAll();
$students = $pdo->query("SELECT id, full_name FROM users WHERE role = 'student' ORDER BY full_name")->fetchAll();
?>

<div class="flex items-center justify-between mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Institutional Library</h2>
        <p class="text-slate-500 font-medium">Catalog management and resource distribution system.</p>
    </div>

    <div class="flex items-center space-x-4">
        <button onclick="document.getElementById('issue_modal').classList.remove('hidden')"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 shadow-lg shadow-indigo-100 transition-all hover:-translate-y-0.5">
            <i class="fas fa-hand-holding"></i>
            <span>Issue Resource</span>
        </button>
        <button onclick="document.getElementById('add_book_modal').classList.remove('hidden')"
            class="bg-white border border-indigo-100 text-indigo-600 px-8 py-3.5 rounded-2xl font-bold flex items-center space-x-2 transition-all hover:bg-indigo-50">
            <i class="fas fa-plus"></i>
            <span>Add to Catalog</span>
        </button>
    </div>
</div>

<?php if ($success_message): ?>
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl mb-8 flex items-center">
        <i class="fas fa-check-circle text-2xl mr-4"></i>
        <p class="text-sm font-bold">
            <?php echo $success_message; ?>
        </p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-20">
    <!-- Active Issues -->
    <div class="lg:col-span-2 bg-white rounded-[3rem] shadow-sm border border-indigo-100/50 overflow-hidden">
        <div class="p-10 border-b border-indigo-50 bg-slate-50/50 flex items-center justify-between">
            <h4 class="text-xl font-bold text-slate-800">Resources Currently Out</h4>
            <span
                class="text-xs font-black text-slate-400 uppercase tracking-widest tracking-widest bg-white px-4 py-2 rounded-xl">
                <?php echo count($issued); ?> Active Loads
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-indigo-50">
                        <th class="py-6 px-10">Resource Title</th>
                        <th class="py-6 px-10">Issued To</th>
                        <th class="py-6 px-10">Date</th>
                        <th class="py-6 px-10 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-50/30">
                    <?php if (empty($issued)): ?>
                        <tr>
                            <td colspan="4" class="p-20 text-center italic text-slate-400">Inventory is currently
                                stationary.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($issued as $is): ?>
                            <tr class="group hover:bg-slate-50 transition-all">
                                <td class="py-6 px-10"><span class="font-bold text-slate-800">
                                        <?php echo $is['book_title']; ?>
                                    </span></td>
                                <td class="py-6 px-10"><span class="text-sm font-medium text-slate-500">
                                        <?php echo $is['user_name']; ?>
                                    </span></td>
                                <td class="py-6 px-10"><span class="text-xs font-black text-slate-400">
                                        <?php echo date('d M, Y', strtotime($is['issue_date'])); ?>
                                    </span></td>
                                <td class="py-6 px-10 text-right">
                                    <a href="library.php?return=<?php echo $is['id']; ?>"
                                        class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-emerald-100/50 hover:bg-emerald-500 hover:text-white transition-all">
                                        Mark Return
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Catalog Quick View -->
    <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl flex flex-col">
        <h4 class="text-2xl font-black text-white tracking-tight mb-8">Stock Overview</h4>
        <div class="space-y-6 flex-1 overflow-y-auto pr-2 scrollbar-hide">
            <?php foreach ($books as $book): ?>
                <div
                    class="p-6 bg-slate-800 rounded-2xl border border-slate-700/50 flex items-center justify-between group transform hover:-translate-x-1 transition-all">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-10 h-10 bg-indigo-500/10 rounded-lg flex items-center justify-center text-indigo-500 font-black text-xs">
                            Bk
                        </div>
                        <div>
                            <h6 class="text-white font-bold text-sm tracking-tight leading-none mb-1">
                                <?php echo $book['title']; ?>
                            </h6>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest italic">
                                <?php echo $book['author']; ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-black text-indigo-400">
                            <?php echo $book['available']; ?>
                        </div>
                        <div class="text-[8px] font-black text-slate-600 uppercase tracking-widest">Left</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Book Modal -->
<div id="add_book_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-slate-800">Add Resource</h3>
            <button onclick="document.getElementById('add_book_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="library.php" method="POST" class="space-y-6">
            <input type="hidden" name="add_book" value="1">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Resource
                        Title</label>
                    <input type="text" name="title" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Author /
                        Publisher</label>
                    <input type="text" name="author" required
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">ISBN
                            #</label>
                        <input type="text" name="isbn"
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Category</label>
                        <select name="category"
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-700">
                            <option>CS / IT</option>
                            <option>Physics</option>
                            <option>Mathematics</option>
                            <option>Humanities</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Total
                        Quantity</label>
                    <input type="number" name="quantity" required value="1"
                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
                </div>
            </div>
            <button type="submit"
                class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">Add
                to Shelf</button>
        </form>
    </div>
</div>

<!-- Issue Resource Modal -->
<div id="issue_modal"
    class="fixed inset-0 bg-slate-900/60 z-[100] hidden items-center justify-center p-4 backdrop-blur-sm transition-all flex">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 animate__animated animate__zoomIn">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-slate-800">Issue Flow</h3>
            <button onclick="document.getElementById('issue_modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="library.php" method="POST" class="space-y-6">
            <input type="hidden" name="issue_book" value="1">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Resource
                    Select</label>
                <select name="book_id" required
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-700">
                    <?php foreach ($books as $b): ?>
                        <option value="<?php echo $b['id']; ?>">
                            <?php echo $b['title']; ?> (
                            <?php echo $b['available']; ?> in stock)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Issued To
                    (Student)</label>
                <select name="user_id" required
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold text-slate-700">
                    <?php foreach ($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>">
                            <?php echo $s['full_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Issue Date</label>
                <input type="date" name="issue_date" required value="<?php echo date('Y-m-d'); ?>"
                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none font-bold">
            </div>
            <button type="submit"
                class="w-full py-5 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all uppercase tracking-widest text-xs">Authorize
                Movement</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>