@extends('admin.layouts.app')

@section('title', 'Security Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    @livewire('admin.security-settings-component')
</div>
@endsection