<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Models\User;
use App\Models\Complaint;
use App\Models\Feedback;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_requests'  => PickupRequest::count(),
            'in_progress'     => PickupRequest::where('status', 'in_progress')->count(),
            'completed'       => PickupRequest::where('status', 'completed')->count(),
            'open_complaints' => Complaint::where('status', 'open')->count(),
        ];

        $recentRequests = PickupRequest::with(['user', 'wasteCategories'])
            ->latest()
            ->take(10)
            ->get();

        $recentComplaints = Complaint::with('user')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentRequests', 'recentComplaints'));
    }
}
