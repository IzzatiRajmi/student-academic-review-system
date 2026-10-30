<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewRequest;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'stats' => ReviewRequest::stats(),
            'requests' => ReviewRequest::pending()->with('student')->oldest()->oldest('id')->take(5)->get(),
        ]);
    }
}
