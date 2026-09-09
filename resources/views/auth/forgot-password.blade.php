<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Forgot Password</h2>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-sm text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block text-gray-700 mb-2 text-sm font-medium">Email</label>
            <input type="email" id="email" name="email" required autofocus
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full bg-indigo-500 text-white py-2 rounded-lg font-semibold hover:bg-indigo-600 transition">
            Send Reset Link
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="text-indigo-500 text-sm hover:underline">← Back to login</a>
    </div>
</div>

</body>
</html>
