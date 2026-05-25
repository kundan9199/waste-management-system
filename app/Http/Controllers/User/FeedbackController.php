<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\PickupRequest;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.feedbacks.index', compact('feedbacks'));
    }

    public function create()
    {
        $completedRequests = PickupRequest::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        return view('user.feedbacks.create', compact('completedRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_request_id' => 'nullable',
            'rating'            => 'required|integer',
            'title'             => 'nullable|string',
            'message'           => 'required|string',
        ]);

        Feedback::create([
            ...$validated,
            'user_id'      => auth()->id(),
            'is_published' => true,
        ]);

        return redirect()->route('user.feedbacks.index')
            ->with('success', 'Thank you for your feedback!');
    }
}
