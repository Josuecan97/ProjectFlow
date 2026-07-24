<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Acceso') }} · {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <main class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(28rem,0.8fr)]">
            <section class="hidden bg-indigo-700 p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <x-brand href="/" class="text-white" />

                <div class="max-w-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-200">
                        Proyectos de servicios
                    </p>
                    <h1 class="mt-5 text-5xl font-semibold tracking-tight">
                        Toda la operación de cada proyecto, en un solo lugar.
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-indigo-100">
                        Mantén sincronizados al equipo y al cliente desde la primera cotización hasta la entrega.
                    </p>
                </div>

                <p class="text-sm text-indigo-200">ProjectFlow · {{ now()->year }}</p>
            </section>

            <section class="flex items-center justify-center px-6 py-12 sm:px-10">
                <div class="w-full max-w-md">
                    <x-brand href="/" class="mb-10 text-zinc-950 dark:text-white lg:hidden" />
                    {{ $slot }}
                </div>
            </section>
        </main>

        @fluxScripts
    </body>
</html>
