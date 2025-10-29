@if($sermons->count() > 0)
    @foreach($sermons as $sermon)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 sermon-card">
                <div class="card-img-top position-relative">
                    @if($sermon->thumbnail_path)
                        <img src="{{ asset('storage/' . $sermon->thumbnail_path) }}" alt="{{ $sermon->title }}" class="img-fluid">
                    @else
                        <div class="bg-secondary text-white d-flex justify-content-center align-items-center" style="height: 180px;">
                            <i class="fas fa-bible fa-3x"></i>
                        </div>
                    @endif

                    @if($sermon->duration)
                        <span class="position-absolute bottom-0 end-0 bg-dark text-white px-2 py-1 m-2 rounded">
                            {{ gmdate('H:i:s', $sermon->duration) }}
                        </span>
                    @endif

                    @if($sermon->featured)
                        <span class="position-absolute top-0 start-0 bg-warning text-dark px-2 py-1 m-2 rounded">
                            Featured
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <h5 class="card-title text-truncate">{{ $sermon->title }}</h5>

                    <div class="sermon-details mb-3">
                        @if($sermon->speaker_name)
                            <p class="mb-1"><i class="fas fa-user-tie me-2"></i> {{ $sermon->speaker_name }}</p>
                        @endif

                        @if($sermon->date_preached)
                            <p class="mb-1"><i class="fas fa-calendar-alt me-2"></i> {{ date('F j, Y', strtotime($sermon->date_preached)) }}</p>
                        @endif

                        @if($sermon->scripture_reference)
                            <p class="mb-1"><i class="fas fa-book-open me-2"></i> {{ $sermon->scripture_reference }}</p>
                        @endif
                    </div>

                    <p class="card-text text-muted">
                        {{ Str::limit($sermon->description, 100) }}
                    </p>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('member.sermons.show', $sermon->slug) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-play me-1"></i> Watch
                    </a>
                    <div class="d-flex">
                        @if($sermon->audio_path)
                            <a href="{{ route('member.sermons.download', ['sermon' => $sermon->slug, 'type' => 'audio']) }}" class="btn btn-sm btn-outline-secondary me-2" title="Download Audio">
                                <i class="fas fa-download"></i>
                            </a>
                        @endif

                        <button class="btn btn-sm btn-outline-danger toggle-favorite" data-id="{{ $sermon->id }}" title="Remove from favorites">
                            <i class="fas fa-heart text-danger"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="col-12 d-flex justify-content-center">
        {{ $sermons->links() }}
    </div>
@else
    <div class="col-12">
        <div class="alert alert-info">
            <p>You haven't added any sermons to your favorites yet.</p>
            <a href="{{ route('member.sermons.index') }}" class="btn btn-primary mt-2">Browse Sermons</a>
        </div>
    </div>
@endif


