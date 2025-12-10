@extends('layouts.lobby')

@section('title', 'Spel aanmaken')

@section('content')
    <x-nav.join-nav />

    <main class="flex-1 flex justify-center px-4 pb-10">
        <livewire:create-game :locationId="$locationId" />
    </main>
@endsection
