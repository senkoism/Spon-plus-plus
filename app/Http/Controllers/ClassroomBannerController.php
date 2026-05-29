<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClassroomBannerController extends Controller
{
    /**
     * Authorize that the current user is a teacher for the classroom.
     */
    protected function authorizeTeacher(Classroom $classroom)
    {
        $role = $classroom->users()
            ->where('user_id', Auth::id())
            ->first()
            ->pivot->role ?? null;

        if ($role !== 'teacher') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function update(Request $request, Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'banner' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);

        if ($classroom->banner_path) {
            Storage::disk('public')->delete($classroom->banner_path);
        }

        $path = $request->file('banner')->store('banners', 'public');
        $classroom->update(['banner_path' => $path]);

        return back()->with('success', 'Banner updated.');
    }

    public function destroy(Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        if ($classroom->banner_path) {
            Storage::disk('public')->delete($classroom->banner_path);
        }

        $classroom->update(['banner_path' => null]);

        return back()->with('success', 'Banner removed.');
    }
}
