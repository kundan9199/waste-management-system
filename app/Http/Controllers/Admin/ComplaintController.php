<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['user', 'pickupRequest']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }

        $complaints = $query->latest()->paginate(15);

        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'pickupRequest.wasteCategories']);
        return view('admin.complaints.show', compact('complaint'));
    }

    public function respond(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status'         => 'required|string',
            'admin_response' => 'required|string',
        ]);

        $data = [
            'status'         => $validated['status'],
            'admin_response' => $validated['admin_response'],
        ];

        if ($validated['status'] === 'resolved') {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);

        return back()->with('success', 'Response submitted successfully.');
    }
}
