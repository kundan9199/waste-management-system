<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'pickupRequest']);

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('message', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }

        $feedbacks = $query->latest()->paginate(15);
        $avgRating = round(Feedback::avg('rating'), 1);
        $ratingDistribution = [];

        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = Feedback::where('rating', $i)->count();
        }

        return view('admin.feedbacks.index', compact('feedbacks', 'avgRating', 'ratingDistribution'));
    }

    public function togglePublish(Feedback $feedback)
    {
        $feedback->update(['is_published' => !$feedback->is_published]);
        return back()->with('success', 'Feedback visibility updated.');
    }
}
