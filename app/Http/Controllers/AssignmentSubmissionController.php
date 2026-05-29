<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\MaterialCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    public function destroy(AssignmentSubmission $submission)
    {
        // Ensure the authenticated user owns this submission
        if ($submission->user_id !== Auth::id()) {
            abort(403);
        }

        // Lock check (Change 7 - only delete if open)
        $material = $submission->material;
        $isLocked = (
            ($material->open_date && now()->lt($material->open_date)) ||
            ($material->due_date  && now()->gt($material->due_date))
        );

        if ($isLocked) {
            return back()->withErrors(['file' => 'Submission window is closed. You can no longer delete your submission.']);
        }

        // Delete the file from storage
        Storage::disk('public')->delete($submission->file_path);

        // Delete the DB record
        $submission->delete();

        // Also un-mark as done in material_completions
        MaterialCompletion::where('material_id', $submission->material_id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Your submission has been deleted.');
    }
}
