<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Section A — Pending Assignments (limited to 5)
        $pendingAssignments = Announcement::where('type', 'assignment')
            ->whereHas('classroom.members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            // No longer filtering by visibility (feature removed)
            ->whereNotIn('id', function($q) use ($user) {
                $q->select('announcement_id')
                    ->from('assignment_submissions')
                    ->where('user_id', $user->id);
            })
            ->where(function($q) {
                $q->whereNull('due_date')->orWhere('due_date', '>', now());
            })
            ->with(['classroom', 'author'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Section B — Recently Accessed Classrooms (limit 9)
        $recentClassrooms = $user->classrooms()
            ->orderByPivot('last_accessed_at', 'desc')
            ->take(9)
            ->get();

        return view('dashboard.index', compact('pendingAssignments', 'recentClassrooms'));
    }
}
