@extends('layouts.lobby')

@section('title', 'Meedoen met een spel')

@section('content')
    <x-nav.join-nav />

    <main class="flex-1 flex justify-center px-4 pb-10">
        <livewire:join-game />
    </main>
@endsection
