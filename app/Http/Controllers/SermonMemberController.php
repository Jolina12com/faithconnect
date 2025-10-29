<?php

namespace App\Http\Controllers;

use App\Models\Sermon;
use App\Models\SermonSeries;
use App\Models\SermonTopic;
use App\Models\SermonView;
use App\Models\SermonFavorite;
use App\Models\SermonNote;
use App\Models\SermonDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SermonMemberController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the member's saved sermons.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
{
    $search = $request->input('search');
        $seriesId = $request->input('series_id');
        $topicId = $request->input('topic_id');
        $featured = $request->input('featured');
        $mediaType = $request->input('media_type');
        
        $sermons = Sermon::with(['series', 'topics'])
            ->when($search, fn($q) => $q->where('title', 'like', "%$search%"))
            ->when($seriesId, fn($q) => $q->where('series_id', $seriesId))
            ->when($topicId, fn($q) => $q->whereHas('topics', fn($q) => $q->where('sermon_topics.id', $topicId)))
            ->when($featured, fn($q) => $q->where('featured', true))
            ->when($mediaType === 'video', fn($q) => $q->whereNotNull('video_path'))
            ->when($mediaType === 'audio', fn($q) => $q->whereNotNull('audio_path'))
            ->latest('date_preached')
            ->paginate(9);

        $series = SermonSeries::all();
        $topics = SermonTopic::all();

        return view('member.sermons.index', compact('sermons', 'series', 'topics'));
    }

    /**
     * Display a specific sermon.
     *
     * @param string $sermon
     * @return \Illuminate\View\View
     */
    public function show($sermon)
    {
        $sermon = Sermon::where('slug', $sermon)
            ->orWhere('id', $sermon)
            ->with(['series', 'topics'])
            ->firstOrFail();

        // Increment view count
        $sermon->incrementViewCount();

        // Check if user has favorited this sermon
        $sermon_favorites = Auth::user()->hasFavoritedSermon($sermon->id);

        // Get related sermons
        $relatedSermons = $sermon->relatedSermons(3);

        // Get series if exists
        $series = $sermon->series;

        // Get topics
        $topics = $sermon->topics;

        return view('member.sermons.show', compact('sermon', 'sermon_favorites', 'relatedSermons', 'series', 'topics'));
    }

    /**
     * Display sermons by series.
     *
     * @param string $series
     * @return \Illuminate\View\View
     */
    public function series($series)
    {
        $series = SermonSeries::where('slug', $series)
            ->orWhere('id', $series)
            ->firstOrFail();

        $sermons = $series->sermons()
            ->with(['topics'])
            ->latest('date_preached')
            ->paginate(9);

        return view('member.sermons.series', compact('sermons', 'series'));
    }

    /**
     * Display sermons by topic.
     *
     * @param string $topic
     * @return \Illuminate\View\View
     */
    public function topics($topic)
    {
        $topic = SermonTopic::where('slug', $topic)
            ->orWhere('id', $topic)
            ->firstOrFail();

        $sermons = $topic->sermons()
            ->with(['series', 'topics'])
            ->latest('date_preached')
            ->paginate(9);

        return view('member.sermons.topics', compact('sermons', 'topic'));
    }

    /**
     * Display user's favorite sermons.
     *
     * @return \Illuminate\View\View
     */
    public function favorites()
    {
        $sermons = Auth::user()->favoriteSermons()
            ->with(['series', 'topics'])
            ->latest('date_preached')
            ->paginate(9);

        // For filters
        $sermonSeries = SermonSeries::select('id','title')->get();
        $sermonTopics = SermonTopic::select('id','name')->get();
        $seriesMap = $sermonSeries->pluck('title','id');

        return view('member.sermons.favorites', compact('sermons','sermonSeries','sermonTopics','seriesMap'));
    }

    /**
     * Filter favorite sermons via AJAX.
     */
    public function filterFavorites(Request $request)
    {
        $query = Auth::user()->favoriteSermons()->with(['series','topics']);

        if ($request->filled('series_id')) {
            $query->where('series_id', $request->input('series_id'));
        }
        if ($request->filled('topic_id')) {
            $query->whereHas('topics', function($q) use ($request) {
                $q->where('sermon_topics.id', $request->input('topic_id'));
            });
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title','like',"%$search%")
                  ->orWhere('speaker_name','like',"%$search%")
                  ->orWhere('scripture_reference','like',"%$search%");
            });
        }

        $sermons = $query->latest('date_preached')->paginate(9);

        // Render only the cards HTML to replace in page
        $html = view('member.sermons.partials.favorite_cards', [
            'sermons' => $sermons
        ])->render();

        return response($html);
    }

    /**
     * Toggle favorite status for a sermon.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'sermon_id' => 'required|exists:sermons,id'
        ]);

        $result = Auth::user()->toggleFavoriteSermon($request->sermon_id);

        return response()->json($result);
    }

    /**
     * Download sermon media.
     *
     * @param string $sermon
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function download($sermon, $type)
    {
        $sermon = Sermon::where('slug', $sermon)
            ->orWhere('id', $sermon)
            ->firstOrFail();

        $filePath = null;
        $fileName = null;

        if ($type === 'audio' && $sermon->audio_path) {
            $filePath = $sermon->audio_path;
            $fileName = Str::slug($sermon->title) . '.mp3';
        } elseif ($type === 'video' && $sermon->video_path) {
            $filePath = $sermon->video_path;
            $fileName = Str::slug($sermon->title) . '.mp4';
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Increment download count
        $sermon->incrementDownloadCount();

        return Storage::disk('public')->download($filePath, $fileName);
}
}
