@extends('admin.layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    @livewire('admin.profile-component')
</div>
@endsection