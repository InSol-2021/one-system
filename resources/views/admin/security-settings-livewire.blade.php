@extends('admin.layouts.app')

@section('title', 'Security Settings')

@section('content')
<div class="os-container py-8">
    @livewire('admin.security-settings-component')
</div>
@endsection