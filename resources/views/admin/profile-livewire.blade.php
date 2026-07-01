@extends('admin.layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="os-container py-8">
    @livewire('admin.profile-component')
</div>
@endsection