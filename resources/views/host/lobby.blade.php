@extends('layouts.lobby')

@section('title', 'Host lobby')

@section('content')
    <livewire:host-lobby :gameId="$gameId" />
@endsection
