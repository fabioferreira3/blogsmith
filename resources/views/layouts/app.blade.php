<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>2Text AI</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:weight@400;600;700&display=swap">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Scripts -->
        @wireUiScripts
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <style>
            [x-cloak] { display: none !important; }
        </style>

    </head>
    <body class="font-sans antialiased">
        <x-notifications />
        <x-jet-banner />
        @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

        <div class="flex min-h-screen bg-gray-100 relative">
            <div class="hidden md:block md:w-1/5 h-full fixed p-6 bg-white border-r-2">
                @include('livewire.common.sidebar')
            </div>
            <!-- Page Content -->
            <main class="mx-auto w-full md:w-4/5 absolute right-0 min-h-screen bg-white px-6">
                @livewire('navigation-menu')
                <div class="p-6 rounded-lg bg-zinc-100">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
