<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Staff - Ether POS</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0a0e14; color: #f1f3fc; transition: all 0.3s ease; }
        html.light body { background-color: #f9fafb; color: #1f2937; }
    </style>
</head>
<body class="bg-[#0a0e14] min-h-screen">
    @include('Every.sidebar')
    <div class="ml-64">
        @include('Every.topbar')

        <main class="pt-16 p-8">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">Manage Staff</h1>
                <p class="text-gray-400">Only admins can add or remove staff accounts.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-white mb-4">Add Staff Member</h2>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-600/10 text-green-300 rounded">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-600/10 text-red-300 rounded">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-600/10 text-red-300 rounded">
                            <ul class="list-disc ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-sm text-gray-300">Name</label>
                            <input name="name" value="{{ old('name') }}" required class="w-full mt-1 px-3 py-2 rounded bg-[#0f141a] border border-gray-700 text-white">
                        </div>
                        <div>
                            <label class="text-sm text-gray-300">Email</label>
                            <div class="flex gap-3 items-end">
                                <input id="staff-email" type="email" name="email" value="{{ old('email') }}" required class="w-full mt-1 px-3 py-2 rounded bg-[#0f141a] border border-gray-700 text-white">
                                <button id="send-otp-btn" type="button" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">Send OTP</button>
                            </div>
                            <p id="otp-status" class="mt-2 text-sm text-cyan-300 hidden"></p>
                        </div>
                        <div id="otp-section" class="hidden">
                            <label class="text-sm text-gray-300">OTP</label>
                            <div class="flex gap-3 items-end">
                                <input id="staff-otp" type="text" maxlength="6" class="w-full mt-1 px-3 py-2 rounded bg-[#0f141a] border border-gray-700 text-white">
                                <button id="verify-otp-btn" type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Verify OTP</button>
                            </div>
                            <p id="otp-timer" class="mt-2 text-sm text-gray-400">OTP valid for 40 seconds.</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-300">Password</label>
                            <input id="staff-password" type="password" name="password" required disabled class="w-full mt-1 px-3 py-2 rounded bg-[#0f141a] border border-gray-700 text-white">
                        </div>
                        <div>
                            <label class="text-sm text-gray-300">Confirm Password</label>
                            <input id="staff-password-confirm" type="password" name="password_confirmation" required disabled class="w-full mt-1 px-3 py-2 rounded bg-[#0f141a] border border-gray-700 text-white">
                        </div>
                        <input type="hidden" name="otp_verified" id="otp_verified" value="0">
                        <div>
                            <button id="create-staff-btn" type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded disabled:opacity-60" disabled>Create Staff</button>
                        </div>
                    </form>
                    <script>
                        const sendOtpBtn = document.getElementById('send-otp-btn');
                        const verifyOtpBtn = document.getElementById('verify-otp-btn');
                        const staffEmail = document.getElementById('staff-email');
                        const staffOtp = document.getElementById('staff-otp');
                        const otpSection = document.getElementById('otp-section');
                        const otpStatus = document.getElementById('otp-status');
                        const otpTimer = document.getElementById('otp-timer');
                        const passwordInput = document.getElementById('staff-password');
                        const confirmInput = document.getElementById('staff-password-confirm');
                        const createStaffBtn = document.getElementById('create-staff-btn');
                        const otpVerifiedInput = document.getElementById('otp_verified');
                        let countdown;
                        let timerValue = 40;

                        function setCountdown(seconds) {
                            clearInterval(countdown);
                            timerValue = seconds;
                            otpTimer.textContent = `OTP valid for ${timerValue} seconds.`;
                            countdown = setInterval(() => {
                                timerValue -= 1;
                                if (timerValue <= 0) {
                                    clearInterval(countdown);
                                    otpTimer.textContent = 'OTP expired. Request a new OTP.';
                                    verifyOtpBtn.disabled = true;
                                    otpStatus.textContent = 'OTP expired. Please request a new OTP.';
                                    otpStatus.classList.remove('text-cyan-300');
                                    otpStatus.classList.add('text-red-400');
                                } else {
                                    otpTimer.textContent = `OTP valid for ${timerValue} seconds.`;
                                }
                            }, 1000);
                        }

                        sendOtpBtn?.addEventListener('click', async () => {
                            const email = staffEmail.value.trim();
                            if (!email) {
                                alert('Enter a valid staff email before sending OTP.');
                                return;
                            }
                            sendOtpBtn.disabled = true;
                            sendOtpBtn.textContent = 'Sending...';

                            const response = await fetch('{{ route('admin.staff.sendOtp') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                },
                                body: JSON.stringify({ email }),
                            });
                            const data = await response.json();
                            sendOtpBtn.disabled = false;
                            sendOtpBtn.textContent = 'Send OTP';

                            if (response.ok && data.success) {
                                otpSection.classList.remove('hidden');
                                otpStatus.classList.remove('hidden', 'text-red-400');
                                otpStatus.classList.add('text-cyan-300');
                                otpStatus.textContent = data.message;
                                verifyOtpBtn.disabled = false;
                                setCountdown(40);
                            } else {
                                otpStatus.classList.remove('hidden', 'text-cyan-300');
                                otpStatus.classList.add('text-red-400');
                                otpStatus.textContent = data.message || 'Failed to send OTP.';
                            }
                        });

                        verifyOtpBtn?.addEventListener('click', async () => {
                            const email = staffEmail.value.trim();
                            const otp = staffOtp.value.trim();
                            if (!email || !otp) {
                                alert('Enter both email and OTP before verification.');
                                return;
                            }
                            verifyOtpBtn.disabled = true;
                            verifyOtpBtn.textContent = 'Verifying...';

                            const response = await fetch('{{ route('admin.staff.verifyOtp') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                },
                                body: JSON.stringify({ email, otp }),
                            });
                            const data = await response.json();
                            verifyOtpBtn.disabled = false;
                            verifyOtpBtn.textContent = 'Verify OTP';

                            if (response.ok && data.success) {
                                otpStatus.classList.remove('text-red-400');
                                otpStatus.classList.add('text-cyan-300');
                                otpStatus.textContent = data.message;
                                passwordInput.disabled = false;
                                confirmInput.disabled = false;
                                createStaffBtn.disabled = false;
                                otpVerifiedInput.value = '1';
                            } else {
                                otpStatus.classList.remove('text-cyan-300');
                                otpStatus.classList.add('text-red-400');
                                otpStatus.textContent = data.message || 'OTP verification failed.';
                            }
                        });
                    </script>
                </div>

                <div class="bg-[#151a21] rounded-lg border border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-white mb-4">Existing Staff</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-gray-600 bg-[#0f141a]">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-300">Name</th>
                                    <th class="px-4 py-2 text-left text-gray-300">Email</th>
                                    <th class="px-4 py-2 text-left text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-600">
                                @forelse($staff as $s)
                                    <tr>
                                        <td class="px-4 py-3">{{ $s->name }}</td>
                                        <td class="px-4 py-3">{{ $s->email }}</td>
                                        <td class="px-4 py-3">
                                            <form action="{{ route('admin.staff.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Delete this staff member?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-gray-400">No staff members yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
