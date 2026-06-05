<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ether POS - Secure Login</title>
    <script>
        // Initialize theme early to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.classList.toggle('light', savedTheme === 'light');
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "background": "#0a0e14",
                        "surface-tint": "#b6a0ff",
                        "surface-container-high": "#1b2028",
                        "on-error": "#490013",
                        "on-background": "#f1f3fc",
                        "tertiary-dim": "#ff59e3",
                        "surface-dim": "#0a0e14",
                        "inverse-surface": "#f8f9ff",
                        "outline": "#72757d",
                        "on-tertiary-container": "#1b0017",
                        "primary-fixed-dim": "#9c7eff",
                        "surface-container-highest": "#20262f",
                        "secondary-fixed": "#26e6ff",
                        "surface-container-low": "#0f141a",
                        "surface-container-lowest": "#000000",
                        "on-secondary-fixed": "#003a42",
                        "surface-variant": "#20262f",
                        "inverse-primary": "#6834eb",
                        "on-secondary": "#004d57",
                        "on-error-container": "#ffb2b9",
                        "primary": "#b6a0ff",
                        "primary-container": "#a98fff",
                        "on-primary": "#340090",
                        "surface": "#0a0e14",
                        "secondary-fixed-dim": "#00d7f0",
                        "secondary-container": "#006875",
                        "on-surface-variant": "#a8abb3",
                        "on-primary-fixed": "#000000",
                        "on-primary-container": "#280072",
                        "on-tertiary-fixed": "#33002d",
                        "on-tertiary": "#42003a",
                        "surface-bright": "#262c36",
                        "tertiary-container": "#ff05e5",
                        "tertiary": "#ff59e3",
                        "error-container": "#a70138",
                        "error": "#ff6e84",
                        "primary-dim": "#7e51ff",
                        "tertiary-fixed-dim": "#ff68e3",
                        "primary-fixed": "#a98fff",
                        "on-surface": "#f1f3fc",
                        "on-tertiary-fixed-variant": "#6d0061",
                        "secondary-dim": "#00d4ec",
                        "outline-variant": "#44484f",
                        "error-dim": "#d73357",
                        "surface-container": "#151a21",
                        "on-secondary-fixed-variant": "#005964",
                        "tertiary-fixed": "#ff85e4",
                        "on-secondary-container": "#e8fbff",
                        "on-primary-fixed-variant": "#32008a",
                        "inverse-on-surface": "#51555c",
                        "secondary": "#00e3fd"
                    },
                    fontFamily: {
                        "headline": ["Space Grotesk"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body {
            background-color: #0a0e14;
            color: #f1f3fc;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }
        html.light body {
            background-color: #f9fafb;
            color: #1f2937;
        }
        
        .glass-card {
            background: rgba(32, 38, 47, 0.6);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            transition: all 0.3s ease;
        }
        html.light .glass-card {
            background: rgba(255, 255, 255, 0.95);
        }
        
        .neon-glow {
            box-shadow: 0 0 40px -10px rgba(182, 160, 255, 0.3);
        }
        html.light .neon-glow {
            box-shadow: 0 0 20px -10px rgba(182, 160, 255, 0.15);
        }
        
        .ghost-border {
            border: 1px solid rgba(68, 72, 79, 0.15);
            transition: border-color 0.3s ease;
        }
        html.light .ghost-border {
            border-color: rgba(0, 0, 0, 0.1);
        }
        
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        input[type="email"], input[type="password"] {
            transition: all 0.3s ease;
        }
        html.light input[type="email"], html.light input[type="password"] {
            background-color: rgba(0, 0, 0, 0.05) !important;
            color: #1f2937 !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
        }
        html.light input[type="email"]::placeholder, html.light input[type="password"]::placeholder {
            color: #9ca3af !important;
        }
        
        .error-glow {
            box-shadow: 0 0 12px -2px rgba(255, 110, 132, 0.3);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen items-center justify-center relative overflow-hidden">
    <!-- Ambient Neon Glows -->
    <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/20 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-secondary/10 blur-[120px] rounded-full pointer-events-none"></div>

    <main class="w-full max-w-[1200px] grid grid-cols-1 lg:grid-cols-2 gap-12 items-center px-6 relative z-10">
        <!-- Left Column: Brand Identity -->
        <div class="hidden lg:flex flex-col space-y-8">
            <div class="space-y-4">
                <span class="text-secondary font-headline tracking-[0.2em] text-sm font-bold">NEXT GEN RETAIL</span>
                <h1 class="text-6xl font-headline font-bold leading-tight tracking-tighter">
                    Ether <span class="text-transparent bg-clip-text bg-gradient-to-br from-violet-300 to-cyan-400">POS</span>
                </h1>
                <p class="text-on-surface-variant text-lg max-w-md font-body leading-relaxed">
                    Seamlessly manage transactions, inventory, and staff through a single, luminous dashboard designed for the future of commerce.
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-surface-container-highest flex items-center justify-center ghost-border">
                    <span class="material-symbols-outlined text-secondary">bolt</span>
                </div>
                <div>
                    <h3 class="font-headline font-bold text-on-surface">Zero Latency</h3>
                    <p class="text-sm text-on-surface-variant">Instant transaction synchronization</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-surface-container-highest flex items-center justify-center ghost-border">
                    <span class="material-symbols-outlined text-primary">security</span>
                </div>
                <div>
                    <h3 class="font-headline font-bold text-on-surface">Encrypted Core</h3>
                    <p class="text-sm text-on-surface-variant">Bank-grade data protection</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Login Card -->
        <div class="flex flex-col items-center justify-center">
            <div class="glass-card w-full max-w-md p-10 rounded-[2rem] ghost-border neon-glow relative">
                <!-- Logo for Mobile -->
                <div class="lg:hidden mb-8 text-center">
                    <h2 class="text-3xl font-headline font-bold tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-violet-300 to-cyan-400">
                        Ether POS
                    </h2>
                </div>

                <!-- Card Header -->
                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-headline font-bold text-on-surface mb-2">Welcome Back</h2>
                    <p class="text-on-surface-variant text-sm font-body">Enter your credentials to access the terminal</p>
                </div>

                <div class="flex items-center justify-between mb-6 gap-4">
                    <div class="inline-flex rounded-full bg-surface-container-lowest p-1">
                        <button type="button" id="loginTabBtn" onclick="showAuthPanel('login')" class="px-5 py-2 rounded-full text-sm font-headline font-bold text-secondary bg-surface-container-highest transition-all duration-300">Login</button>
                        <button type="button" id="signupTabBtn" onclick="showAuthPanel('signup')" class="px-5 py-2 rounded-full text-sm font-headline font-medium text-on-surface-variant hover:text-on-surface transition-all duration-300">Sign Up</button>
                    </div>
                    <span class="text-xs uppercase tracking-[0.3em] text-on-surface-variant">Staff can log in here</span>
                </div>

                <!-- Login Panel -->
                <div id="loginPanel" class="space-y-6">
                    <div id="userTypeToggle" class="bg-surface-container-lowest p-1 rounded-xl flex ghost-border">
                        <button type="button" onclick="selectUserType('admin')" class="flex-1 py-2 px-4 rounded-lg bg-surface-container-highest text-secondary text-sm font-headline font-bold transition-all duration-300 user-type-btn active" data-type="admin">
                            Admin/Staff
                        </button>
                        <button type="button" onclick="selectUserType('customer')" class="flex-1 py-2 px-4 rounded-lg text-on-surface-variant text-sm font-headline font-medium hover:text-on-surface transition-all duration-300 user-type-btn" data-type="customer">
                            Customer
                        </button>
                    </div>

                    <div class="mb-8 p-4 rounded-xl bg-primary/10 border border-primary/30">
                        <p class="text-xs font-headline font-bold text-primary mb-3 uppercase tracking-widest">Demo Credentials</p>
                        <div id="adminCredentials" class="space-y-2">
                            <div class="flex items-start gap-2">
                                <span class="text-primary mt-1">👤</span>
                                <div class="text-sm font-body">
                                    <p class="text-on-surface-variant text-xs">Admin / Staff Email</p>
                                    <p class="text-on-surface font-mono font-bold">admin@example.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-primary mt-1">🔑</span>
                                <div class="text-sm font-body">
                                    <p class="text-on-surface-variant text-xs">Password</p>
                                    <p class="text-on-surface font-mono font-bold">password</p>
                                </div>
                            </div>
                        </div>

                        <div id="customerCredentials" class="space-y-2" style="display: none;">
                            <div class="flex items-start gap-2">
                                <span class="text-primary mt-1">👤</span>
                                <div class="text-sm font-body">
                                    <p class="text-on-surface-variant text-xs">Customer Email</p>
                                    <p class="text-on-surface font-mono font-bold">customer@example.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-primary mt-1">🔑</span>
                                <div class="text-sm font-body">
                                    <p class="text-on-surface-variant text-xs">Password</p>
                                    <p class="text-on-surface font-mono font-bold">password</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any() && ($activeTab ?? 'login') === 'login')
                        <div class="mb-6 p-4 rounded-xl bg-error-dim/20 border border-error/30 space-y-2">
                            @foreach ($errors->all() as $error)
                                <p class="text-error text-sm font-body">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" id="userType" name="user_type" value="admin">

                        <div class="space-y-2">
                            <label class="text-xs font-headline font-bold tracking-widest text-on-surface-variant uppercase ml-1">Email</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">mail</span>
                                <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-surface-container-low border-transparent focus:border-secondary/40 focus:ring-0 text-on-surface pl-12 pr-4 py-4 rounded-xl ghost-border transition-all duration-300 placeholder:text-outline/50 @error('email') error-glow @enderror" placeholder="name@etherpos.com" required>
                            </div>
                            @error('email')
                                <p class="text-error text-xs ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between items-center px-1">
                                <label class="text-xs font-headline font-bold tracking-widest text-on-surface-variant uppercase">Password</label>
                                <a class="text-xs font-headline font-bold text-primary hover:text-primary-fixed transition-colors" href="#">Forgot Password?</a>
                            </div>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-secondary transition-colors">lock</span>
                                <input type="password" name="password" class="w-full bg-surface-container-low border-transparent focus:border-secondary/40 focus:ring-0 text-on-surface pl-12 pr-12 py-4 rounded-xl ghost-border transition-all duration-300 placeholder:text-outline/50 @error('password') error-glow @enderror" placeholder="••••••••" id="passwordInput" required>
                                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface transition-colors" onclick="togglePassword()">
                                    <span class="material-symbols-outlined text-xl" id="visibilityIcon">visibility</span>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-error text-xs ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full py-4 rounded-xl font-headline font-bold text-on-primary bg-gradient-to-r from-secondary to-primary-dim shadow-lg shadow-secondary/20 hover:shadow-secondary/40 hover:scale-[1.01] active:scale-[0.98] transition-all duration-300">
                            Log In to Terminal
                        </button>
                    </form>

                    <div class="mt-10 text-center">
                        <p class="text-on-surface-variant text-sm font-body">
                            New to Ether POS? 
                            <button type="button" class="text-secondary font-bold hover:underline ml-1" onclick="showAuthPanel('signup')">Create an account</button>
                        </p>
                    </div>
                </div>

                <!-- Signup Panel -->
                <div id="signupPanel" class="hidden space-y-6">
                    <div class="mb-8 p-4 rounded-xl bg-primary/10 border border-primary/30">
                        <p class="text-xs font-headline font-bold text-primary mb-3 uppercase tracking-widest">Create a customer account</p>
                        <p class="text-sm text-on-surface-variant">Customers can self-register and then access products, scan barcodes, and pay bills.</p>
                    </div>

                    @if ($errors->any() && ($activeTab ?? 'login') === 'signup')
                        <div class="mb-6 p-4 rounded-xl bg-error-dim/20 border border-error/30 space-y-2">
                            @foreach ($errors->all() as $error)
                                <p class="text-error text-sm font-body">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.post') }}" class="space-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-xs font-headline font-bold tracking-widest text-on-surface-variant uppercase ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-surface-container-low border-transparent focus:border-secondary/40 focus:ring-0 text-on-surface px-4 py-4 rounded-xl ghost-border transition-all duration-300 placeholder:text-outline/50 @error('name') error-glow @enderror" placeholder="Your full name" required>
                            @error('name')
                                <p class="text-error text-xs ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-headline font-bold tracking-widest text-on-surface-variant uppercase ml-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-surface-container-low border-transparent focus:border-secondary/40 focus:ring-0 text-on-surface px-4 py-4 rounded-xl ghost-border transition-all duration-300 placeholder:text-outline/50 @error('email') error-glow @enderror" placeholder="name@etherpos.com" required>
                            @error('email')
                                <p class="text-error text-xs ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-headline font-bold tracking-widest text-on-surface-variant uppercase ml-1">Password</label>
                            <input type="password" name="password" class="w-full bg-surface-container-low border-transparent focus:border-secondary/40 focus:ring-0 text-on-surface px-4 py-4 rounded-xl ghost-border transition-all duration-300 placeholder:text-outline/50 @error('password') error-glow @enderror" placeholder="Create a password" required>
                            @error('password')
                                <p class="text-error text-xs ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-headline font-bold tracking-widest text-on-surface-variant uppercase ml-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full bg-surface-container-low border-transparent focus:border-secondary/40 focus:ring-0 text-on-surface px-4 py-4 rounded-xl ghost-border transition-all duration-300 placeholder:text-outline/50" placeholder="Repeat your password" required>
                        </div>

                        <button type="submit" class="w-full py-4 rounded-xl font-headline font-bold text-on-primary bg-gradient-to-r from-secondary to-primary-dim shadow-lg shadow-secondary/20 hover:shadow-secondary/40 hover:scale-[1.01] active:scale-[0.98] transition-all duration-300">
                            Sign Up as Customer
                        </button>
                    </form>

                    <div class="mt-10 text-center">
                        <p class="text-on-surface-variant text-sm font-body">
                            Already have an account? 
                            <button type="button" class="text-secondary font-bold hover:underline ml-1" onclick="showAuthPanel('login')">Sign in</button>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Terminal Info -->
            <div class="mt-8 flex items-center space-x-6 text-on-surface-variant/40">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-secondary shadow-[0_0_8px_rgba(0,227,253,0.8)]"></span>
                    <span class="text-[10px] font-headline tracking-tighter">NODE: TERMINAL-01</span>
                </div>
                <div class="text-[10px] font-headline tracking-tighter">v4.2.0-STABLE</div>
            </div>
        </div>
    </main>

    <!-- Decorative Background Elements -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=%22100%22 height=%22100%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cdefs%3E%3ClinearGradient id=%22grid%22 x1=%220%25%22 y1=%220%25%22 x2=%22100%25%22 y2=%22100%25%22%3E%3Cstop offset=%220%25%22 style=%22stop-color:rgb(182,160,255);stop-opacity:0.1%22 /%3E%3Cstop offset=%22100%25%22 style=%22stop-color:rgb(0,227,253);stop-opacity:0.1%22 /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width=%22100%22 height=%22100%22 fill=%22url(%23grid)%22/%3E%3Cpath d=%22M 0 0 L 100 100 M 100 0 L 0 100%22 stroke=%22rgb(182,160,255)%22 stroke-width=%220.5%22 opacity=%220.1%22/%3E%3C/svg%3E');"></div>

    <script>
        const activeAuthTab = "{{ $activeTab ?? 'login' }}";

        function showAuthPanel(panel) {
            const loginPanel = document.getElementById('loginPanel');
            const signupPanel = document.getElementById('signupPanel');
            const loginTabBtn = document.getElementById('loginTabBtn');
            const signupTabBtn = document.getElementById('signupTabBtn');

            if (panel === 'signup') {
                loginPanel.classList.add('hidden');
                signupPanel.classList.remove('hidden');
                loginTabBtn.classList.remove('bg-surface-container-highest', 'text-secondary', 'font-bold');
                loginTabBtn.classList.add('text-on-surface-variant', 'font-medium');
                signupTabBtn.classList.add('bg-surface-container-highest', 'text-secondary', 'font-bold');
                signupTabBtn.classList.remove('text-on-surface-variant', 'font-medium');
            } else {
                loginPanel.classList.remove('hidden');
                signupPanel.classList.add('hidden');
                signupTabBtn.classList.remove('bg-surface-container-highest', 'text-secondary', 'font-bold');
                signupTabBtn.classList.add('text-on-surface-variant', 'font-medium');
                loginTabBtn.classList.add('bg-surface-container-highest', 'text-secondary', 'font-bold');
                loginTabBtn.classList.remove('text-on-surface-variant', 'font-medium');
            }
        }

        function selectUserType(type) {
            document.getElementById('userType').value = type;

            document.querySelectorAll('.user-type-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-surface-container-highest', 'text-secondary', 'font-bold');
                btn.classList.add('text-on-surface-variant', 'font-medium');
            });

            const activeBtn = document.querySelector(`[data-type="${type}"]`);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-surface-container-highest', 'text-secondary', 'font-bold');
                activeBtn.classList.remove('text-on-surface-variant', 'font-medium');
            }

            const adminCreds = document.getElementById('adminCredentials');
            const customerCreds = document.getElementById('customerCredentials');

            if (type === 'admin') {
                adminCreds.style.display = 'block';
                customerCreds.style.display = 'none';
            } else {
                adminCreds.style.display = 'none';
                customerCreds.style.display = 'block';
            }
        }

        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('visibilityIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            showAuthPanel(activeAuthTab === 'signup' ? 'signup' : 'login');
            selectUserType('admin');
        });
    </script>
</body>
</html>