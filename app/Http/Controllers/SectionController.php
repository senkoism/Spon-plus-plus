<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function store(Request $request, Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $classroom->sections()->create([
            'title' => $request->title,
            'order' => $classroom->sections()->count() + 1,
        ]);

        return back()->with('success', 'Section created successfully.');
    }

    public function update(Request $request, Classroom $classroom, Section $section)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section->update([
            'title' => $request->title,
        ]);

        return back()->with('success', 'Section updated successfully.');
    }

    public function destroy(Classroom $classroom, Section $section)
    {
        $this->authorizeTeacher($classroom);

        $section->delete();

        return back()->with('success', 'Section deleted successfully. Materials are now unsectioned.');
    }

    private function authorizeTeacher(Classroom $classroom)
    {
        $user = $classroom->users()->where('user_id', Auth::id())->first();
        if (!$user || $user->pivot->role !== 'teacher') {
            abort(403);
        }
    }
}
