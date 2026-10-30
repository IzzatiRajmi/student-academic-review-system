<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return view('student.dashboard', [
            'stats' => ReviewRequest::stats($user->id),
            'requests' => $user->reviewRequests()->with('student')->latest()->latest('id')->take(5)->get(),
        ]);
    }
}
