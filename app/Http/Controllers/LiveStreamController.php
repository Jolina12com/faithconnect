<?php

namespace App\Http\Controllers;

use App\Models\LiveStream;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use App\Models\LivestreamReaction;
use App\Models\StreamViewer;



class LiveStreamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function auth(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        return $pusher->socket_auth($request->channel_name, $request->socket_id);
    }

    public function checkAllowed(Request $request)
    {
        $userId = $request->input('user_id');

        if (Auth::id() != $userId) {
            return response()->json(['allowed' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $allowedToStream = true; // Replace with your permission logic

        return response()->json([
            'allowed' => $allowedToStream,
            'message' => $allowedToStream ? 'You are authorized to broadcast' : 'You do not have broadcaster permissions'
        ]);
    }

    public function getLivekitUrl()
    {
        return response()->json([
            'url' => config('livekit.url')
        ]);
    }

    public function getRoomName()
    {
        return response()->json([
            'room' => config('livekit.room_name', 'default-room')
        ]);
    }

    public function getToken(Request $request)
    {
        $userId = $request->input('user_id');
        $metadata = $request->input('metadata', '{}');
        
        // Get user's full name
        $user = Auth::user();
        $userName = 'Anonymous';
        if ($user) {
            $userName = trim($user->first_name . ' ' . $user->last_name);
            if (empty($userName)) {
                $userName = $user->name ?? 'User ' . $user->id;
            }
        }
        
        Log::info('Token request', [
            'user_id' => $userId,
            'user_name' => $userName,
            'livekit_url' => config('livekit.url'),
            'has_api_key' => !empty(config('livekit.api_key')),
            'has_api_secret' => !empty(config('livekit.api_secret'))
        ]);
        
        if (empty(config('livekit.api_key')) || empty(config('livekit.api_secret'))) {
            Log::error('LiveKit credentials not configured');
            return response()->json(['error' => 'LiveKit not properly configured'], 500);
        }

        try {
            $token = $this->generateLiveKitToken(
                config('livekit.api_key'),
                config('livekit.api_secret'),
                config('livekit.room_name', 'default-room'),
                $userId,
                $userName,
                true
            );

            Log::info('Token generated successfully', [
                'user_id' => $userId,
                'user_name' => $userName,
                'token_length' => strlen($token)
            ]);

            return response()->json(['token' => $token]);

        } catch (\Exception $e) {
            Log::error('LiveKit token generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to generate token'], 500);
        }
    }

    private function generateLiveKitToken($apiKey, $apiSecret, $roomName, $userId, $userName, $canPublish = false)
    {
        $now = time();
        $exp = $now + 3600;

        $payload = [
            'iss' => $apiKey,
            'sub' => $userId,
            'name' => $userName,
            'nbf' => $now,
            'exp' => $exp,
            'video' => [
                'roomJoin' => true,
                'room' => $roomName,
                'canPublish' => $canPublish,
                'canSubscribe' => true,
                'canPublishData' => true,
                'canPublishScreen' => true,
            ],
            'screen' => [
                'roomJoin' => true,
                'room' => $roomName,
                'canPublish' => $canPublish,
                'canSubscribe' => true,
            ]
        ];

        return JWT::encode($payload, $apiSecret, 'HS256');
    }

    public function storeReaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string',
                'room_name' => 'required|string',
                'participant_identity' => 'required|string'
            ]);

            $reaction = LivestreamReaction::create([
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'room_name' => $validated['room_name'],
                'participant_identity' => $validated['participant_identity'],
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'reaction' => $reaction
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store reaction', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to store reaction'
            ], 500);
        }
    }
    
    public function startRecording(Request $request)
    {
        $roomName = $request->input('room_name', config('livekit.room_name'));
        $streamId = $request->input('stream_id');
        $title = $request->input('title', 'Untitled Stream');

        try {
            if (empty($streamId)) {
                $streamKey = 'stream_' . auth()->id() . '_' . time() . '_' . Str::random(6);
            } else {
                $streamKey = $streamId;
            }

            $stream = LiveStream::create([
                'user_id' => auth()->id(),
                'title' => $title,
                'room_name' => $roomName,
                'stream_id' => $streamKey,
                'status' => 'live',
                'started_at' => now(),
            ]);

            Log::info('Recording started', [
                'db_id' => $stream->id,
                'stream_key' => $streamKey,
                'room' => $roomName
            ]);

            return response()->json([
                'success' => true,
                'stream_id' => $stream->id,
                'stream_key' => $streamKey,
                'message' => 'Recording started - will be saved when stream ends'
            ]);

        } catch (\Exception $e) {
            Log::error('Recording start failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to start recording'
            ], 500);
        }
    }

    public function stopRecording(Request $request)
{
    $streamId = $request->input('stream_id');
    $hasData = filter_var($request->input('has_data', true), FILTER_VALIDATE_BOOLEAN); // Default to true
    
    try {
        $stream = LiveStream::find($streamId);
        
        if (!$stream) {
            return response()->json([
                'success' => false,
                'error' => 'Stream not found'
            ], 404);
        }

        // Calculate duration
        $durationSeconds = now()->diffInSeconds($stream->started_at);
        
        Log::info('Recording stopped', [
            'stream_id' => $stream->id,
            'duration_seconds' => $durationSeconds,
            'has_data' => $hasData
        ]);

        // Mark all active viewers as left
        \DB::table('stream_viewers')
            ->where('stream_id', $stream->id)
            ->whereNull('left_at')
            ->update([
                'left_at' => now(),
                'duration_seconds' => \DB::raw('TIMESTAMPDIFF(SECOND, joined_at, NOW())'),
                'updated_at' => now()
            ]);

        // Only mark as 'ended' if explicitly no data or duration is too short
        if (!$hasData || $durationSeconds < 3) {
            $stream->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);
            Log::warning('Stream ended without recording', [
                'stream_id' => $stream->id,
                'reason' => !$hasData ? 'no_data' : 'too_short'
            ]);
        } else {
            $stream->update([
                'status' => 'processing',
                'ended_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Recording stopped'
        ]);

    } catch (\Exception $e) {
        Log::error('Recording stop failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Failed to stop recording'
        ], 500);
    }
}

    public function uploadRecordingFile(Request $request, CloudinaryService $cloudinary)
    {
        // Extend execution time for large uploads
       @ini_set('max_execution_time', '900'); // 15 minutes instead of 10
        @set_time_limit(900);
        Log::info('Upload request received', [
            'has_file' => $request->hasFile('video'),
            'stream_id' => $request->input('stream_id'),
            'file_size' => $request->hasFile('video') ? $request->file('video')->getSize() : 0
        ]);

        $request->validate([
            'video' => 'required|file|mimes:webm,mp4|max:512000',
            'stream_id' => 'required|exists:live_streams,id'
        ]);

        $streamId = $request->stream_id;
        $video = $request->file('video');
        
        try {
            $stream = LiveStream::find($streamId);
            
            if (!$stream) {
                Log::error('Stream not found', ['stream_id' => $streamId]);
                return response()->json([
                    'success' => false,
                    'error' => 'Stream not found'
                ], 404);
            }
            
            if (!empty($stream->cloudinary_public_id) && $stream->status === 'ended') {
                Log::info('Upload skipped: stream already has Cloudinary recording', [
                    'stream_id' => $stream->id,
                    'cloudinary_public_id' => $stream->cloudinary_public_id
                ]);
                return response()->json([
                    'success' => true,
                    'stream' => $stream,
                    'message' => 'Upload skipped; recording already exists'
                ]);
            }

            Log::info('Starting Cloudinary upload', [
                'stream_id' => $stream->id,
                'file_name' => $video->getClientOriginalName(),
                'file_size_mb' => round($video->getSize() / 1024 / 1024, 2)
            ]);
            
            $result = $cloudinary->uploadRecording($video->getRealPath(), $video->getClientOriginalName());
            
            if (!$result) {
                throw new \Exception('Cloudinary upload returned null');
            }
            
            Log::info('Cloudinary upload successful', [
                'public_id' => $result['public_id'] ?? null,
                'url' => $result['secure_url'] ?? ($result['url'] ?? null)
            ]);
            
            // Mark all remaining viewers as left when upload completes
            \DB::table('stream_viewers')
                ->where('stream_id', $stream->id)
                ->whereNull('left_at')
                ->update([
                    'left_at' => now(),
                    'duration_seconds' => \DB::raw('TIMESTAMPDIFF(SECOND, joined_at, NOW())'),
                    'updated_at' => now()
                ]);
            
            $stream->update([
                'cloudinary_public_id' => $result['public_id'] ?? null,
                'replay_url' => $result['secure_url'] ?? ($result['url'] ?? null),
                'thumbnail_url' => isset($result['public_id']) ? $cloudinary->getThumbnailUrl($result['public_id']) : null,
                'duration' => isset($result['duration']) ? (int)$result['duration'] : 0,
                'file_size' => isset($result['bytes']) ? $result['bytes'] : $video->getSize(),
                'status' => 'ended'
            ]);
            
            Log::info('Stream updated successfully', ['stream_id' => $stream->id]);
            
            return response()->json([
                'success' => true,
                'stream' => $stream,
                'message' => 'Recording uploaded successfully'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'error' => 'Validation failed: ' . json_encode($e->errors())
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recordings()
    {
        $streams = LiveStream::where('status', 'ended')
            ->whereNotNull('replay_url')
            ->with('user:id,first_name,last_name')
            ->latest('ended_at')
            ->paginate(12);

        return view('member.recordings', compact('streams'));
    }

    public function showViewerPage()
    {
        return view('viewer');
    }

    public function watchRecording($id)
    {
        $stream = LiveStream::with('user:id,first_name,last_name')
            ->find($id);
        
        if (!$stream) {
            Log::error('Stream not found', ['id' => $id]);
            abort(404, 'Stream not found in database');
        }
        
        Log::info('Stream found', [
            'id' => $stream->id,
            'status' => $stream->status,
            'has_replay_url' => !empty($stream->replay_url),
            'replay_url' => $stream->replay_url
        ]);
        
        if ($stream->status !== 'ended' || !$stream->replay_url) {
            abort(404, 'Recording not available - Status: ' . $stream->status);
        }

        return view('member.watch-recording', compact('stream'));
    }

    public function getPastStreams()
    {
        $streams = LiveStream::where('status', 'ended')
            ->whereNotNull('replay_url')
            ->with('user:id,first_name,last_name')
            ->latest('ended_at')
            ->paginate(10);

        return response()->json($streams);
    }

    public function index()
    {
        $streams = LiveStream::with('user')
            ->latest()
            ->paginate(10);
        
        return view('admin.livestream.index', compact('streams'));
    }

    public function destroy($id)
    {
        try {
            $stream = LiveStream::findOrFail($id);
            
            // Try to delete from Cloudinary if configured
            if ($stream->cloudinary_public_id) {
                try {
                    $cloudinary = app(CloudinaryService::class);
                    $cloudinary->deleteRecording($stream->cloudinary_public_id);
                } catch (\Exception $e) {
                    Log::warning('Cloudinary deletion failed, continuing with database deletion', [
                        'error' => $e->getMessage(),
                        'stream_id' => $id
                    ]);
                }
            }
            
            $stream->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Stream deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stream deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete stream'
            ], 500);
        }
    }

    public function getReactions(Request $request)
    {
        try {
            $roomName = $request->input('room_name');
            
            $reactions = LivestreamReaction::where('room_name', $roomName)
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();

            $reactionCounts = LivestreamReaction::where('room_name', $roomName)
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type');

            return response()->json([
                'success' => true,
                'reactions' => $reactions,
                'counts' => $reactionCounts
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get reactions', [
                'error' => $e->getMessage(),
                'room' => $request->input('room_name')
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get reactions'
            ], 500);
        }
    }

    public function viewerJoined(Request $request)
    {
        try {
            $validated = $request->validate([
                'stream_id' => 'required|string',
                'participant_identity' => 'required|string',
                'viewer_name' => 'nullable|string'
            ]);

            // Try to find active stream by ID or room name
            $stream = LiveStream::where('id', $validated['stream_id'])
                ->orWhere('room_name', $validated['stream_id'])
                ->where('status', 'live')
                ->first();

            if (!$stream) {
                Log::warning('No active stream found for viewer tracking', [
                    'stream_id' => $validated['stream_id']
                ]);
                return response()->json(['success' => false, 'message' => 'No active stream'], 404);
            }

            $viewer = StreamViewer::create([
                'stream_id' => $stream->id,
                'user_id' => auth()->id(),
                'viewer_name' => $validated['viewer_name'] ?? (auth()->user()->first_name ?? 'Anonymous'),
                'participant_identity' => $validated['participant_identity'],
                'joined_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'viewer_id' => $viewer->id
            ]);
        } catch (\Exception $e) {
            Log::error('Viewer join tracking failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    public function viewerLeft(Request $request)
    {
        try {
            $validated = $request->validate([
                'stream_id' => 'required|string',
                'participant_identity' => 'required|string'
            ]);

            // Find stream by ID or room name
            $stream = LiveStream::where('id', $validated['stream_id'])
                ->orWhere('room_name', $validated['stream_id'])
                ->first();

            if (!$stream) {
                return response()->json(['success' => false, 'message' => 'Stream not found'], 404);
            }

            $viewer = StreamViewer::where('stream_id', $stream->id)
                ->where('participant_identity', $validated['participant_identity'])
                ->whereNull('left_at')
                ->first();

            if ($viewer) {
                $duration = now()->diffInSeconds($viewer->joined_at);
                $viewer->update([
                    'left_at' => now(),
                    'duration_seconds' => $duration
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Viewer leave tracking failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    public function getStreamViewers($streamId)
    {
        try {
            $stream = LiveStream::with(['viewers' => function($query) {
                $query->with('user:id,first_name,last_name')
                      ->orderBy('joined_at', 'desc');
            }])->findOrFail($streamId);

            // Auto-cleanup stale viewers for ended streams
            if ($stream->status !== 'live') {
                $endTime = $stream->ended_at ?? now();
                \DB::table('stream_viewers')
                    ->where('stream_id', $stream->id)
                    ->whereNull('left_at')
                    ->update([
                        'left_at' => $endTime,
                        'duration_seconds' => \DB::raw('TIMESTAMPDIFF(SECOND, joined_at, "' . $endTime . '")'),
                        'updated_at' => now()
                    ]);
                
                // Refresh the relationship
                $stream->load('viewers');
            }

            $viewers = $stream->viewers->map(function($viewer) {
                return [
                    'id' => $viewer->id,
                    'name' => $viewer->user ? 
                        $viewer->user->first_name . ' ' . $viewer->user->last_name : 
                        $viewer->viewer_name,
                    'joined_at' => $viewer->joined_at->format('M d, Y h:i A'),
                    'left_at' => $viewer->left_at ? $viewer->left_at->format('M d, Y h:i A') : 'Still watching',
                    'duration' => $viewer->duration_seconds > 0 ? 
                        gmdate('H:i:s', $viewer->duration_seconds) : '-'
                ];
            });

            return response()->json([
                'success' => true,
                'viewers' => $viewers,
                'total_count' => $viewers->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get stream viewers', [
                'error' => $e->getMessage(),
                'stream_id' => $streamId
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    public function getLiveStreams()
    {
        $liveStreams = LiveStream::where('status', 'live')
            ->with('user:id,first_name,last_name')
            ->latest('started_at')
            ->get();

        return response()->json($liveStreams);
    }

    public function getActiveStream()
    {
        $stream = LiveStream::where('status', 'live')
            ->with('user:id,first_name,last_name')
            ->latest('started_at')
            ->first();

        return response()->json($stream);
    }

    public function cleanupStaleViewers()
    {
        // Mark viewers as left for streams that ended more than 5 minutes ago
        $cutoff = now()->subMinutes(5);
        
        $updated = \DB::table('stream_viewers')
            ->whereNull('left_at')
            ->whereExists(function($query) use ($cutoff) {
                $query->select(\DB::raw(1))
                      ->from('live_streams')
                      ->whereColumn('live_streams.id', 'stream_viewers.stream_id')
                      ->where('status', '!=', 'live')
                      ->where('ended_at', '<', $cutoff);
            })
            ->update([
                'left_at' => now(),
                'duration_seconds' => \DB::raw('TIMESTAMPDIFF(SECOND, joined_at, NOW())'),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'updated' => $updated
        ]);
    }

    public function emergencyEndStream(Request $request)
    {
        try {
            $streamId = $request->input('stream_id');
            
            if (!$streamId) {
                return response()->json(['success' => false], 400);
            }

            $stream = LiveStream::find($streamId);
            
            if (!$stream || $stream->status !== 'live') {
                return response()->json(['success' => false], 404);
            }

            // Mark stream as ended
            $stream->update([
                'status' => 'ended',
                'ended_at' => now()
            ]);

            // Mark all viewers as left
            \DB::table('stream_viewers')
                ->where('stream_id', $stream->id)
                ->whereNull('left_at')
                ->update([
                    'left_at' => now(),
                    'duration_seconds' => \DB::raw('TIMESTAMPDIFF(SECOND, joined_at, NOW())'),
                    'updated_at' => now()
                ]);

            Log::info('Emergency stream end', ['stream_id' => $stream->id]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Emergency end failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}