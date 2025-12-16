@extends('layouts.cta-page')

@section('title', config('cta.page_title', 'Zelf aan de slag!'))

@section('content')
    <x-nav.lobby
        :title="config('cta.page_title', 'Zelf aan de slag!')"
        :subtitle="config('cta.page_subtitle', 'TIps voor thuis en onderweg!')"
    />

    <main class="flex-1">
        <livewire:player-cta :gameId="$gameId" :playerToken="$playerToken" />
    </main>
@endsection
