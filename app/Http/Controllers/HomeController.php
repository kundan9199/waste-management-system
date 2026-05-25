<?php

namespace App\Http\Controllers;

use App\Models\PickupRequest;
use App\Models\WasteCategory;
use App\Models\Complaint;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_requests' => PickupRequest::count(),
            'completed'      => PickupRequest::where('status', 'completed')->count(),
            'pending'        => PickupRequest::where('status', 'pending')->count(),
            'users'          => User::where('role', 'user')->count(),
        ];

        $categories = WasteCategory::where('is_active', true)->get();
        $feedbacks   = Feedback::with('user')->where('is_published', true)->latest()->take(6)->get();

        return view('welcome', compact('stats', 'categories', 'feedbacks'));
    }
}
