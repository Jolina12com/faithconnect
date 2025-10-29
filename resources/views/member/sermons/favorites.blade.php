@extends('member.dashboard_member')

@section('title', 'My Favorite Sermons')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mt-4 mb-0">My Favorite Sermons</h1>
            <p class="text-muted">Your collection of saved sermons</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="input-group">
                <input type="text" id="search" class="form-control" placeholder="Search favorites..." aria-label="Search">
                <button class="btn btn-primary" type="button" id="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <select class="form-select" id="filter-series">
                <option value="">All Series</option>
                @foreach($sermonSeries as $series)
                    <option value="{{ $series->id }}">{{ $series->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <select class="form-select" id="filter-topic">
                <option value="">All Topics</option>
                @foreach($sermonTopics as $topic)
                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row" id="sermons-container">
        @include('member.sermons.partials.favorite_cards', ['sermons' => $sermons])
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Toggle favorite status
        $('.toggle-favorite').on('click', function() {
            const sermonId = $(this).data('id');
            const card = $(this).closest('.sermon-card').parent();
            
            $.ajax({
                url: "{{ route('member.sermons.toggle-favorite') }}",
                type: "POST",
                data: {
                    sermon_id: sermonId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'removed') {
                        // Remove the card with a fade effect
                        card.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if there are any cards left
                            if ($('.sermon-card').length === 0) {
                                $('#sermons-container').html(
                                    '<div class="col-12">' +
                                    '<div class="alert alert-info">' +
                                    '<p>You haven\'t added any sermons to your favorites yet.</p>' +
                                    '<a href="{{ route("member.sermons.index") }}" class="btn btn-primary mt-2">Browse Sermons</a>' +
                                    '</div>' +
                                    '</div>'
                                );
                            }
                        });
                        
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });
        
        // Filter favorites
        $('#filter-series, #filter-topic').on('change', function() {
            filterFavorites();
        });
        
        // Search favorites
        $('#search-button').on('click', function() {
            filterFavorites();
        });
        
        $('#search').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                filterFavorites();
            }
        });
        
        function filterFavorites() {
            const seriesId = $('#filter-series').val();
            const topicId = $('#filter-topic').val();
            const searchQuery = $('#search').val();
            
            $.ajax({
                url: "{{ route('member.sermons.filter-favorites') }}",
                type: "GET",
                data: {
                    series_id: seriesId,
                    topic_id: topicId,
                    search: searchQuery
                },
                success: function(response) {
                    $('#sermons-container').html(response);
                    
                    // Reinitialize favorite buttons
                    $('.toggle-favorite').on('click', function() {
                        const sermonId = $(this).data('id');
                        const card = $(this).closest('.sermon-card').parent();
                        
                        $.ajax({
                            url: "{{ route('member.sermons.toggle-favorite') }}",
                            type: "POST",
                            data: {
                                sermon_id: sermonId,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.status === 'removed') {
                                    card.fadeOut(300, function() {
                                        $(this).remove();
                                        
                                        if ($('.sermon-card').length === 0) {
                                            $('#sermons-container').html(
                                                '<div class="col-12">' +
                                                '<div class="alert alert-info">' +
                                                '<p>You haven\'t added any sermons to your favorites yet.</p>' +
                                                '<a href="{{ route("member.sermons.index") }}" class="btn btn-primary mt-2">Browse Sermons</a>' +
                                                '</div>' +
                                                '</div>'
                                            );
                                        }
                                    });
                                    
                                    toastr.success(response.message);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('An error occurred. Please try again.');
                            }
                        });
                    });
                },
                error: function(xhr) {
                    toastr.error('An error occurred while filtering sermons.');
                }
            });
        }
    });
</script>
@endsection