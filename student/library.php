<?php
$page_title = "Library Catalog Search";
require_once 'includes/header.php';

$student_id = $_SESSION['user_id'];

// Get my issued books
$stmt_my = $pdo->prepare("SELECT ib.*, b.title as book_title, b.author as book_author 
                          FROM issued_books ib 
                          JOIN books b ON ib.book_id = b.id 
                          WHERE ib.user_id = ? AND ib.status = 'Issued'
                          ORDER BY ib.issue_date DESC");
$stmt_my->execute([$student_id]);
$my_books = $stmt_my->fetchAll();

// Search Books
$search_query = isset($_GET['q']) ? $_GET['q'] : '';
$books = [];
if ($search_query) {
    $stmt_search = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ?");
    $stmt_search->execute(["%$search_query%", "%$search_query%", "%$search_query%"]);
    $books = $stmt_search->fetchAll();
} else {
    $books = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 12")->fetchAll();
}
?>

<div class="max-w-7xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Academic Library</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Discover research papers, textbooks, and
                institutional resources.</p>
        </div>

        <div class="flex items-center space-x-4">
            <div class="bg-indigo-600 px-8 py-4 rounded-[2rem] text-white shadow-xl flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest leading-none mb-1">Active
                        Loans</p>
                    <p class="text-lg font-black text-white tracking-tight leading-none italic">
                        <?php echo count($my_books); ?> Items
                    </p>
                </div>
                <div
                    class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white font-black italic shadow-lg shadow-indigo-400">
                    03</div>
            </div>
        </div>
    </div>

    <!-- Active Loans Section -->
    <?php if (!empty($my_books)): ?>
        <div class="mb-20 animate__animated animate__fadeInUp">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight mb-8 flex items-center space-x-4 italic">
                <span
                    class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xs italic">A</span>
                <span>Currently on Loan</span>
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($my_books as $mb): ?>
                    <div
                        class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group border border-slate-800">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform">
                        </div>
                        <div class="flex flex-col h-full justify-between gap-8">
                            <div>
                                <span
                                    class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-4 block italic font-black">Issue
                                    #REC-
                                    <?php echo $mb['id']; ?>
                                </span>
                                <h5
                                    class="text-2xl font-black text-white tracking-tight leading-tight group-hover:text-indigo-400 transition-colors italic">
                                    <?php echo $mb['book_title']; ?>
                                </h5>
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-2 italic">
                                    <?php echo $mb['book_author']; ?>
                                </p>
                            </div>
                            <div class="pt-8 border-t border-slate-800 flex items-center justify-between">
                                <div>
                                    <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1 italic">Date
                                        of Issue</p>
                                    <p class="text-sm font-bold text-slate-300">
                                        <?php echo date('M d, Y', strtotime($mb['issue_date'])); ?>
                                    </p>
                                </div>
                                <button
                                    class="w-12 h-12 bg-slate-800 text-slate-500 rounded-2xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all transform active:scale-95 shadow-sm shadow-slate-900 border border-slate-700">
                                    <i class="fas fa-arrow-rotate-left text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search Catalog -->
    <div
        class="bg-white p-12 rounded-[4rem] shadow-sm border border-indigo-100/30 overflow-hidden mb-20 animate__animated animate__fadeIn">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-10 mb-15">
            <h4 class="text-2xl font-black text-slate-800 tracking-tight leading-none italic">Discover Institutional
                Catalog</h4>
            <form method="GET"
                class="flex-1 max-w-lg bg-slate-100 px-8 py-4.5 rounded-[2rem] flex items-center space-x-6 group focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-500/10 transition-all shadow-sm">
                <i
                    class="fas fa-search text-slate-300 group-focus-within:text-indigo-600 text-lg transition-colors"></i>
                <input type="text" name="q" value="<?php echo htmlspecialchars($search_query); ?>"
                    placeholder="Search by Title, Author, or ISBN..."
                    class="bg-transparent border-none focus:ring-0 flex-1 text-slate-800 font-bold text-base placeholder-slate-400 italic">
                <button type="submit" class="hidden">Run</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (empty($books)): ?>
                <div class="col-span-4 py-20 text-center italic text-slate-400">No resources found matching your current
                    search criteria.</div>
            <?php else: ?>
                <?php foreach ($books as $b): ?>
                    <div
                        class="bg-slate-50/50 p-10 rounded-[3rem] border border-slate-50 group hover:bg-white hover:shadow-2xl hover:shadow-indigo-50 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div
                                class="w-12 h-12 bg-white rounded-2xl mb-8 flex items-center justify-center text-indigo-500 font-black text-[10px] italic shadow-sm hover:shadow-indigo-100 transition-all transform group-hover:rotate-6">
                                BK</div>
                            <h6
                                class="text-xl font-black text-slate-800 tracking-tight leading-tight group-hover:text-indigo-600 transition-colors italic">
                                <?php echo $b['title']; ?>
                            </h6>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2 italic">
                                <?php echo $b['author']; ?>
                            </p>
                            <p
                                class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 mt-6 inline-block uppercase tracking-widest italic">
                                <?php echo $b['category']; ?>
                            </p>
                        </div>

                        <div class="mt-10 pt-8 border-t border-indigo-50/30 flex items-center justify-between">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">
                                    Availability</p>
                                <p
                                    class="font-black <?php echo $b['available'] > 0 ? 'text-emerald-500' : 'text-rose-500'; ?> text-xl leading-none tracking-tight">
                                    <?php echo $b['available']; ?> <span
                                        class="text-[10px] text-slate-300 uppercase italic">Stock</span>
                                </p>
                            </div>
                            <button
                                class="w-10 h-10 bg-white border border-slate-100 rounded-xl text-slate-300 flex items-center justify-center group-hover:text-indigo-600 group-hover:border-indigo-600 transition-all animate-pulse">
                                <i class="fas fa-circle-info text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>