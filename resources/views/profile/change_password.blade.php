<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>body{background:#0a0e14;color:#f1f3fc}</style>
</head>
<body>
    @include('Every.sidebar')
    <div class="ml-64">
        @include('Every.topbar')
        <main class="pt-16 p-8">
            <div class="max-w-md">
                <h1 class="text-2xl font-bold mb-4">Change Password</h1>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-600/10 text-green-300 rounded">{{ session('success') }}</div>
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

                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-300">New Password</label>
                        <input type="password" name="password" required class="w-full mt-1 px-3 py-2 rounded bg-[#0f141a] border border-gray-700 text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-300">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full mt-1 px-3 py-2 rounded bg-[#0f141a] border border-gray-700 text-white">
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Update Password</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
