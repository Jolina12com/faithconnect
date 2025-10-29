@extends('admin.dashboard')

@section('content')
<div class="container">
    <h1>{{ $sermon->title }}</h1>
    <p>{{ $sermon->description }}</p>

    @if ($sermon->video_path)
        <video width="100%" height="auto" controls>
            <source src="{{ asset('storage/' . $sermon->video_path) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @else
        <p>No video available.</p>
    @endif

    <a href="{{ route('admin.sermons.index') }}" class="btn btn-secondary mt-3">Back to Sermons</a>
</div>
@endsection
