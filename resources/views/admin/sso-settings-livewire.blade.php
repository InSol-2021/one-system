@extends('admin.layouts.app')

@section('title', 'SSO Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    @livewire('admin.sso-settings-component')
</div>
@endsection