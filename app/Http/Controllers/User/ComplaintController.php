<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\PickupRequest;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.complaints.index', compact('complaints'));
    }

    public function create()
    {
        $requests = PickupRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.complaints.create', compact('requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_request_id' => 'nullable',
            'subject'           => 'required|string',
            'description'       => 'required|string',
        ]);

        Complaint::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status'  => 'open',
        ]);

        return redirect()->route('user.complaints.index')
            ->with('success', 'Your complaint has been submitted. We will review it shortly.');
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.complaints.show', compact('complaint'));
    }
}
