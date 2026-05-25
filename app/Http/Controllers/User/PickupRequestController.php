<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PickupRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PickupRequest::with('wasteCategories')
            ->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('request_number', 'like', "%{$request->search}%")
                  ->orWhere('address', 'like', "%{$request->search}%")
                  ->orWhere('pickup_location', 'like', "%{$request->search}%")
                  ->orWhere('block', 'like', "%{$request->search}%");
            });
        }

        $requests = $query->latest()->paginate(10);

        return view('user.requests.index', compact('requests'));
    }

    public function create()
    {
        $categories = WasteCategory::where('is_active', true)->get();
        return view('user.requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'waste_category_ids' => 'required|array',
            'address'            => 'required|string',
            'pickup_location'    => 'required|string',
            'block'              => 'required|string',
            'pickup_date'        => 'required|date',
            'pickup_time'        => 'nullable',
            'waste_image'        => 'nullable|image',
        ]);

        $imagePath = null;
        if ($request->hasFile('waste_image')) {
            $imagePath = $request->file('waste_image')->store('waste-images', 'public');
        }

        $pickupRequest = PickupRequest::create([
            'address'         => $validated['address'],
            'pickup_location' => $validated['pickup_location'],
            'block'           => $validated['block'],
            'pickup_date'     => $validated['pickup_date'],
            'pickup_time'     => $validated['pickup_time'],
            'user_id'         => auth()->id(),
            'waste_image'     => $imagePath,
            'status'          => 'pending',
        ]);

        $pickupRequest->wasteCategories()->attach($validated['waste_category_ids']);

        return redirect()->route('user.requests.index')
            ->with('success', 'Pickup request submitted successfully! We will contact you soon.');
    }

    public function show(PickupRequest $pickupRequest)
    {
        if ($pickupRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $pickupRequest->load(['wasteCategories', 'feedbacks', 'complaints']);
        return view('user.requests.show', compact('pickupRequest'));
    }
}
