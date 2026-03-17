            </div> <!-- End Page Content -->

            <footer
                class="h-10 px-3 sm:px-4 lg:px-6 border-t border-slate-100 flex items-center justify-between text-slate-400 text-[10px] bg-white flex-shrink-0">
                <div class="flex items-center space-x-1">
                    <span>&copy; <?php echo date('Y'); ?></span>
                    <span class="text-primary-600 font-semibold">VidyaSetu</span>
                    <span class="hidden sm:inline">— Faculty Portal</span>
                </div>
                <span class="text-slate-300">v2.0</span>
            </footer>

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-sidebar');
            const closeBtn = document.getElementById('close-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            function openSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                requestAnimationFrame(() => backdrop.classList.add('opacity-100'));
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                if (!sidebar || !backdrop) return;
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                setTimeout(() => { backdrop.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            document.querySelectorAll('[role="alert"]').forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.3s, transform 0.3s';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-6px)';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>
</body>

</html>

