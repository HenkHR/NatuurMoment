@extends('layouts.lobby')

@section('title', 'Game lobby')

@section('content')
    <livewire:player-lobby 
        :gameId="$gameId" 
        :playerToken="$playerToken" 
    />
@endsection
