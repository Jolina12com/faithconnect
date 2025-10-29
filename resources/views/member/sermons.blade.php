@extends('member.dashboard_member')

@section('content')
<div class="sermon-hero text-dark position-relative">
    <div class="container py-5">
        <div class="row py-5">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-3 fw-bold mb-4 text-gradient">Sermon Library</h1>
                <p class="lead mb-4 fs-4">Watch and listen to our latest messages and grow in your faith journey.</p>

                <form action="{{ route('member.sermons.index') }}" method="GET" class="sermon-search-form mt-5">
                    <div class="input-group input-group-lg">
                        <input type="text" name="search" class="form-control" placeholder="Search sermons..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-8">
            @if(request('search') || request('series') || request('speaker') || request('scripture'))
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sermons.index') }}">All Sermons</a></li>
                            @if(request('search'))
                                <li class="breadcrumb-item active">Search: "{{ request('search') }}"</li>
                            @endif
                            @if(request('series'))
                                <li class="breadcrumb-item active">Series: "{{ $activeSeries->title ?? request('series') }}"</li>
                            @endif
                            @if(request('speaker'))
                                <li class="breadcrumb-item active">Speaker: {{ request('speaker') }}</li>
                            @endif
                            @if(request('scripture'))
                                <li class="breadcrumb-item active">Scripture: {{ request('scripture') }}</li>
                            @endif
                        </ol>
                    </nav>
                    <a href="{{ route('sermons.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="d-flex justify-content-lg-end">
                <div class="dropdown me-2">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button"
                            id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><h6 class="dropdown-header">Filter by Series</h6></li>
                        @foreach($sermonSeries as $series)
                            <li>
                                <a class="dropdown-item {{ request('series') == $series->id ? 'active' : '' }}"
                                   href="{{ route('sermons.index', ['series' => $series->id]) }}">
                                    {{ $series->title }}
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Filter by Speaker</h6></li>
                        @foreach($speakers as $speaker)
                            <li>
                                <a class="dropdown-item {{ request('speaker') == $speaker ? 'active' : '' }}"
                                   href="{{ route('sermons.index', ['speaker' => $speaker]) }}">
                                    {{ $speaker }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button"
                            id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-sort me-1"></i>Sort
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'newest' || !request('sort') ? 'active' : '' }}"
                               href="{{ route('sermons.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}">
                                <i class="fas fa-calendar-alt me-1"></i>Newest First
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'oldest' ? 'active' : '' }}"
                               href="{{ route('sermons.index', array_merge(request()->except('sort'), ['sort' => 'oldest'])) }}">
                                <i class="fas fa-calendar-alt me-1"></i>Oldest First
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'title_asc' ? 'active' : '' }}"
                               href="{{ route('sermons.index', array_merge(request()->except('sort'), ['sort' => 'title_asc'])) }}">
                                <i class="fas fa-sort-alpha-down me-1"></i>Title A-Z
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'most_viewed' ? 'active' : '' }}"
                               href="{{ route('sermons.index', array_merge(request()->except('sort'), ['sort' => 'most_viewed'])) }}">
                                <i class="fas fa-eye me-1"></i>Most Popular
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(isset($featuredSermon) && !request('search') && !request('series') && !request('speaker') && !request('scripture'))
        <div class="featured-sermon mb-5">
            <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="position-relative h-100">
                            @if($featuredSermon->thumbnail)
                                <img src="{{ asset('storage/' . $featuredSermon->thumbnail) }}"
                                     class="featured-sermon-img" alt="{{ $featuredSermon->title }}">
                            @else
                                <div class="featured-sermon-placeholder d-flex align-items-center justify-content-center h-100 bg-dark">
                                    <i class="fas fa-microphone-alt fa-4x text-white opacity-50"></i>
                                </div>
                            @endif
                            <div class="featured-badge position-absolute top-0 start-0 m-4">
                                <span class="badge badge-gradient fs-6 px-3 py-2">✨ Featured Message</span>
                            </div>
                            <a href="{{ route('sermons.show', $featuredSermon->slug) }}"
                               class="play-button position-absolute top-50 start-50 translate-middle">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge badge-gradient me-2 fs-6 px-3 py-2">🔥 Latest</span>
                                @if($featuredSermon->series)
                                    <a href="{{ route('sermons.index', ['series' => $featuredSermon->series->id]) }}"
                                       class="badge badge-outline text-decoration-none fs-6 px-3 py-2">
                                        📚 {{ $featuredSermon->series->title }}
                                    </a>
                                @endif
                            </div>

                            <h2 class="card-title h2 fw-bold mb-4 lh-sm">
                                <a href="{{ route('member.sermons.show', $featuredSermon->slug) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ $featuredSermon->title }}
                                </a>
                            </h2>

                            <div class="d-flex align-items-center text-muted mb-3">
                                <div class="me-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($featuredSermon->date_preached)->format('M d, Y') }}
                                </div>

                                @if($featuredSermon->duration)
                                    <div>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ floor($featuredSermon->duration / 60) }} min
                                    </div>
                                @endif
                            </div>

                            @if($featuredSermon->speaker_name)
                                <div class="mb-3">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    <a href="{{ route('sermons.index', ['speaker' => $featuredSermon->speaker_name]) }}"
                                       class="text-decoration-none">
                                        {{ $featuredSermon->speaker_name }}
                                    </a>
                                </div>
                            @endif

                            @if($featuredSermon->scripture_reference)
                                <div class="mb-3">
                                    <i class="fas fa-bible text-primary me-1"></i>
                                    <a href="{{ route('sermons.index', ['scripture' => $featuredSermon->scripture_reference]) }}"
                                       class="text-decoration-none">
                                        {{ $featuredSermon->scripture_reference }}
                                    </a>
                                </div>
                            @endif

                            @if($featuredSermon->description)
                                <p class="card-text mb-4">{{ Str::limit($featuredSermon->description, 150) }}</p>
                            @endif

                            <div class="d-flex align-items-center">
                                <a href="{{ route('sermons.show', $featuredSermon->slug) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-play me-1"></i>Watch Now
                                </a>
                                <div class="ms-auto">
                                    <a href="#" class="btn btn-sm btn-outline-primary sermon-share-btn"
                                       data-bs-toggle="tooltip" title="Share">
                                        <i class="fas fa-share-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($latestSeries) && count($latestSeries) > 0 && !request('search') && !request('series') && !request('speaker') && !request('scripture'))
        <div class="sermon-series-section mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0">Latest Series</h2>
                <a href="{{ route('series.index') }}" class="btn btn-sm btn-outline-primary">View All Series</a>
            </div>

            <div class="row g-4">
                @foreach($latestSeries as $series)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 series-card">
                            <div class="position-relative">
                                @if($series->image)
                                    <img src="{{ asset('storage/' . $series->image) }}"
                                         class="card-img-top series-img" alt="{{ $series->title }}">
                                @else
                                    <div class="series-placeholder-img d-flex align-items-center justify-content-center bg-secondary">
                                        <i class="fas fa-layer-group fa-2x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h3 class="h5 card-title">{{ $series->title }}</h3>
                                <p class="text-muted small mb-0">{{ $series->sermon_count }} sermons</p>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="{{ route('sermons.index', ['series' => $series->id]) }}"
                                   class="btn btn-sm btn-outline-primary w-100">
                                    View Series
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 mb-4">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @elseif(request('series'))
                    Sermons in "{{ $activeSeries->title ?? request('series') }}"
                @elseif(request('speaker'))
                    Sermons by {{ request('speaker') }}
                @elseif(request('scripture'))
                    Sermons on {{ request('scripture') }}
                @else
                    All Sermons
                @endif
                <span class="text-muted fs-6 fw-normal ms-2">({{ $sermons->total() }} sermons)</span>
            </h2>
        </div>
    </div>

    @if($sermons->count() > 0)
        <div class="row g-4">
            @foreach($sermons as $sermon)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 sermon-card border-0 shadow-sm rounded-4">
                        <div class="position-relative sermon-card-thumbnail">
                            @if($sermon->thumbnail)
                                <img src="{{ asset('storage/' . $sermon->thumbnail) }}"
                                     class="card-img-top sermon-thumbnail" alt="{{ $sermon->title }}">
                            @else
                                <div class="sermon-placeholder-thumbnail d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-microphone-alt fa-2x text-muted"></i>
                                </div>
                            @endif

                            <a href="{{ route('member.sermons.show', $sermon->slug) }}"
                               class="play-button-sm position-absolute top-50 start-50 translate-middle">
                                <i class="fas fa-play"></i>
                            </a>

                            @if($sermon->featured)
                                <span class="badge badge-gradient position-absolute top-0 start-0 m-3 fs-6 px-3 py-2">
                                    ⭐ Featured
                                </span>
                            @endif

                            <div class="sermon-media-type position-absolute bottom-0 end-0 m-3">
                                @if($sermon->video)
                                    <span class="badge badge-gradient fs-6 px-3 py-2">
                                        🎥 Video
                                    </span>
                                @elseif($sermon->audio)
                                    <span class="badge badge-outline fs-6 px-3 py-2">
                                        🎵 Audio
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <h3 class="h5 card-title mb-3 fw-bold lh-sm">
                                <a href="{{ route('sermons.show', $sermon->slug) }}" class="sermon-title text-decoration-none text-dark hover-primary">
                                    {{ $sermon->title }}
                                </a>
                            </h3>

                            <div class="d-flex align-items-center text-muted small mb-2">
                                <div class="me-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($sermon->date_preached)->format('M d, Y') }}
                                </div>

                                @if($sermon->duration)
                                    <div>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ floor($sermon->duration / 60) }} min
                                    </div>
                                @endif
                            </div>

                            @if($sermon->speaker_name)
                                <div class="mb-2 small">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    <a href="{{ route('sermons.index', ['speaker' => $sermon->speaker_name]) }}"
                                       class="text-decoration-none">
                                        {{ $sermon->speaker_name }}
                                    </a>
                                </div>
                            @endif

                            @if($sermon->scripture_reference)
                                <div class="mb-2 small">
                                    <i class="fas fa-bible text-primary me-1"></i>
                                    <a href="{{ route('sermons.index', ['scripture' => $sermon->scripture_reference]) }}"
                                       class="text-decoration-none">
                                        {{ $sermon->scripture_reference }}
                                    </a>
                                </div>
                            @endif

                            @if($sermon->series)
                                <div class="mb-2 small">
                                    <i class="fas fa-layer-group text-primary me-1"></i>
                                    <a href="{{ route('sermons.index', ['series' => $sermon->series->id]) }}"
                                       class="text-decoration-none">
                                        Series: {{ $sermon->series->title }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge badge-outline fs-6 px-3 py-2">
                                        👁️ {{ $sermon->views ?? 0 }}
                                    </span>
                                </div>
                                <div>
                                    <a href="{{ route('member.sermons.show', $sermon->slug) }}"
                                       class="btn btn-sm btn-primary rounded-pill px-3">
                                        ▶️ Watch
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-primary rounded-pill sermon-share-btn"
                                       data-bs-toggle="tooltip" title="Share">
                                        📤
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $sermons->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-3x text-muted"></i>
            </div>
            <h3 class="h4 text-muted">No sermons found</h3>
            @if(request('search') || request('series') || request('speaker') || request('scripture'))
                <p>We couldn't find any sermons matching your search criteria.</p>
                <a href="{{ route('sermons.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-times me-1"></i>Clear All Filters
                </a>
            @else
                <p>Check back soon for new sermon content.</p>
            @endif
        </div>
    @endif
</div>

@if(count($sermonSeries) > 0 && !request('search') && !request('series') && !request('speaker') && !request('scripture'))
    <div class="py-5 border-top">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="h3 mb-4 fw-bold">📚 Browse by Series</h2>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        @foreach($sermonSeries as $series)
                            <a href="{{ route('sermons.index', ['series' => $series->id]) }}"
                               class="btn btn-outline-primary mb-2">
                                {{ $series->title }}
                                <span class="badge bg-primary ms-1">{{ $series->sermon_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="py-5 subscribe-section border-top">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="h3 mb-4 fw-bold text-gradient">🔔 Never Miss a Message</h2>
                <p class="mb-4 fs-5">Subscribe to receive notifications when new sermons are available.</p>

                <form class="row g-2 justify-content-center">
                    <div class="col-md-6">
                        <input type="email" class="form-control form-control-lg rounded-pill shadow-sm border-2" placeholder="Your email address">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">Subscribe</button>
                    </div>
                </form>

                <div class="mt-4">
                    <a href="#" class="text-dark me-4 social-link" title="YouTube">
                        <i class="fab fa-youtube fa-2x"></i>
                    </a>
                    <a href="#" class="text-dark me-4 social-link" title="Podcast">
                        <i class="fas fa-podcast fa-2x"></i>
                    </a>
                    <a href="#" class="text-dark me-4 social-link" title="Spotify">
                        <i class="fab fa-spotify fa-2x"></i>
                    </a>
                    <a href="#" class="text-dark social-link" title="Apple Podcasts">
                        <i class="fab fa-apple fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hero section styling */
    .sermon-hero {
        padding: 100px 0 80px;
        position: relative;
        border-bottom: 3px solid #e9ecef;
    }

    /* Text gradient */
    .text-gradient {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Search form styling */
    .sermon-search-form .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border: 2px solid #e9ecef;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .sermon-search-form .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Featured sermon styling */
    .featured-sermon-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .featured-sermon-placeholder {
        border: 2px dashed #dee2e6;
    }

    .play-button {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(78, 115, 223, 0.3);
    }

    .play-button:hover {
        box-shadow: 0 12px 35px rgba(78, 115, 223, 0.4);
        transform: scale(1.1) translateY(-2px);
    }

    .play-button-sm {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        opacity: 0;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(78, 115, 223, 0.3);
    }

    /* Sermon card styling */
    .sermon-card {
        transition: all 0.4s ease;
        overflow: hidden;
        border: 1px solid #f8f9fa;
    }

    .sermon-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .sermon-card:hover .play-button-sm {
        opacity: 1;
    }

    .sermon-card-thumbnail {
        overflow: hidden;
    }

    .sermon-card-thumbnail img {
        transition: transform 0.5s ease;
    }

    .sermon-card:hover .sermon-card-thumbnail img {
        transform: scale(1.08);
    }

    .sermon-thumbnail {
        height: 200px;
        object-fit: cover;
    }

    .sermon-placeholder-thumbnail {
        height: 200px;
        border: 2px dashed #dee2e6;
    }

    .hover-primary:hover {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
    }

    /* Series card styling */
    .series-card {
        transition: all 0.4s ease;
        border: 1px solid #f8f9fa;
    }

    .series-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .series-img {
        height: 180px;
        object-fit: cover;
    }

    .series-placeholder-img {
        height: 180px;
        border: 2px dashed #dee2e6;
    }

    /* Badge styling */
    .badge-gradient {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border: none;
    }

    .badge-outline {
        border: 2px solid #4e73df;
        color: #4e73df;
        background: transparent;
    }

    /* Social links */
    .social-link {
        transition: all 0.3s ease;
        opacity: 0.7;
    }

    .social-link:hover {
        opacity: 1;
        transform: translateY(-3px);
        color: #4e73df !important;
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-color: #4e73df;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }

    .page-link {
        color: #4e73df;
        border-radius: 8px;
        margin: 0 2px;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        color: white;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }

    /* Filter and sorting dropdowns */
    .dropdown-menu {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border: none;
        border-radius: 12px;
    }

    .dropdown-item {
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 2px 8px;
    }

    .dropdown-item.active, .dropdown-item:active {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    .dropdown-item:hover {
        background: rgba(78, 115, 223, 0.1);
        transform: translateX(5px);
    }

    /* Button enhancements */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
    }

    .btn-primary:hover {
        box-shadow: 0 6px 20px rgba(78, 115, 223, 0.4);
    }

    .btn-outline-primary {
        border: 2px solid #4e73df;
        color: #4e73df;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border-color: #4e73df;
    }

    /* Media queries for responsiveness */
    @media (max-width: 991.98px) {
        .featured-sermon .row {
            flex-direction: column;
        }

        .featured-sermon-img {
            height: 300px;
        }
    }

    @media (max-width: 767.98px) {
        .sermon-hero {
            padding: 60px 0;
        }

        .play-button {
            width: 60px;
            height: 60px;
            font-size: 20px;
        }

        .text-gradient {
            font-size: 2.5rem !important;
        }
    }
    
    /* Custom animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sermon-card {
        animation: fadeInUp 0.6s ease forwards;
    }

    .sermon-card:nth-child(2) { animation-delay: 0.1s; }
    .sermon-card:nth-child(3) { animation-delay: 0.2s; }
    .sermon-card:nth-child(4) { animation-delay: 0.3s; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Share buttons functionality
        const shareButtons = document.querySelectorAll('.sermon-share-btn');
        shareButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Get the URL of the current page or sermon
                const sermonUrl = this.closest('.card').querySelector('.sermon-title')?.href || window.location.href;

                // If Web Share API is supported by the browser
                if (navigator.share) {
                    navigator.share({
                        title: document.title,
                        url: sermonUrl
                    })
                    .catch(console.error);
                } else {
                    // Fallback - copy to clipboard
                    navigator.clipboard.writeText(sermonUrl).then(function() {
                        // Create a temporary tooltip
                        const tooltip = bootstrap.Tooltip.getInstance(button);
                        tooltip.hide();

                        // Change title temporarily
                        button.setAttribute('data-bs-original-title', 'Link copied!');
                        tooltip.show();

                        // Reset after 2 seconds
                        setTimeout(function() {
                            button.setAttribute('data-bs-original-title', 'Share');
                            tooltip.hide();
                        }, 2000);
                    });
                }
            });
        });
    });
</script>
@endsection
