<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>

<!-- SideNavBar Component -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-[#0f141a] flex flex-col py-6 z-50">
    <div class="px-6 mb-10">
        <h1 class="text-2xl font-bold bg-gradient-to-br from-[#b6a0ff] to-[#7e51ff] bg-clip-text text-transparent font-headline tracking-tight">Kinetic Ether</h1>
        <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-label">Admin Terminal</p>
    </div>
    <nav class="flex-1 px-3 space-y-2">
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 font-medium hover:bg-[#20262f] hover:text-[#b6a0ff] transition-all duration-300 {{ request()->routeIs('home') ? 'text-[#b6a0ff] border-l-4 border-[#00e3fd] bg-[#20262f] scale-105' : '' }}">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('showProduct') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 font-medium hover:bg-[#20262f] hover:text-[#b6a0ff] transition-all duration-300 {{ request()->routeIs('showProduct') ? 'text-[#b6a0ff] border-l-4 border-[#00e3fd] bg-[#20262f] scale-105' : '' }}">
            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
            <span>Products</span>
        </a>
        <a href="{{ route('bill') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 font-medium hover:bg-[#20262f] hover:text-[#b6a0ff] transition-all duration-300 {{ request()->routeIs('bill') ? 'text-[#b6a0ff] border-l-4 border-[#00e3fd] bg-[#20262f] scale-105' : '' }}">
            <span class="material-symbols-outlined text-[20px]">point_of_sale</span>
            <span>Billing</span>
        </a>
        <a href="{{ route('addProduct') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 font-medium hover:bg-[#20262f] hover:text-[#b6a0ff] transition-all duration-300 {{ request()->routeIs('addProduct') ? 'text-[#b6a0ff] border-l-4 border-[#00e3fd] bg-[#20262f] scale-105' : '' }}">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            <span>Add Product</span>
        </a>
        <a href="{{ route('addcat') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 font-medium hover:bg-[#20262f] hover:text-[#b6a0ff] transition-all duration-300 {{ request()->routeIs('addcat') ? 'text-[#b6a0ff] border-l-4 border-[#00e3fd] bg-[#20262f] scale-105' : '' }}">
            <span class="material-symbols-outlined text-[20px]">category</span>
            <span>Categories</span>
        </a>
        <a href="{{ route('manageProduct') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 font-medium hover:bg-[#20262f] hover:text-[#b6a0ff] transition-all duration-300 {{ request()->routeIs('manageProduct') ? 'text-[#b6a0ff] border-l-4 border-[#00e3fd] bg-[#20262f] scale-105' : '' }}">
            <span class="material-symbols-outlined text-[20px]">edit</span>
            <span>Manage Products</span>
        </a>
    </nav>
    <div class="px-6 mt-auto pt-6 border-t border-outline-variant/10 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full border border-primary/20 bg-gradient-to-br from-[#b6a0ff] to-[#7e51ff] flex items-center justify-center text-white font-headline font-bold text-sm">
            {{ (auth()->user()->name ?? 'A')[0] }}
        </div>
        <div>
            <p class="text-sm font-medium text-on-surface">{{ auth()->user()->name ?? 'Admin' }}</p>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs text-slate-500 hover:text-slate-400 transition">Logout</button>
            </form>
        </div>
    </div>
</aside>
