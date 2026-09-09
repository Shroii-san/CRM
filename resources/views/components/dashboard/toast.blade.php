<div id="toastContainer" class="fixed top-20 right-6 z-50 flex flex-col gap-3"></div>

<script>
    function showToast(message) {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');

        // Warna hijau solid untuk semua toast
        const bgClass = 'bg-green-600';
        const icon = `<svg class="w-6 h-6 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"></path>
                      </svg>`;

        toast.className = `toast-enter flex items-start gap-3 min-w-[320px] max-w-[400px] 
            p-4 rounded-xl shadow-2xl ${bgClass} text-white`;

        toast.innerHTML = `
            ${icon}
            <div class="flex-1">
                <p class="font-semibold text-sm">Notifikasi</p>
                <p class="text-sm text-white/90 mt-0.5">${message}</p>
                <div class="mt-2 h-1 bg-white/20 rounded-full overflow-hidden">
                    <div class="progress-bar h-full bg-white/60 rounded-full"></div>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        toastContainer.appendChild(toast);

        // auto close
        setTimeout(() => {
            toast.classList.remove('toast-enter');
            toast.classList.add('toast-exit');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ✅ Login sukses
    @if (session('login_success'))
        document.addEventListener('DOMContentLoaded', () => {
            showToast("{{ session('login_success') }}");
        });
    @endif

    // ✅ Logout sukses
    @if (session('success'))
        document.addEventListener('DOMContentLoaded', () => {
            showToast("{{ session('success') }}");
        });
    @endif

    // ❌ Error login
    @if ($errors->has('loginError'))
        document.addEventListener('DOMContentLoaded', () => {
            showToast("{{ $errors->first('loginError') }}");
        });
    @endif
</script>

<style>
    @keyframes slideInRight {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
    .toast-enter { animation: slideInRight 0.4s ease-out forwards; }
    .toast-exit { animation: slideOutRight 0.3s ease-in forwards; }
    .progress-bar { animation: progress 4s linear forwards; }
</style>