<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Classroom;
use App\Models\User;
use App\Models\Announcement;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Auth::user()->classrooms()->with('tags')->get();
        $allTags = \App\Models\Tag::orderBy('name')->get();
        return view('classrooms.index', compact('classrooms', 'allTags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array|max:5',
        ]);

        $classroom = Classroom::create([
            'name' => $request->name,
            'description' => $request->description,
            'join_code' => strtoupper(Str::random(8)),
        ]);

        $classroom->users()->attach(Auth::id(), ['role' => 'teacher']);

        if ($request->has('tags')) {
            $classroom->tags()->sync($request->tags);
        }

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Class created successfully!');
    }

    public function edit(Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);
        $allTags = \App\Models\Tag::orderBy('name')->get();
        return view('classrooms.edit', compact('classroom', 'allTags'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array|max:5',
        ]);

        $classroom->update($request->only('name', 'description'));

        if ($request->has('tags')) {
            $classroom->tags()->sync($request->tags);
        }

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Class updated successfully!');
    }

    public function join(Request $request)
    {
        $request->validate([
            'join_code' => 'required|string',
        ]);

        $joinCode = strtoupper($request->join_code);
        $classroom = Classroom::where('join_code', $joinCode)->first();

        if (!$classroom) {
            return back()->withErrors(['join_code' => 'Invalid join code.']);
        }

        // Cannot join their own class as member (if they are teacher)
        $existing = $classroom->users()->where('user_id', Auth::id())->first();
        if ($existing) {
            return back()->withErrors(['join_code' => 'You are already in this class.']);
        }

        $classroom->users()->attach(Auth::id(), ['role' => 'member']);

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Joined class successfully!');
    }

    public function show(Classroom $classroom)
    {
        // Check if user belongs to this class
        $userInClass = $classroom->users()->where('user_id', Auth::id())->first();
        if (!$userInClass) {
            abort(403);
        }

        // Update last access for participants tab tracking
        $classroom->users()->updateExistingPivot(Auth::id(), [
            'last_accessed_at' => now(),
        ]);

        $userRole = $userInClass->pivot->role;

        // Get Stream Items (Announcements/Materials/Assignments)
        $announcements = Announcement::where('classroom_id', $classroom->id)
            ->with(['author', 'comments.user', 'comments.replies.user', 'submissions'])
            ->orderBy('created_at', 'desc')
            ->get();

        $members = $classroom->users()->withPivot('role', 'notes', 'last_accessed_at')->get();
        $creator = $classroom->users()->wherePivot('role', 'teacher')->first();

        // Pass member's existing submissions to the view
        $memberSubmissions = AssignmentSubmission::where('user_id', auth()->id())
            ->whereIn('announcement_id', $announcements->pluck('id'))
            ->get()
            ->keyBy('announcement_id');

        return view('classrooms.show', compact('classroom', 'announcements', 'userRole', 'members', 'creator', 'memberSubmissions'));
    }

    public function kickMember(Classroom $classroom, User $user)
    {
        $this->authorizeTeacher($classroom);

        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot kick yourself.');
        }

        $classroom->users()->detach($user->id);

        return back()->with('success', 'Member kicked successfully.');
    }

    public function leaveClass(Classroom $classroom)
    {
        // Teachers cannot leave their own class this way (maybe they should delete it?)
        // But for now, let's just detach.
        $classroom->users()->detach(Auth::id());

        return redirect()->route('classrooms.index')->with('success', 'You have left the class.');
    }

    public function updateMember(Request $request, Classroom $classroom, User $user)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $classroom->users()->updateExistingPivot($user->id, [
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Member info updated.');
    }

    public function destroy(Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        // Delete all files associated with this classroom
        foreach ($classroom->announcements as $ann) {
            if ($ann->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($ann->file_path);
            }
            
            // Delete submission files
            foreach ($ann->submissions as $sub) {
                if ($sub->file_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($sub->file_path);
                }
            }
        }
        
        // Delete banner if exists
        if ($classroom->banner_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($classroom->banner_path);
        }
        
        $classroom->delete();

        return redirect()->route('classrooms.index')->with('success', 'Class deleted successfully.');
    }

    private function authorizeTeacher(Classroom $classroom)
    {
        $user = $classroom->users()->where('user_id', Auth::id())->first();
        if (!$user || $user->pivot->role !== 'teacher') {
            abort(403, 'Unauthorized.');
        }
    }
}
