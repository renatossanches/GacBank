<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GacBank</title>

    <!-- Tailwind CSS, para um visual elegante -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js para interatividade -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="text-xl font-bold text-gray-800"> 
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <img src="{{ asset('/foto-gacbank.png') }}" alt="Logo" class="w-[60px] h-[60px]"/>
            </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
            <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                    <div class="ms-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('login')">
                    {{ __('Login') }}
                </x-dropdown-link>
                <x-dropdown-link :href="route('register')">
                    {{ __('Registro') }}
                </x-dropdown-link>
            </x-slot>
            </x-dropdown>


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
