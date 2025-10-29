<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPollResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollResponseController extends Controller
{
    /**
     * Show poll responses and analytics for an event
     *
     * @param int $eventId
     * @return \Illuminate\View\View
     */
    public function showResponses($eventId)
    {
        // Get the event with its poll
        $event = Event::with('poll')->findOrFail($eventId);

        // Check if event has a poll
        if (!$event->poll) {
            return view('admin.responses', [
                'event' => $event,
                'groupedResponses' => collect(),
                'analytics' => [
                    'total_responses' => 0,
                    'attending' => 0,
                    'maybe' => 0,
                    'not_attending' => 0
                ],
                'responseTrends' => collect()
            ]);
        }

        // Get all responses for this poll
        $responses = EventPollResponse::where('poll_id', $event->poll->id)
            ->with(['user', 'option'])
            ->get();

        // Group responses by option
        $groupedResponses = $responses->groupBy('option_id')
            ->map(function ($responses, $optionId) {
                if ($responses->isEmpty()) {
                    return null;
                }
                return [
                    'option' => $responses->first()->option,
                    'responses' => $responses
                ];
            })
            ->filter(); // Remove null values

        // Calculate analytics with null checks
        $analytics = [
            'total_responses' => $responses->count(),
            'attending' => $responses->where('option.option_value', 'attending')->count(),
            'maybe' => $responses->where('option.option_value', 'maybe')->count(),
            'not_attending' => $responses->where('option.option_value', 'not_attending')->count(),
        ];

        // Get response trends over time
        $responseTrends = EventPollResponse::where('poll_id', $event->poll->id)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.responses', compact('event', 'groupedResponses', 'analytics', 'responseTrends'));
    }

    /**
     * Export poll responses to CSV
     *
     * @param int $eventId
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportResponses($eventId)
    {
        $event = Event::with('poll')->findOrFail($eventId);
        
        // Check if event has a poll
        if (!$event->poll) {
            return response()->json(['error' => 'No poll found for this event'], 404);
        }

        $responses = EventPollResponse::where('poll_id', $event->poll->id)
            ->with(['user', 'option'])
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="poll_responses_' . $event->title . '.csv"',
        ];

        $callback = function() use ($responses) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Name', 'Response', 'Comment', 'Date']);
            
            // Add data
            foreach ($responses as $response) {
                fputcsv($file, [
                    $response->user->name ?? 'Unknown User',
                    $response->option->option_text ?? 'Unknown Option',
                    $response->comment ?? 'N/A',
                    $response->created_at->format('M d, Y g:i A')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 