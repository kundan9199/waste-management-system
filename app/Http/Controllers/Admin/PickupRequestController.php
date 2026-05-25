<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestStatusUpdated;

class PickupRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PickupRequest::with(['user', 'wasteCategories']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->whereHas('wasteCategories', function($q) use ($request) {
                $q->where('waste_categories.id', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('request_number', 'like', "%{$request->search}%")
                  ->orWhere('pickup_location', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('pickup_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pickup_date', '<=', $request->date_to);
        }

        $requests    = $query->latest()->paginate(15);
        $categories  = WasteCategory::all();
        $statusCounts = [
            'all'         => PickupRequest::count(),
            'pending'     => PickupRequest::where('status', 'pending')->count(),
            'in_progress' => PickupRequest::where('status', 'in_progress')->count(),
            'completed'   => PickupRequest::where('status', 'completed')->count(),
            'cancelled'   => PickupRequest::where('status', 'cancelled')->count(),
        ];

        return view('admin.requests.index', compact('requests', 'categories', 'statusCounts'));
    }

    public function show(PickupRequest $pickupRequest)
    {
        $pickupRequest->load(['user', 'wasteCategories', 'complaints', 'feedbacks.user']);
        return view('admin.requests.show', compact('pickupRequest'));
    }

    public function updateStatus(Request $request, PickupRequest $pickupRequest)
    {
        $validated = $request->validate([
            'status'      => 'required|string',
            'admin_notes' => 'nullable|string',
        ]);

        $data = [
            'status'      => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $pickupRequest->admin_notes,
        ];

        if ($validated['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $pickupRequest->update($data);

        // Send email notification
        try {
            \Mail::to($pickupRequest->user->email)->send(
                new \App\Mail\RequestStatusUpdated($pickupRequest)
            );
        } catch (\Exception $e) {
            // Log but don't fail
            \Log::error('Email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Request status updated successfully.');
    }

    public function destroy(PickupRequest $pickupRequest)
    {
        if ($pickupRequest->waste_image) {
            \Storage::disk('public')->delete($pickupRequest->waste_image);
        }
        $pickupRequest->delete();

        return redirect()->route('admin.requests.index')
            ->with('success', 'Request deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $query = PickupRequest::with(['user', 'wasteCategories']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->get();

        $pdf = Pdf::loadView('admin.requests.pdf', compact('requests'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('pickup-requests-' . now()->format('Y-m-d') . '.pdf');
    }
}
