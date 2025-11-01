<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Sermon;
use App\Models\User;
use App\Models\SermonSeries;
use App\Models\SermonTopic;
use App\Events\NewSermonEvent;
use App\Notifications\NewSermonNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class SermonController extends Controller
{
    /**
     * Display a listing of the sermons.
     */
    public function index()
    {
        $sermons = Sermon::with(['series', 'topics'])->latest()->get();
        return view('admin.sermons.index', compact('sermons'));
    }

    /**
     * Show the form for creating a new sermon.
     */
    public function create()
    {
        $sermonSeries = SermonSeries::all();
        $sermonTopics = SermonTopic::all();
        return view('admin.sermons.create', compact('sermonSeries', 'sermonTopics'));
    }

    /**
     * Store a newly created sermon.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scripture_reference' => 'nullable|string|max:255',
            'date_preached' => 'required|date',
            'speaker_name' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'featured' => 'nullable|boolean',
            'series_id' => 'nullable|exists:sermon_series,id',
            'video' => 'nullable|file|mimes:mp4,webm,mov,mkv,avi|max:20480',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a|max:20480',
            'thumbnail' => 'nullable|image|max:5000',
            'video_path' => 'nullable|string',
            'topics' => 'nullable|array',
            'topics.*' => 'exists:sermon_topics,id'
        ]);

        DB::beginTransaction();
        
        try {
            // Create sermon
            $sermon = new Sermon();
            $sermon->title = $request->title;
            $sermon->slug = Str::slug($request->title);
            $sermon->description = $request->description;
            $sermon->scripture_reference = $request->scripture_reference;
            $sermon->date_preached = $request->date_preached;
            $sermon->speaker_name = $request->speaker_name;
            $sermon->duration = $request->duration;
            $sermon->featured = $request->has('featured') ? 1 : 0;
            $sermon->series_id = $request->series_id;
            $sermon->view_count = 0;
            $sermon->download_count = 0;

            // Handle file uploads to Cloudinary
            if ($request->filled('video_path')) {
                $sermon->video_path = $request->input('video_path');
            } elseif ($request->hasFile('video')) {
                $uploadedFile = Cloudinary::upload($request->file('video')->getRealPath(), [
                    'folder' => 'sermons/videos',
                    'resource_type' => 'video'
                ]);
                $sermon->video_path = $uploadedFile->getSecurePath();
            }

            if ($request->hasFile('audio')) {
                $uploadedFile = Cloudinary::upload($request->file('audio')->getRealPath(), [
                    'folder' => 'sermons/audios',
                    'resource_type' => 'video'
                ]);
                $sermon->audio_path = $uploadedFile->getSecurePath();
            }

            if ($request->hasFile('thumbnail')) {
                $uploadedFile = Cloudinary::upload($request->file('thumbnail')->getRealPath(), [
                    'folder' => 'sermons/thumbnails'
                ]);
                $sermon->thumbnail_path = $uploadedFile->getSecurePath();
            }

            $sermon->save();
            
            // Sync topics
            if ($request->has('topics') && is_array($request->topics)) {
                $sermon->topics()->sync($request->topics);
            }

            // Send notifications
            $currentUser = auth()->user();
            $recipients = User::where('id', '!=', $currentUser->id)->get();
            
            Log::info('Sending notifications to ' . $recipients->count() . ' users for sermon: ' . $sermon->title);
            
            foreach ($recipients as $recipient) {
                try {
                    $recipient->notify(new NewSermonNotification($sermon));
                    Log::info('Notification sent to user: ' . $recipient->id);
                } catch (\Exception $e) {
                    Log::error('Failed to notify user ' . $recipient->id . ': ' . $e->getMessage());
                }
            }

            // Broadcast event
            event(new NewSermonEvent($sermon));
            
            DB::commit();
            
            return redirect()->route('admin.sermons.index')
                ->with('success', 'Sermon created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Note: Cloudinary files are not cleaned up on transaction failure
            // as they are managed by Cloudinary's auto-cleanup policies
            
            Log::error('Error creating sermon: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            
            return back()->withInput()
                ->with('error', 'Error creating sermon: ' . $e->getMessage());
        }
    }

    /**
     * Display a sermon.
     */
    public function show(string $id)
    {
        $sermon = Sermon::with(['series', 'topics'])->findOrFail($id);
        return view('admin.sermons.show', compact('sermon'));
    }

    /**
     * Edit a sermon.
     */
    public function edit(string $id)
    {
        $sermon = Sermon::with(['series', 'topics'])->findOrFail($id);
        $sermonSeries = SermonSeries::all();
        $sermonTopics = SermonTopic::all();
        return view('admin.sermons.edit', compact('sermon', 'sermonSeries', 'sermonTopics'));
    }

    /**
     * Update a sermon.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scripture_reference' => 'nullable|string|max:255',
            'date_preached' => 'nullable|date',
            'speaker_name' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'featured' => 'nullable|boolean',
            'series_id' => 'nullable|exists:sermon_series,id',
            'video' => 'nullable|file|mimes:mp4,webm,mov,mkv,avi|max:20480',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a|max:20480',
            'thumbnail' => 'nullable|image|max:5000',
            'topics' => 'nullable|array',
            'topics.*' => 'exists:sermon_topics,id'
        ]);

        DB::beginTransaction();

        try {
            $sermon = Sermon::findOrFail($id);
            
            // Store old file paths for potential cleanup
            $oldVideo = $sermon->video_path;
            $oldAudio = $sermon->audio_path;
            $oldThumbnail = $sermon->thumbnail_path;
            
            // Update basic fields
            $sermon->title = $request->title;
            
            // Only update slug if title has changed
            if ($sermon->isDirty('title')) {
                $sermon->slug = Str::slug($request->title);
            }

            $sermon->description = $request->description;
            $sermon->scripture_reference = $request->scripture_reference;
            $sermon->date_preached = $request->date_preached;
            $sermon->speaker_name = $request->speaker_name;
            $sermon->duration = $request->duration;
            $sermon->featured = $request->has('featured') ? 1 : 0;
            $sermon->series_id = $request->series_id;

            // Handle file uploads to Cloudinary
            if ($request->hasFile('video')) {
                $uploadedFile = Cloudinary::upload($request->file('video')->getRealPath(), [
                    'folder' => 'sermons/videos',
                    'resource_type' => 'video'
                ]);
                $sermon->video_path = $uploadedFile->getSecurePath();
            }

            if ($request->hasFile('audio')) {
                $uploadedFile = Cloudinary::upload($request->file('audio')->getRealPath(), [
                    'folder' => 'sermons/audios',
                    'resource_type' => 'video'
                ]);
                $sermon->audio_path = $uploadedFile->getSecurePath();
            }

            if ($request->hasFile('thumbnail')) {
                $uploadedFile = Cloudinary::upload($request->file('thumbnail')->getRealPath(), [
                    'folder' => 'sermons/thumbnails'
                ]);
                $sermon->thumbnail_path = $uploadedFile->getSecurePath();
            }

            $sermon->save();

            // Sync sermon topics
            if ($request->has('topics') && is_array($request->topics)) {
                $sermon->topics()->sync($request->topics);
            } else {
                $sermon->topics()->sync([]);
            }

            DB::commit();

            return redirect()->route('admin.sermons.index')
                ->with('success', 'Sermon updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sermon: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error updating sermon: ' . $e->getMessage());
        }
    }

    /**
     * Delete a sermon.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $sermon = Sermon::findOrFail($id);

            // Detach relations
            $sermon->topics()->detach();

            // Delete sermon record
            $sermon->forceDelete();

            // Note: Cloudinary files are managed by Cloudinary's lifecycle policies

            DB::commit();

            return redirect()->route('admin.sermons.index')
                ->with('success', 'Sermon deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sermon: ' . $e->getMessage());
            
            return back()->with('error', 'Error deleting sermon: ' . $e->getMessage());
        }
    }

    /**
     * Receive a video chunk and store it temporarily.
     */
    public function uploadChunk(Request $request)
    {
        try {
            Log::info('Raw request info', [
                'content_length' => $request->header('Content-Length'),
                'content_type' => $request->header('Content-Type'),
                'method' => $request->method(),
                'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
            ]);

            Log::info('Chunk upload request received', [
                'uploadId' => $request->input('uploadId'),
                'chunkIndex' => $request->input('chunkIndex'),
                'totalChunks' => $request->input('totalChunks'),
                'fileName' => $request->input('fileName'),
                'hasChunk' => $request->hasFile('chunk'),
                'chunkSize' => $request->hasFile('chunk') ? $request->file('chunk')->getSize() : 0
            ]);

            $request->validate([
                'chunk' => 'required|file|max:5120', // 5MB max per chunk
                'chunkIndex' => 'required|integer|min:0',
                'totalChunks' => 'required|integer|min:1',
                'uploadId' => 'required|string',
                'fileName' => 'required|string'
            ]);

            $uploadId = preg_replace('/[^A-Za-z0-9_\-]/', '', $request->input('uploadId'));
            $chunkIndex = (int) $request->input('chunkIndex');
            $fileName = basename($request->input('fileName'));

            // Ensure base chunks directory exists
            $baseChunksDir = storage_path('app/chunks');
            if (!is_dir($baseChunksDir)) {
                if (!mkdir($baseChunksDir, 0775, true)) {
                    throw new \Exception('Failed to create base chunks directory: ' . $baseChunksDir);
                }
            }

            // Temporary directory per upload
            $tempDir = $baseChunksDir . DIRECTORY_SEPARATOR . $uploadId;
            if (!is_dir($tempDir)) {
                if (!mkdir($tempDir, 0775, true)) {
                    throw new \Exception('Failed to create upload directory: ' . $tempDir);
                }
            }

            // Store chunk as sequential file
            $chunkFile = $request->file('chunk');
            $chunkPath = $tempDir . DIRECTORY_SEPARATOR . $chunkIndex . '.part';
            
            if (!$chunkFile->move($tempDir, $chunkIndex . '.part')) {
                throw new \Exception('Failed to move chunk file to: ' . $chunkPath);
            }

            Log::info('Chunk uploaded successfully', [
                'chunkIndex' => $chunkIndex,
                'chunkPath' => $chunkPath,
                'chunkSize' => filesize($chunkPath)
            ]);

            return response()->json([
                'success' => true,
                'chunkIndex' => $chunkIndex,
                'message' => 'Chunk received'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Chunk upload validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            Log::error('Chunk upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Chunk upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Concatenate chunks and move final file to public storage.
     */
    public function finalizeUpload(Request $request)
    {
        try {
            $request->validate([
                'uploadId' => 'required|string',
                'fileName' => 'required|string',
                'totalChunks' => 'required|integer|min:1',
            ]);

            $uploadId = preg_replace('/[^A-Za-z0-9_\-]/', '', $request->input('uploadId'));
            $totalChunks = (int) $request->input('totalChunks');
            $originalName = basename($request->input('fileName'));

            $tempDir = storage_path('app/chunks/' . $uploadId);
            
            if (!is_dir($tempDir)) {
                Log::error('Upload session not found: ' . $tempDir);
                return response()->json([
                    'success' => false,
                    'message' => 'Upload session not found'
                ], 400);
            }

            // Verify all chunks exist
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFile = $tempDir . DIRECTORY_SEPARATOR . $i . '.part';
                if (!file_exists($chunkFile)) {
                    Log::error('Missing chunk: ' . $i);
                    return response()->json([
                        'success' => false,
                        'message' => 'Missing chunk ' . $i
                    ], 400);
                }
            }

            // Create unique filename
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeBase = pathinfo($originalName, PATHINFO_FILENAME);
            $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $safeBase);
            $finalName = $safeBase . '_' . time() . '.' . $extension;

            // Assemble file path
            $assembledPath = storage_path('app/chunks/' . $uploadId . '_assembled.' . $extension);
            
            Log::info('Starting file assembly', [
                'uploadId' => $uploadId,
                'totalChunks' => $totalChunks,
                'assembledPath' => $assembledPath
            ]);

            // Concatenate chunks
            $output = fopen($assembledPath, 'wb');
            if (!$output) {
                throw new \Exception('Failed to create output file');
            }

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $tempDir . DIRECTORY_SEPARATOR . $i . '.part';
                $chunk = fopen($chunkPath, 'rb');
                if (!$chunk) {
                    fclose($output);
                    throw new \Exception('Failed to open chunk ' . $i);
                }
                
                // Copy chunk to output
                while (!feof($chunk)) {
                    $buffer = fread($chunk, 8192); // Read 8KB at a time
                    fwrite($output, $buffer);
                }
                fclose($chunk);
                
                // Delete chunk immediately after copying to save space
                @unlink($chunkPath);
            }
            fclose($output);

            Log::info('File assembled successfully', [
                'size' => filesize($assembledPath),
                'path' => $assembledPath
            ]);

            // Upload to Cloudinary with increased timeout
            Log::info('Starting Cloudinary upload');
            
            // Set longer timeout for large files
            ini_set('max_execution_time', 600); // 10 minutes
            
            $uploadedFile = Cloudinary::upload($assembledPath, [
                'folder' => 'sermons/videos',
                'resource_type' => 'video',
                'public_id' => pathinfo($finalName, PATHINFO_FILENAME),
                'chunk_size' => 6000000, // 6MB chunks to Cloudinary
                'timeout' => 600 // 10 minute timeout
            ]);
            
            $storagePath = $uploadedFile->getSecurePath();

            Log::info('Cloudinary upload successful', [
                'url' => $storagePath
            ]);

            // Cleanup
            @unlink($assembledPath);
            @rmdir($tempDir);

            return response()->json([
                'success' => true,
                'path' => $storagePath,
                'url' => $storagePath
            ]);
            
        } catch (\Exception $e) {
            Log::error('Finalize upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'uploadId' => $request->input('uploadId') ?? 'unknown'
            ]);
            
            // Cleanup on error
            if (isset($tempDir) && is_dir($tempDir)) {
                array_map('unlink', glob("$tempDir/*.*"));
                @rmdir($tempDir);
            }
            if (isset($assembledPath) && file_exists($assembledPath)) {
                @unlink($assembledPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Upload finalization failed: ' . $e->getMessage()
            ], 500);
        }
    }
}