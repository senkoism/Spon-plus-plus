<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    private function authorizeTeacher(Classroom $classroom)
    {
        $user = $classroom->users()->where('user_id', Auth::id())->first();
        if (!$user || $user->pivot->role !== 'teacher') {
            abort(403, 'Only teachers can perform this action.');
        }
    }

    private function authorizeMember(Classroom $classroom)
    {
        if (!$classroom->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'You are not a member of this class.');
        }
    }

    public function store(Request $request, Classroom $classroom)
    {
        $this->authorizeTeacher($classroom);

        $request->validate([
            'type'        => 'required|in:announcement,material,assignment',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'open_date'   => 'nullable|date|after_or_equal:now',
            'due_date'    => 'nullable|date|after_or_equal:now',
            'file'        => 'nullable|file|max:20480|mimes:pdf,docx,xlsx,pptx,txt,zip,rar,png,jpg,jpeg,drawio',
        ]);

        $filePath = null;
        $originalName = null;
        $fileType = $request->type; // default type = post type

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('class-files', 'public');
            $originalName = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());

            $fileType = match($ext) {
                'pdf'              => 'pdf',
                'docx'             => 'document',
                'xlsx'             => 'spreadsheet',
                'pptx'             => 'presentation',
                'png','jpg','jpeg' => 'image',
                'zip','rar'        => 'archive',
                'txt'              => 'txt',
                'drawio'           => 'drawio',
                default            => 'document',
            };
        }

        $announcement = Announcement::create([
            'classroom_id'      => $classroom->id,
            'user_id'           => auth()->id(),
            'type'              => $request->type,
            'title'             => $request->title,
            'description'       => $request->description,
            'file_path'         => $filePath,
            'original_filename' => $originalName,
            'file_type'         => $fileType,
            'open_date'         => $request->open_date,
            'due_date'          => $request->due_date,
            'order'             => 0,
        ]);

        // Shift others down
        $classroom->announcements()->where('id', '!=', $announcement->id)->increment('order');

        return back()->with('success', ucfirst($request->type) . ' posted successfully.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeTeacher($announcement->classroom);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'open_date'   => 'nullable|date',
            'due_date'    => 'nullable|date',
            'file'        => 'nullable|file|max:20480|mimes:pdf,docx,xlsx,pptx,txt,zip,rar,png,jpg,jpeg,drawio',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'open_date'   => $request->open_date ?: $announcement->open_date,
            'due_date'    => $request->due_date,
        ];

        if ($request->hasFile('file')) {
            if ($announcement->file_path) {
                Storage::disk('public')->delete($announcement->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('class-files', 'public');
            $data['original_filename'] = $file->getClientOriginalName();
            
            $ext = strtolower($file->getClientOriginalExtension());
            $data['file_type'] = match($ext) {
                'pdf'              => 'pdf',
                'docx'             => 'document',
                'xlsx'             => 'spreadsheet',
                'pptx'             => 'presentation',
                'png','jpg','jpeg' => 'image',
                'zip','rar'        => 'archive',
                'txt'              => 'txt',
                'drawio'           => 'drawio',
                default            => 'document',
            };
        }

        $announcement->update($data);

        return back()->with('success', 'Stream item updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorizeTeacher($announcement->classroom);
        
        if ($announcement->file_path) {
            Storage::disk('public')->delete($announcement->file_path);
        }

        $announcement->delete();
        return back()->with('success', 'Item removed from stream.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:announcements,id',
            'order.*.order' => 'required|integer',
        ]);

        foreach ($request->order as $item) {
            $announcement = Announcement::find($item['id']);
            if ($announcement) {
                $this->authorizeTeacher($announcement->classroom);
                $announcement->update(['order' => $item['order']]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function download(Announcement $announcement)
    {
        $classroom = $announcement->classroom;
        if (!$classroom->users()->where('user_id', Auth::id())->exists()) {
             abort(403);
        }

        if (!$announcement->file_path || !Storage::disk('public')->exists($announcement->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($announcement->file_path, $announcement->original_filename);
    }

    public function submitAssignment(Request $request, Announcement $announcement)
    {
        $this->authorizeMember($announcement->classroom);
        
        if ($announcement->type !== 'assignment') {
            abort(403, 'This is not an assignment.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,docx,xlsx,pptx,txt,zip,rar,png,jpg,jpeg,drawio|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('submissions', 'public');

        \App\Models\AssignmentSubmission::updateOrCreate([
            'announcement_id' => $announcement->id,
            'user_id'     => Auth::id(),
        ], [
            'file_path'    => $path,
            'file_name'    => $file->getClientOriginalName(),
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Assignment submitted!');
    }
}
