<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? __('Panel') }} · {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        @php($currentOrganization = app(\App\Domain\Organizations\Support\CurrentOrganization::class))
        <header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-5">
                    <x-brand href="{{ route('dashboard') }}" class="text-zinc-950 dark:text-white" />
                    @if ($currentOrganization->has())
                        <flux:button href="{{ route('organizations.select') }}" variant="ghost" size="sm">
                            {{ $currentOrganization->get()->name }}
                        </flux:button>
                    @endif
                </div>

                <nav class="flex items-center gap-2" aria-label="{{ __('Navegación principal') }}">
                    <flux:button href="{{ route('dashboard') }}" variant="ghost" size="sm">
                        {{ __('Dashboard') }}
                    </flux:button>
                    @if ($currentOrganization->has())
                        @can('viewAny', [\App\Domain\People\Models\Person::class, $currentOrganization->get()])
                            <flux:button href="{{ route('people.index') }}" variant="ghost" size="sm">
                                {{ __('Personas') }}
                            </flux:button>
                        @endcan
                    @endif
                    <flux:button href="{{ route('profile.edit') }}" variant="ghost" size="sm">
                        {{ __('Perfil') }}
                    </flux:button>
                    @if ($currentOrganization->has())
                        @can('viewMembers', $currentOrganization->get())
                            <flux:button href="{{ route('organization.members') }}" variant="ghost" size="sm">
                                {{ __('Equipo') }}
                            </flux:button>
                        @endcan
                        @can('update', $currentOrganization->get())
                            <flux:button href="{{ route('organization.settings') }}" variant="ghost" size="sm">
                                {{ __('Organización') }}
                            </flux:button>
                        @endcan
                        @can('viewSubscription', $currentOrganization->get())
                            <flux:button href="{{ route('organization.subscription') }}" variant="ghost" size="sm">
                                {{ __('Membresía') }}
                            </flux:button>
                        @endcan
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button type="submit" variant="ghost" size="sm">
                            {{ __('Salir') }}
                        </flux:button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        @fluxScripts
    </body>
</html>
