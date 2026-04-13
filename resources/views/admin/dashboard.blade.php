@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-3 gap-4">

    <div class="bg-white p-5 rounded shadow">
        <div class="text-gray-500 text-sm">Announcements</div>
        <div class="text-2xl font-bold">--</div>
    </div>

    <div class="bg-white p-5 rounded shadow">
        <div class="text-gray-500 text-sm">Tasks</div>
        <div class="text-2xl font-bold">--</div>
    </div>

    <div class="bg-white p-5 rounded shadow">
        <div class="text-gray-500 text-sm">Users</div>
        <div class="text-2xl font-bold">--</div>
    </div>

</div>

@endsection