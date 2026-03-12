</div> <!-- End of Page Content Injection Point -->

<footer class="h-16 px-8 border-t border-indigo-50 flex items-center justify-between text-slate-400 text-xs bg-white">
    <div>
        &copy;
        <?php echo date('Y'); ?> <span class="text-indigo-600 font-bold">VidyaSetu</span>. Built for Excellence.
    </div>
    <div class="flex items-center space-x-4">
        <a href="#" class="hover:text-indigo-600 transition-colors">Documentation</a>
        <a href="#" class="hover:text-indigo-600 transition-colors">Support</a>
    </div>
</footer>

</main>
</div>

<!-- Scripts -->
<script>
    // Sidebar Toggle for Mobile
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            setTimeout(() => backdrop.classList.add('opacity-100'), 10);
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.remove('opacity-100');
            setTimeout(() => backdrop.classList.add('hidden'), 300);
        });
    }
</script>
</body>

</html>