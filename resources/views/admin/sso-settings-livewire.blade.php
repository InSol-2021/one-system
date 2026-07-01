@extends('admin.layouts.app')

@section('title', 'SSO Settings')

@section('content')
<div class="os-container py-10">
    @livewire('admin.sso-settings-component')
</div>
@endsection
