@extends('member.dashboard_member')
@section('content')
<div class="container">
    <title>Livestream Viewer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Add stream ID input -->
    <input type="hidden" id="stream-id" value="{{ request()->query('stream_id') }}">
    <input type="hidden" id="room-name" value="{{ request()->query('room_name') }}">
    <input type="hidden" id="viewer-id" value="{{ auth()->id() }}">

    <!-- React mounting point -->
    <div id="viewer-app"></div>

    <!-- Load React and the compiled app.jsx -->
    @vite('resources/js/app.jsx')
</div>
@endsection
