<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\AnnouncementComment;
use Illuminate\Support\Facades\Auth;

class AnnouncementCommentController extends Controller
{
    public function store(Request $request, Announcement $announcement)
    {
        $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:announcement_comments,id'
        ]);

        // Sanitize body: allow <b><i><u><ul><li>
        $sanitizedBody = strip_tags($request->body, '<b><i><u><ul><li>');

        if (empty(trim(strip_tags($sanitizedBody)))) {
             // If after stripping all tags it's empty, but maybe they just have tags?
             // Actually strip_tags might leave empty if they only put <b></b>.
             // But we should allow it if there is content.
        }

        AnnouncementComment::create([
            'announcement_id' => $announcement->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'body' => $sanitizedBody,
        ]);

        return back()->with('success', 'Comment posted.');
    }

    public function update(Request $request, AnnouncementComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string'
        ]);

        $sanitizedBody = strip_tags($request->body, '<b><i><u><ul><li>');

        $comment->update([
            'body' => $sanitizedBody
        ]);

        return back()->with('success', 'Comment updated.');
    }

    public function destroy(AnnouncementComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
