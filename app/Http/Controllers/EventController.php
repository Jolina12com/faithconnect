<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventPoll;
use App\Models\EventPollOption;
use App\Models\EventPollResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        $eventType = $request->get('type', 'all');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        $query = Event::with('poll');
        
        // Filter by event type if specified
        if ($eventType !== 'all') {
            $query->where('event_type', $eventType);
        }
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // Apply date range filters if provided
        if ($dateFrom) {
            $query->whereDate('event_date', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('event_date', '<=', $dateTo);
        }
        
        // Apply sorting
        $sort = $request->get('sort', 'date_desc');
        switch ($sort) {
            case 'date_asc':
                $query->orderBy('event_date', 'asc')->orderBy('event_time', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'date_desc':
            default:
                $query->orderBy('event_date', 'desc')->orderBy('event_time', 'desc');
                break;
        }
        
        $events = $query->paginate(10);
        
        // Pass the filter values back to the view
        return view('admin.events.index', compact(
            'events', 
            'eventType',
            'search',
            'dateFrom',
            'dateTo',
            'sort'
        ));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(Request $request)
    {
        $eventType = $request->get('type', 'regular');
        $users = null;
        
        // If creating a wedding or baptism, load users for dropdown
        if ($eventType === 'wedding' || $eventType === 'baptism') {
            $users = \App\Models\User::orderBy('first_name', 'asc')->get();
        }
        
        return view('admin.events.create', compact('eventType', 'users'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        // Basic validation for all event types
        $baseValidation = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_color' => 'nullable|string|max:30',
            'event_type' => 'required|in:regular,wedding,baptism',
        ];
        
        // Get current user
        $user = auth()->user();
        
        // Additional validation rules based on event type
        $eventType = $request->input('event_type', 'regular');
        
        // Wedding-specific validation
        if ($eventType === 'wedding') {
            $baseValidation = array_merge($baseValidation, [
                'groom_id' => 'nullable|exists:users,id',
                'bride_id' => 'nullable|exists:users,id',
                'groom_name' => 'required|string|max:255',
                'bride_name' => 'required|string|max:255',
                'officiating_minister' => 'nullable|string|max:255',
                'witnesses' => 'nullable|string',
                'status' => 'required|in:scheduled,completed,cancelled',
                'notes' => 'nullable|string',
            ]);
        }
        
        // Baptism-specific validation
        if ($eventType === 'baptism') {
            $baseValidation = array_merge($baseValidation, [
                'person_id' => 'nullable|exists:users,id',
                'person_name' => 'required|string|max:255',
                'birth_date' => 'nullable|date',
                'officiating_minister' => 'nullable|string|max:255',
                'godparents' => 'nullable|string',
                'parents' => 'nullable|string',
                'is_child' => 'nullable|boolean',
                'status' => 'required|in:scheduled,completed,cancelled',
                'notes' => 'nullable|string',
            ]);
        }
        
        // Poll-related validation
        if ($request->has('enable_poll')) {
            $baseValidation = array_merge($baseValidation, [
                'enable_poll' => 'nullable|boolean',
                'poll_options' => 'nullable|array',
                'poll_options.*' => 'string',
                'custom_options' => 'nullable|array',
                'custom_options.*' => 'string',
                'poll_deadline' => 'nullable|date|before_or_equal:event_date',
                'allow_comments' => 'nullable|boolean',
                'notify_responses' => 'nullable|boolean',
            ]);
        }
        
        $validated = $request->validate($baseValidation);

        DB::beginTransaction();
        
        try {
            // Create the event
            $event = new Event();
            $event->title = $validated['title'];
            $event->event_type = $eventType;
            $event->description = $validated['description'];
            $event->event_date = $validated['event_date'];
            $event->event_time = $validated['event_time'] ?? null;
            $event->location = $validated['location'];
            $event->color = $validated['event_color'] ?? $this->getDefaultColorByType($eventType);
            
            // Set wedding-specific fields
            if ($eventType === 'wedding') {
                $event->groom_id = $validated['groom_id'] ?? null;
                $event->bride_id = $validated['bride_id'] ?? null;
                $event->groom_name = $validated['groom_name'];
                $event->bride_name = $validated['bride_name'];
                $event->officiating_minister = $validated['officiating_minister'] ?? null;
                $event->witnesses = $validated['witnesses'] ?? null;
                $event->status = $validated['status'];
                $event->notes = $validated['notes'] ?? null;
            }
            
            // Set baptism-specific fields
            if ($eventType === 'baptism') {
                $event->person_id = $validated['person_id'] ?? null;
                $event->person_name = $validated['person_name'];
                $event->birth_date = $validated['birth_date'] ?? null;
                $event->officiating_minister = $validated['officiating_minister'] ?? null;
                $event->godparents = $validated['godparents'] ?? null;
                $event->parents = $validated['parents'] ?? null;
                $event->is_child = $request->has('is_child') ? 1 : 0;
                $event->status = $validated['status'];
                $event->notes = $validated['notes'] ?? null;
            }
            
            $event->save();

            // Create poll if enabled
            if ($request->has('enable_poll') && $request->enable_poll == 1) {
                $poll = new EventPoll();
                $poll->event_id = $event->id;
                $poll->deadline = $request->poll_deadline;
                $poll->allow_comments = $request->has('allow_comments') ? 1 : 0;
                $poll->notify_responses = $request->has('notify_responses') ? 1 : 0;
                $poll->save();

                // Add default poll options if selected
                $pollOptions = $request->poll_options ?? [];
                foreach ($pollOptions as $optionValue) {
                    $option = new EventPollOption();
                    $option->poll_id = $poll->id;
                    $option->option_text = $this->getPollOptionText($optionValue);
                    $option->option_value = $optionValue;
                    $option->is_default = true;
                    $option->save();
                }

                // Add custom poll options if provided
                if ($request->has('custom_options') && is_array($request->custom_options)) {
                    foreach ($request->custom_options as $customText) {
                        if (!empty(trim($customText))) {
                            $option = new EventPollOption();
                            $option->poll_id = $poll->id;
                            $option->option_text = $customText;
                            $option->option_value = Str::slug($customText);
                            $option->is_default = false;
                            $option->save();
                        }
                    }
                }
            }

            // Send notifications to all users except the admin
            $users = \App\Models\User::where('id', '!=', $user->id)->get();
            foreach ($users as $recipient) {
                $recipient->notify(new \App\Notifications\EventNotification($event));
            }

            // Broadcast the event
            event(new \App\Events\NewEventPosted($event));

            DB::commit();

            return redirect()->route('admin.events.index', ['type' => $eventType])
                ->with('success', ucfirst($eventType) . ' created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating event: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified event.
     */
    public function show($id)
    {
        $event = Event::with(['poll', 'poll.options', 'poll.responses'])->findOrFail($id);
        
        // Get poll statistics if poll exists
        $pollStats = null;
        if ($event->poll) {
            $pollStats = [
                'total_responses' => $event->poll->responses->count(),
                'option_counts' => $event->poll->getOptionCounts(),
                'response_rate' => $event->poll->getResponseRate()
            ];
        }
        
        return view('admin.events.show', compact('event', 'pollStats'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit($id)
    {
        $event = Event::with(['poll', 'poll.options'])->findOrFail($id);
        $users = null;
        
        // If editing a wedding or baptism, load users for dropdown
        if ($event->event_type === 'wedding' || $event->event_type === 'baptism') {
            $users = \App\Models\User::orderBy('first_name', 'asc')->get();
        }
        
        return view('admin.events.edit', compact('event', 'users'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $eventType = $event->event_type;

        // Basic validation for all event types
        $baseValidation = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable|string',
            'location' => 'required|string|max:255',
            'event_color' => 'nullable|string|max:30',
        ];
        
        // Wedding-specific validation
        if ($eventType === 'wedding') {
            $baseValidation = array_merge($baseValidation, [
                'groom_id' => 'nullable|exists:users,id',
                'bride_id' => 'nullable|exists:users,id',
                'groom_name' => 'required|string|max:255',
                'bride_name' => 'required|string|max:255',
                'officiating_minister' => 'nullable|string|max:255',
                'witnesses' => 'nullable|string',
                'status' => 'required|in:scheduled,completed,cancelled',
                'notes' => 'nullable|string',
            ]);
        }
        
        // Baptism-specific validation
        if ($eventType === 'baptism') {
            $baseValidation = array_merge($baseValidation, [
                'person_id' => 'nullable|exists:users,id',
                'person_name' => 'required|string|max:255',
                'birth_date' => 'nullable|date',
                'officiating_minister' => 'nullable|string|max:255',
                'godparents' => 'nullable|string',
                'parents' => 'nullable|string',
                'is_child' => 'nullable|boolean',
                'status' => 'required|in:scheduled,completed,cancelled',
                'notes' => 'nullable|string',
            ]);
        }
        
        // Poll-related validation
        $baseValidation = array_merge($baseValidation, [
            'enable_poll' => 'nullable|boolean',
            'poll_options' => 'nullable|array',
            'poll_options.*' => 'string',
            'custom_options' => 'nullable|array',
            'custom_options.*' => 'string',
            'poll_deadline' => 'nullable|date|before_or_equal:event_date',
            'allow_comments' => 'nullable|boolean',
            'notify_responses' => 'nullable|boolean',
        ]);

        $validated = $request->validate($baseValidation);

        DB::beginTransaction();
        
        try {
            // Update event details
            $event->title = $validated['title'];
            $event->description = $validated['description'];
            $event->event_date = $validated['event_date'];
            $event->event_time = $validated['event_time'] ?? null;
            $event->location = $validated['location'];
            $event->color = $validated['event_color'] ?? $this->getDefaultColorByType($eventType);
            
            // Update wedding-specific fields
            if ($eventType === 'wedding') {
                $event->groom_id = $validated['groom_id'] ?? null;
                $event->bride_id = $validated['bride_id'] ?? null;
                $event->groom_name = $validated['groom_name'];
                $event->bride_name = $validated['bride_name'];
                $event->officiating_minister = $validated['officiating_minister'] ?? null;
                $event->witnesses = $validated['witnesses'] ?? null;
                $event->status = $validated['status'];
                $event->notes = $validated['notes'] ?? null;
            }
            
            // Update baptism-specific fields
            if ($eventType === 'baptism') {
                $event->person_id = $validated['person_id'] ?? null;
                $event->person_name = $validated['person_name'];
                $event->birth_date = $validated['birth_date'] ?? null;
                $event->officiating_minister = $validated['officiating_minister'] ?? null;
                $event->godparents = $validated['godparents'] ?? null;
                $event->parents = $validated['parents'] ?? null;
                $event->is_child = $request->has('is_child') ? 1 : 0;
                $event->status = $validated['status'];
                $event->notes = $validated['notes'] ?? null;
            }
            
            $event->save();

            // Handle poll
            if ($request->has('enable_poll') && $request->enable_poll == 1) {
                // Create or update poll
                $poll = EventPoll::firstOrNew(['event_id' => $event->id]);
                $poll->deadline = $request->poll_deadline;
                $poll->allow_comments = $request->has('allow_comments') ? 1 : 0;
                $poll->notify_responses = $request->has('notify_responses') ? 1 : 0;
                $poll->save();

                // Remove existing options and recreate them
                EventPollOption::where('poll_id', $poll->id)->delete();

                // Add default poll options if selected
                $pollOptions = $request->poll_options ?? [];
                foreach ($pollOptions as $optionValue) {
                    $option = new EventPollOption();
                    $option->poll_id = $poll->id;
                    $option->option_text = $this->getPollOptionText($optionValue);
                    $option->option_value = $optionValue;
                    $option->is_default = true;
                    $option->save();
                }

                // Add custom poll options if provided
                if ($request->has('custom_options') && is_array($request->custom_options)) {
                    foreach ($request->custom_options as $customText) {
                        if (!empty(trim($customText))) {
                            $option = new EventPollOption();
                            $option->poll_id = $poll->id;
                            $option->option_text = $customText;
                            $option->option_value = Str::slug($customText);
                            $option->is_default = false;
                            $option->save();
                        }
                    }
                }
            } else {
                // If poll is disabled, remove existing poll
                $poll = EventPoll::where('event_id', $event->id)->first();
                if ($poll) {
                    // Delete associated options and responses
                    EventPollOption::where('poll_id', $poll->id)->delete();
                    EventPollResponse::where('poll_id', $poll->id)->delete();
                    $poll->delete();
                }
            }

            DB::commit();

            return redirect()->route('admin.events.index', ['type' => $eventType])
                ->with('success', ucfirst($eventType) . ' updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating event: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        
        // The poll and its options/responses should be deleted automatically 
        // if you've set up cascading deletes in your migrations
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully');
    }

    /**
     * Submit a response to an event poll.
     */
    public function submitPollResponse(Request $request, $eventId)
    {
        $event = Event::with('poll')->findOrFail($eventId);
        
        if (!$event->poll) {
            return back()->with('error', 'This event does not have an active poll.');
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
        
        // Send notification if enabled
        if ($event->poll->notify_responses) {
            // Implement notification logic here or queue a notification job
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Get a formatted option text based on predefined option values.
     */
    private function getPollOptionText($optionValue)
    {
        switch ($optionValue) {
            case 'attending':
                return 'Attending';
            case 'maybe':
                return 'Maybe';
            case 'not_attending':
                return 'Not Attending';
            default:
                return ucfirst(str_replace('_', ' ', $optionValue));
        }
    }

    /**
     * Get event analytics data for dashboard
     */
    public function getEventAnalytics()
    {
        // Get total events count
        $totalEvents = Event::count();
        
        // Get upcoming events (next 30 days)
        $upcomingEvents = Event::where('event_date', '>=', now())
            ->where('event_date', '<=', now()->addDays(30))
            ->count();
        
        // Get events by month (for the next 6 months)
        $sixMonthsForward = now()->addMonths(6);
        $monthlyEvents = Event::select(
                DB::raw('YEAR(event_date) as year'),
                DB::raw('MONTH(event_date) as month'),
                DB::raw('count(*) as count')
            )
            ->where('event_date', '>=', now())
            ->where('event_date', '<=', $sixMonthsForward)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        // Format the monthly data for charts
        $monthlyData = [];
        foreach ($monthlyEvents as $data) {
            $monthName = date('M', mktime(0, 0, 0, $data->month, 1));
            $monthlyData[$monthName] = $data->count;
        }
        
        // Get events with polls
        $eventsWithPolls = Event::has('poll')->count();
        
        // Get upcoming events list (next 5) for the sidebar/list display
        $nextEvents = Event::with('poll')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();
        
        // Get all events for the current and next month for the calendar
        $startOfCurrentMonth = now()->startOfMonth();
        $endOfNextMonth = now()->addMonth()->endOfMonth();
        
        $calendarEvents = Event::where('event_date', '>=', $startOfCurrentMonth)
            ->where('event_date', '<=', $endOfNextMonth)
            ->orderBy('event_date', 'asc')
            ->get();
            
        // Ensure dates are formatted consistently
        $calendarEvents = $calendarEvents->map(function($event) {
            // Make sure event_date is formatted consistently for the frontend
            if ($event->event_date) {
                $event->event_date = date('Y-m-d', strtotime($event->event_date));
            }
            return $event;
        });
        
        return response()->json([
            'totalEvents' => $totalEvents,
            'upcomingEvents' => $upcomingEvents,
            'monthlyEvents' => $monthlyData,
            'eventsWithPolls' => $eventsWithPolls,
            'nextEvents' => $nextEvents,
            'calendarEvents' => $calendarEvents
        ]);
    }

    /**
     * Get events for a specific date range (for calendar)
     */
    public function getEventsForRange(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);
        
        $events = Event::whereBetween('event_date', [$request->start, $request->end])
            ->orderBy('event_date', 'asc')
            ->get();
            
        return response()->json($events);
    }

    /**
     * Get default color based on event type
     */
    private function getDefaultColorByType($eventType)
    {
        switch ($eventType) {
            case 'wedding':
                return '#E91E63'; // Pink
            case 'baptism':
                return '#2196F3'; // Blue
            default:
                return '#3788d8'; // Default blue
        }
    }
}