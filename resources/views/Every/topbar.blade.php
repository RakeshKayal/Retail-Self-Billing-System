<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>

<!-- TopNavBar Component -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 z-40 bg-[#0a0e14]/60 backdrop-blur-xl flex justify-between items-center px-8 border-b border-[#44484f]/15 shadow-2xl shadow-black/50">
    <div class="flex items-center gap-6 flex-1">
        <span class="text-xl font-bold text-[#b6a0ff] font-headline">Ether POS</span>
        <div class="relative w-96">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[20px]">search</span>
            <input class="w-full bg-[#0f141a] border-none rounded-lg py-2 pl-10 pr-4 text-sm text-on-surface focus:ring-1 focus:ring-primary/40 transition-all outline-none" placeholder="Search product name or SKU..." type="text"/>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <button class="p-2 text-slate-300 hover:text-[#b6a0ff] hover:bg-[#20262f]/40 rounded-full transition-colors duration-200">
            <span class="material-symbols-outlined">qr_code_scanner</span>
        </button>
        <button class="p-2 text-slate-300 hover:text-[#b6a0ff] hover:bg-[#20262f]/40 rounded-full transition-colors duration-200 relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-tertiary rounded-full"></span>
        </button>
        <button id="themeToggle" onclick="toggleTheme()" class="p-2 text-slate-300 hover:text-[#b6a0ff] hover:bg-[#20262f]/40 rounded-full transition-colors duration-200">
            <span class="material-symbols-outlined" id="themeIcon">light_mode</span>
        </button>
        @if(auth()->check())
            <div class="flex items-center gap-2 ml-4 pl-4 border-l border-[#44484f]/30">
                <span class="text-sm text-slate-400">{{ auth()->user()->name }}</span>
                
                @if (auth()->user()->role === 'staff')
                    <a href="{{ route('profile.password.edit') }}"  class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-sm font-medium hover:bg-amber-100 transition">
   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
       d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
   </svg>
   Change your password
                      
                    </a>
                    
                @endif
                <button onclick="document.getElementById('logoutForm').submit()" class="text-xs text-slate-500 hover:text-slate-400 transition cursor-pointer" title="Logout">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                </button>
                <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @endif
    </div>
</header>

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const isLight = html.classList.contains('light');
        
        if (isLight) {
            html.classList.remove('light');
            localStorage.setItem('theme', 'dark');
        } else {
            html.classList.add('light');
            localStorage.setItem('theme', 'light');
        }
        
        updateThemeIcon();
    }

    function updateThemeIcon() {
        const isLight = document.documentElement.classList.contains('light');
        const icon = document.getElementById('themeIcon');
        icon.textContent = isLight ? 'dark_mode' : 'light_mode';
    }

    // Initialize theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.classList.toggle('light', savedTheme === 'light');
        updateThemeIcon();
    });
</script>