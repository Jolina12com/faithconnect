@extends('member.dashboard_member')
@section('content')

    <div id="viewer-app"></div>
    <input type="hidden" id="viewer-id" value="{{ auth()->user()->id ?? 'guest' }}">

@endsection
