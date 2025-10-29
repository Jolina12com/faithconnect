<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPoll;
use App\Models\EventPollOption;
use App\Models\EventPollResponse;
use Illuminate\Support\Facades\Auth;

class EventPollController extends Controller
{
    /**
     * Display the responses for a specific event poll.
     */
    public function index($eventId)
    {
        $event = Event::with(['poll', 'poll.options', 'poll.responses'])->findOrFail($eventId);
        
        if (!$event->poll) {
            return redirect()->route('admin.events.show', $eventId)
                ->with('error', 'This event does not have a poll.');
        }
        
        return view('admin.events.poll.index', compact('event'));
    }
    
    /**
     * Submit a response to an event poll.
     */
    public function submitResponse(Request $request, $eventId)
    {
        $event = Event::with('poll')->findOrFail($eventId);
        
        if (!$event->poll) {
            return back()->with('error', 'This event does not have a poll.');
        }
        
        $validated = $request->validate([
            'option_id' => 'required|exists:event_poll_options,id',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        $existingResponse = EventPollResponse::where([
            'poll_id' => $event->poll->id,
            'user_id' => Auth::id(),
        ])->first();
        
        if ($existingResponse) {
            $existingResponse->option_id = $validated['option_id'];
            $existingResponse->comment = $validated['comment'] ?? null;
            $existingResponse->save();
            
            $message = 'Your response has been updated.';
        } else {
            EventPollResponse::create([
                'poll_id' => $event->poll->id,
                'option_id' => $validated['option_id'],
                'user_id' => Auth::id(),
                'comment' => $validated['comment'] ?? null,
            ]);
            
            $message = 'Your response has been recorded.';
        }
        
        return back()->with('success', $message);
    }
} 