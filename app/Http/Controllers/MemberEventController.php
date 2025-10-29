<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPoll;
use App\Models\EventPollOption;
use App\Models\EventPollResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MemberEventController extends Controller
{
    /**
     * Display the events for members
     */
    public function index()
    {
        // Eager load poll and poll options relationship
        $events = Event::with(['poll', 'poll.options', 'poll.responses'])->get();
        
        // Add user's response if they've responded
        foreach ($events as $event) {
            if ($event->poll) {
                $event->userResponse = EventPollResponse::where([
                    'poll_id' => $event->poll->id,
                    'user_id' => Auth::id()
                ])->first();
            }
        }
        
        return view('member.view_events', compact('events'));
    }
    
    /**
     * Show a specific event with poll details
     */
    public function show($id)
    {
        try {
            $event = Event::with(['poll', 'poll.options'])->findOrFail($id);
                
            // Get the user's existing response if any
            $userResponse = null;
            if ($event->poll) {
                $userResponse = EventPollResponse::where([
                    'poll_id' => $event->poll->id,
                    'user_id' => Auth::id()
                ])->first();
                
                // Only include response counts, not individual responses
                $responseCounts = [];
                foreach ($event->poll->options as $option) {
                    $responseCounts[$option->id] = $event->poll->responses->where('option_id', $option->id)->count();
                }
                $event->poll->responseCounts = $responseCounts;
            }
            
            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json([
                    'event' => $event,
                    'userResponse' => $userResponse
                ]);
            }
            
            return view('member.events.show', compact('event', 'userResponse'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Error fetching event details: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error fetching event details: ' . $e->getMessage());
        }
    }
    
    /**
     * Submit a response to an event poll
     */
    public function submitPollResponse(Request $request, $eventId)
    {
        try {
            $event = Event::with('poll')->findOrFail($eventId);
            
            if (!$event->poll) {
                return response()->json([
                    'success' => false,
                    'message' => 'This event does not have an active poll.'
                ], 404);
            }
            
            // Check if poll deadline has passed
            if ($event->poll->deadline && Carbon::parse($event->poll->deadline)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The deadline for this poll has passed.'
                ], 400);
            }
            
            $validated = $request->validate([
                'option_id' => 'required|exists:event_poll_options,id',
                'comment' => 'nullable|string|max:500',
            ]);
            
            // Check if user has already responded
            $existingResponse = EventPollResponse::where([
                'poll_id' => $event->poll->id,
                'user_id' => Auth::id()
            ])->first();
            
            if ($existingResponse) {
                // Update existing response
                $existingResponse->option_id = $validated['option_id'];
                $existingResponse->comment = $validated['comment'] ?? null;
                $existingResponse->save();
                $message = 'Your response has been updated.';
            } else {
                // Create new response
                $response = new EventPollResponse();
                $response->poll_id = $event->poll->id;
                $response->option_id = $validated['option_id'];
                $response->user_id = Auth::id();
                $response->comment = $validated['comment'] ?? null;
                $response->save();
                $message = 'Your response has been recorded.';
            }
            
            // Get updated poll data
            $event->load(['poll', 'poll.options', 'poll.responses.user', 'poll.responses.option']);
            
            // Calculate response counts
            $responseCounts = [];
            foreach ($event->poll->options as $option) {
                $responseCounts[$option->id] = $event->poll->responses->where('option_id', $option->id)->count();
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'poll' => [
                    'id' => $event->poll->id,
                    'responses' => $event->poll->responses,
                    'responseCounts' => $responseCounts
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting response: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get poll details for an event
     */
    public function getPollDetails($id)
    {
        try {
            $event = Event::with(['poll', 'poll.options', 'poll.responses.user', 'poll.responses.option'])
                ->findOrFail($id);
                
            if (!$event->poll) {
                return response()->json([
                    'error' => 'This event does not have a poll.'
                ], 404);
            }
            
            // Get the user's existing response if any
            $userResponse = null;
            if ($event->poll) {
                $userResponse = EventPollResponse::where([
                    'poll_id' => $event->poll->id,
                    'user_id' => Auth::id()
                ])->first();
                
                // Only include response counts, not individual responses
                $responseCounts = [];
                foreach ($event->poll->options as $option) {
                    $responseCounts[$option->id] = $event->poll->responses->where('option_id', $option->id)->count();
                }
                $event->poll->responseCounts = $responseCounts;
            }
            
            return response()->json([
                'poll' => $event->poll,
                'userResponse' => $userResponse
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching poll details: ' . $e->getMessage()
            ], 500);
        }
    }
}