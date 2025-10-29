<?php

namespace App\Http\Controllers;

use App\Events\NewLivestreamComment;
use App\Events\NewLivestreamReaction;
use App\Models\LivestreamComment;
use App\Models\LivestreamReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestreamInteractionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getComments(Request $request)
    {
        $request->validate([
            'room_name' => 'required|string',
        ]);

        $comments = LivestreamComment::with('user')
            ->where('room_name', $request->room_name)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json($comments);
    }

    public function postComment(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'room_name' => 'required|string',
        ]);

        $comment = LivestreamComment::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'room_name' => $request->room_name,
        ]);

        $comment->load('user');
        broadcast(new NewLivestreamComment($comment))->toOthers();

        return response()->json($comment);
    }

    public function getReactions(Request $request)
    {
        $request->validate([
            'room_name' => 'required|string',
        ]);

        $reactions = LivestreamReaction::with('user')
            ->where('room_name', $request->room_name)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json($reactions);
    }

    public function postReaction(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:👍,❤️,😂,👏,🔥',
            'room_name' => 'required|string',
        ]);

        $reaction = LivestreamReaction::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'room_name' => $request->room_name,
        ]);

        $reaction->load('user');
        broadcast(new NewLivestreamReaction($reaction))->toOthers();

        return response()->json($reaction);
    }
}
