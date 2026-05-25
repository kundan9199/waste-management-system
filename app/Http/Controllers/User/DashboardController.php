<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total'       => PickupRequest::where('user_id', $user->id)->count(),
            'pending'     => PickupRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'in_progress' => PickupRequest::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'completed'   => PickupRequest::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        $recentRequests = PickupRequest::with('wasteCategories')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentRequests'));
    }
}
