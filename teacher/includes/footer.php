            </main>

            <footer class="p-6 lg:p-10 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-400 dark:text-slate-500 text-[11px] bg-white dark:bg-[#1E293B] mt-auto">
                <div class="flex items-center space-x-1.5">
                    <span class="font-medium">&copy; <?php echo date('Y'); ?></span>
                    <span class="text-primary-600 font-black tracking-tight italic">VidyaSetu</span>
                    <span class="hidden sm:inline opacity-50">•</span>
                    <span class="hidden sm:inline font-medium">Faculty Management Systems</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="hover:text-primary-600 transition-colors">Documentation</a>
                    <a href="#" class="hover:text-primary-600 transition-colors">Support</a>
                    <span class="opacity-30">v2.4.0-stable</span>
                </div>
            </footer>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Theme Toggle Logic
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
            const themeToggleBtn = document.getElementById('theme-toggle');

            // Change the icons inside the button based on previous settings
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            themeToggleBtn.addEventListener('click', function() {
                // toggle icons inside button
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                // if set via local storage previously
                if (localStorage.getItem('color-theme')) {
                    if (localStorage.getItem('color-theme') === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    }
                } else {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                }
            });

            // Sidebar Logic
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-sidebar');
            const closeBtn = document.getElementById('close-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const topNav = document.getElementById('top-nav');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Navbar shadow on scroll
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    topNav.classList.add('shadow-lg', 'shadow-slate-900/5', 'bg-white/95', 'dark:bg-[#0F172A]/95');
                    topNav.classList.replace('lg:h-20', 'lg:h-16');
                } else {
                    topNav.classList.remove('shadow-lg', 'shadow-slate-900/5', 'bg-white/95', 'dark:bg-[#0F172A]/95');
                    topNav.classList.replace('lg:h-16', 'lg:h-20');
                }
            });

            // Alert Auto-dismiss
            document.querySelectorAll('[role="alert"]').forEach(alert => {
                setTimeout(() => {
                    alert.classList.add('opacity-0', '-translate-y-4');
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>

</html>


