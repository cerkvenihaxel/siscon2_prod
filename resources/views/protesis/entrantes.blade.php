@extends('crudbooster::admin_template')


<head>
    <title>Entrantes - PROTESIS</title>
    <meta charset="utf-8">
    @livewireStyles
</head>

@section('content')
    <div>
        <livewire:entrantes/>
    </div>

@livewireScripts

@endsection
