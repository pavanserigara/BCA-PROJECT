<?php
// Footer script for dark mode toggle and responsive behaviors
?>
            </main>

            <footer class="mt-auto py-8 px-6 lg:px-10 border-t border-slate-100 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">&copy; <?php echo date('Y'); ?> VidyaSetu v2.0.1</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                    <span class="text-[10px] font-black text-primary-600 uppercase tracking-widest">Premium Student Edition</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="complaints.php" class="text-[10px] font-bold text-slate-500 dark:text-slate-400 hover:text-primary-600 transition-colors uppercase tracking-widest">Support</a>
                    <a href="profile.php" class="text-[10px] font-bold text-slate-500 dark:text-slate-400 hover:text-primary-600 transition-colors uppercase tracking-widest">Security</a>
                </div>
            </footer>
        </div>
    </div>

    <!-- Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[55] hidden transition-all duration-300 opacity-0 lg:hidden pointer-events-none"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggleBtn = document.getElementById('toggle-sidebar');
            const navbar = document.getElementById('top-nav');
            
            // Sidebar Toggle
            function toggleSidebar() {
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                if (!isOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                    setTimeout(() => backdrop.classList.add('opacity-100'), 10);
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.remove('opacity-100');
                    setTimeout(() => backdrop.classList.add('hidden'), 300);
                }
            }

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (backdrop) backdrop.addEventListener('click', toggleSidebar);

            // Navbar Blur on Scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 10) {
                    navbar.classList.add('h-16', 'shadow-soft');
                    navbar.classList.remove('h-20');
                } else {
                    navbar.classList.remove('h-16', 'shadow-soft');
                    navbar.classList.add('h-20');
                }
            });

            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            function syncThemeIcon() {
                if (document.documentElement.classList.contains('dark')) {
                    themeToggleLightIcon.classList.remove('hidden');
                    themeToggleDarkIcon.classList.add('hidden');
                } else {
                    themeToggleDarkIcon.classList.remove('hidden');
                    themeToggleLightIcon.classList.add('hidden');
                }
            }

            syncThemeIcon();

            themeToggleBtn.addEventListener('click', function() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                syncThemeIcon();
            });
        });

        // Flash message helper for animations
        window.setTimeout(() => {
            document.querySelectorAll('[role="alert"]').forEach(alert => {
                alert.classList.add('opacity-0', '-translate-y-4');
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
