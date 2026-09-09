<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Privilege Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#64748B'
                    }
                }
            }
        }
    </script>

    @stack('scripts')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <x-settingsm.header />
    
    <div>
        <!-- Stats Cards -->
        <x-settingsm.kpi />
    </div>
        
    <main>
        @yield('content')
    </main>

        <script>
        window.rolePermissions = @json($rolePermissions ?? []);
        </script>
   
    
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
                timer: 3000, // auto close dalam 3 detik
                timerProgressBar: true
            });
        </script>
        @endif
</body>
</html>