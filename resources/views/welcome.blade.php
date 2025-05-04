<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GacBank</title>

    <!-- Tailwind CSS (opcional, para um visual bonito) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-xl font-bold text-gray-800"> 
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <img src="{{ asset('/foto-gacbank.png') }}" alt="Logo" class="w-[60px] h-[60px]"/>
            </a>
            </div>
            <div>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 mr-4">Login</a>
                <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900">Registro</a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <div class="max-w-7xl mx-auto mt-10 px-4">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Grupo Adriano Cobuccio </h1>
            <p class="text-gray-700 whitespace-pre-line">
                {{ \Illuminate\Support\Facades\File::get(storage_path('app/public/sobreaempresa.txt')) }}
            </p>
            </div>
    </div>
</body>
</html>
