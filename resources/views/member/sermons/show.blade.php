@extends('member.dashboard_member')

@section('title', $sermon->title)

@section('styles')
<style>
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        border-radius: 8px;
    }
    
    .video-container video,
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 8px;
    }
    
    .sermon-meta {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }
    
    .sermon-meta p {
        margin-bottom: 8px;
    }
    
    .sermon-description {
        line-height: 1.7;
    }
    
    .topic-badge {
        background-color: #e9ecef;
        color: #495057;
        padding: 5px 10px;
        border-radius: 15px;
        margin-right: 5px;
        margin-bottom: 5px;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
 

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="video-container">
                        @if($sermon->video_path)
                            <video id="sermon-video" controls preload="metadata">
                                <source src="{{ asset('storage/' . $sermon->video_path) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <div class="d-flex justify-content-center align-items-center bg-secondary text-white" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                <div class="text-center">
                                    <i class="fas fa-video-slash fa-4x mb-3"></i>
                                    <h5>No video available</h5>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="card-title mb-0">{{ $sermon->title }}</h1>
                        <button id="favoriteBtn" class="btn {{ $sermon_favorites ? 'btn-danger' : 'btn-outline-danger' }}" data-id="{{ $sermon->id }}">
                            <i class="fas fa-heart"></i> {{ $sermon_favorites ? 'Favorited' : 'Favorite' }}
                        </button>
                        
                    
                    <div class="sermon-actions mb-4">
                        <div class="btn-group">
                            @if($sermon->audio_path)
                                <a href="{{ route('sermons.download', ['id' => $sermon->id, 'type' => 'audio']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i> Download Audio
                                </a>
                            @endif
                            
                            @if($sermon->video_path)
                                <a href="{{ route('sermons.download', ['id' => $sermon->id, 'type' => 'video']) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download me-1"></i> Download Video
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="sermon-meta mb-4">
                        <div class="row">
                            @if($sermon->speaker_name)
                                <div class="col-md-6 mb-3">
                                    <p><strong><i class="fas fa-user-tie me-2"></i> Speaker:</strong> {{ $sermon->speaker_name }}</p>
                                </div>
                            @endif
                            
                            @if($sermon->date_preached)
                                <div class="col-md-6 mb-3">
                                    <p><strong><i class="fas fa-calendar-alt me-2"></i> Date Preached:</strong> {{ date('F j, Y', strtotime($sermon->date_preached)) }}</p>
                                </div>
                            @endif
                            
                            @if($sermon->scripture_reference)
                                <div class="col-md-6 mb-3">
                                    <p><strong><i class="fas fa-book-open me-2"></i> Scripture:</strong> {{ $sermon->scripture_reference }}</p>
                                </div>
                            @endif
                            
                          
                            
                            <div class="col-md-6 mb-3">
                                <p><strong><i class="fas fa-eye me-2"></i> Views:</strong> {{ number_format($sermon->view_count) }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <p><strong><i class="fas fa-download me-2"></i> Downloads:</strong> {{ number_format($sermon->download_count) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($topics->count() > 0)
                        <div class="sermon-topics mb-4">
                            <h5>Topics:</h5>
                            <div>
                                @foreach($topics as $topic)
                                    <span class="topic-badge">{{ $topic->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($sermon->description)
                        <div class="sermon-description">
                            <h5>Description:</h5>
                            <div class="mt-2">
                                {!! nl2br(e($sermon->description)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            @if($series)
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">About This Series</h5>
                    </div>
                    <div class="card-body">
                        @if($series->image_path)
                            <img src="{{ asset('storage/' . $series->image_path) }}" alt="{{ $series->title }}" class="img-fluid mb-3 rounded">
                        @endif
                        
                        <h6>{{ $series->title }}</h6>
                        
                        @if($series->description)
                            <p class="text-muted">{{ Str::limit($series->description, 150) }}</p>
                        @endif
                        
                        <a href="{{ route('member.sermons.filter', ['series_id' => $series->id]) }}" class="btn btn-sm btn-outline-primary">
                            View All Sermons in This Series
                        </a>
                    </div>
                </div>
            @endif
            
            @if($relatedSermons->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Related Sermons</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($relatedSermons as $relatedSermon)
                                <a href="{{ route('member.sermons.show', $relatedSermon->slug) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            @if($relatedSermon->thumbnail_path)
                                                <img src="{{ asset('storage/' . $relatedSermon->thumbnail_path) }}" alt="{{ $relatedSermon->title }}" class="rounded" style="width: 60px; height: 45px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary text-white d-flex justify-content-center align-items-center rounded" style="width: 60px; height: 45px;">
                                                    <i class="fas fa-bible"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">{{ Str::limit($relatedSermon->title, 50) }}</h6>
                                            <small class="text-muted">
                                                @if($relatedSermon->date_preached)
                                                    {{ date('M j, Y', strtotime($relatedSermon->date_preached)) }}
                                                @else
                                                    {{ $relatedSermon->created_at->format('M j, Y') }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Toggle favorite status
        $('#favoriteBtn').on('click', function() {
            const sermonId = $(this).data('id');
            const button = $(this);
            
            $.ajax({
                url: "{{ route('member.sermons.toggle-favorite') }}",
                type: "POST",
                data: {
                    sermon_id: sermonId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'added') {
                        button.removeClass('btn-outline-danger').addClass('btn-danger');
                        button.html('<i class="fas fa-heart"></i> Favorited');
                    } else {
                        button.removeClass('btn-danger').addClass('btn-outline-danger');
                        button.html('<i class="fas fa-heart"></i> Favorite');
                    }
                    
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });
    });
    
</script>
@endsection