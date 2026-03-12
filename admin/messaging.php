<?php
$page_title = "Messaging Hub";
require_once 'includes/header.php';

$success_message = '';

// In a real app, this would use a messages table. 
// For simulation, we'll just show the high-fidelity UI and structure.
?>

<div class="max-w-7xl mx-auto flex flex-col h-full">
    <div class="flex items-center justify-between mb-15">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none italic">Communication Profile</h2>
            <p class="text-slate-500 font-medium tracking-tight mt-4 italic">Collaborative workspace for institutional
                coordination.</p>
        </div>

        <div class="flex items-center space-x-6">
            <button
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-[2rem] font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-100 transition-all hover:-translate-y-1 transform active:scale-95 flex items-center space-x-3">
                <i class="fas fa-plus-circle text-sm"></i>
                <span>Direct Message</span>
            </button>
        </div>
    </div>

    <!-- Messaging Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 h-[700px] mb-20 animate__animated animate__fadeInUp">
        <!-- Sidebar Contacts -->
        <div class="bg-white rounded-[3.5rem] shadow-sm border border-indigo-100/30 overflow-hidden flex flex-col">
            <div class="p-10 border-b border-indigo-50 bg-slate-50/50">
                <div class="bg-white border border-slate-100 px-6 py-4.5 rounded-2xl flex items-center space-x-4">
                    <i class="fas fa-search text-slate-300 text-sm"></i>
                    <input type="text" placeholder="Search Channels..."
                        class="bg-transparent border-none focus:ring-0 text-sm font-bold text-slate-800 placeholder-slate-300">
                </div>
            </div>
            <div class="flex-1 overflow-y-auto pr-2 scrollbar-hide py-10 space-y-2">
                <div
                    class="px-10 py-6 bg-indigo-50/50 border-r-4 border-indigo-600 flex items-center space-x-5 cursor-pointer transition-all">
                    <div
                        class="w-14 h-14 bg-white rounded-3xl flex items-center justify-center text-indigo-600 font-black text-xs italic shadow-sm">
                        IT</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1 leading-none">
                            <h6 class="text-base font-black text-slate-800 tracking-tight leading-none italic"># BCA
                                Dept Group</h6>
                            <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">Active</span>
                        </div>
                        <p class="text-xs text-slate-400 font-medium line-clamp-1 italic">Prof. Sharma: Agenda for next
                            semester...</p>
                    </div>
                </div>

                <div
                    class="px-10 py-6 hover:bg-slate-50 flex items-center space-x-5 cursor-pointer transition-all border-r-4 border-transparent">
                    <div
                        class="w-14 h-14 bg-emerald-50 rounded-3xl flex items-center justify-center text-emerald-600 font-black text-xs italic shadow-sm">
                        SP</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1 leading-none">
                            <h6 class="text-base font-black text-slate-800 tracking-tight leading-none italic">Student
                                Placement Cell</h6>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">10:45 AM</span>
                        </div>
                        <p class="text-xs text-slate-400 font-medium">New job alert from Google...</p>
                    </div>
                </div>

                <div
                    class="px-10 py-6 hover:bg-slate-50 flex items-center space-x-5 cursor-pointer transition-all border-r-4 border-transparent">
                    <div
                        class="w-14 h-14 bg-rose-50 rounded-3xl flex items-center justify-center text-rose-600 font-black text-xs italic shadow-sm">
                        EX</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1 leading-none">
                            <h6 class="text-base font-black text-slate-800 tracking-tight leading-none italic">Exam
                                Coordination</h6>
                            <span
                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Yesterday</span>
                        </div>
                        <p class="text-xs text-slate-400 font-medium">Draft datesheet is finalized.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat View Area -->
        <div class="lg:col-span-2 bg-slate-900 rounded-[4rem] shadow-2xl flex flex-col overflow-hidden relative">
            <div
                class="p-10 border-b border-slate-800 bg-slate-900/50 backdrop-blur-xl flex items-center justify-between z-10 sticky top-0">
                <div class="flex items-center space-x-6">
                    <div
                        class="w-14 h-14 bg-indigo-500 rounded-3xl flex items-center justify-center text-white font-black text-sm italic shadow-xl shadow-indigo-900/40">
                        IT</div>
                    <div>
                        <h4 class="text-2xl font-black text-white tracking-tight italic leading-none mb-1"># BCA Dept
                            Group</h4>
                        <div
                            class="flex items-center space-x-2 text-[10px] font-black text-slate-500 uppercase tracking-widest italic">
                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span>18 Members Participating</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition-all"><i
                            class="fas fa-phone-alt text-xs"></i></button>
                    <button class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition-all"><i
                            class="fas fa-video text-xs"></i></button>
                </div>
            </div>

            <!-- Chat Bubbles -->
            <div class="flex-1 overflow-y-auto p-12 scrollbar-hide space-y-12">
                <div class="flex flex-col items-start gap-3 max-w-lg">
                    <div class="flex items-center space-x-3 mb-1">
                        <span
                            class="text-[9px] font-black text-indigo-400 uppercase tracking-widest italic font-black">Prof.
                            S. Verma</span>
                    </div>
                    <div
                        class="p-8 bg-slate-800 border border-slate-700 rounded-[2.5rem] rounded-tl-none text-slate-300 text-base font-medium leading-loose shadow-xl">
                        Good morning everyone. I've updated the syllabus for "Cloud Computing" in Semester 5. Please
                        review and provide feedback.
                    </div>
                    <span class="text-[8px] font-black text-slate-600 uppercase tracking-widest italic">10:12 AM</span>
                </div>

                <div class="flex flex-col items-end gap-3 max-w-lg ml-auto">
                    <div
                        class="p-8 bg-indigo-600 rounded-[2.5rem] rounded-tr-none text-white text-base font-medium leading-loose shadow-2xl shadow-indigo-900/40">
                        Thanks Professor. Looking into it now. The lab sessions for the same have been scheduled for
                        next Tuesday.
                    </div>
                    <span class="text-[8px] font-black text-slate-600 uppercase tracking-widest italic font-black">Admin
                        / Current User • 10:25 AM</span>
                </div>

                <div class="flex flex-col items-start gap-3 max-w-lg">
                    <div class="flex items-center space-x-3 mb-1">
                        <span
                            class="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic font-black">Placement
                            Officer</span>
                    </div>
                    <div
                        class="p-8 bg-slate-800 border border-slate-700 rounded-[2.5rem] rounded-tl-none text-slate-300 text-base font-medium leading-loose shadow-xl">
                        Just got confirmation from Microsoft for the campus drive. We need the list of eligible
                        candidates by Friday EOD.
                    </div>
                    <span class="text-[8px] font-black text-slate-600 uppercase tracking-widest italic">11:05 AM</span>
                </div>
            </div>

            <!-- Input Box -->
            <div class="p-10 border-t border-slate-800 bg-slate-900/50 backdrop-blur-xl z-10">
                <div
                    class="bg-slate-800 px-10 py-6 rounded-[2.5rem] border border-slate-700 flex items-center space-x-6 group focus-within:border-indigo-500 transition-all shadow-2xl">
                    <button class="text-slate-500 hover:text-indigo-400 transition-all"><i
                            class="fas fa-paperclip text-lg"></i></button>
                    <input type="text" placeholder="Draft your message to #BCA-Dept..."
                        class="bg-transparent border-none focus:ring-0 flex-1 text-white font-medium text-base placeholder-slate-600 italic">
                    <button
                        class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center hover:bg-indigo-700 hover:-rotate-12 transition-all transform active:scale-95 shadow-xl shadow-indigo-900/40">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>