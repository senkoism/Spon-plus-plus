<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
 
use App\Models\Material;
use App\Models\Classroom;
use App\Models\MaterialCompletion;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    private function authorizeTeacher(Classroom $classroom)
    {
        $isTeacher = $classroom->users()
            ->where('user_id', Auth::id())
            ->wherePivot('role', 'teacher')
            ->exists();
            
        if (!$isTeacher) {
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
            'section_id' => 'nullable|exists:sections,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|max:20480',
            'folder_files.*' => 'nullable|file|max:20480',
            'type_selection' => 'required|in:auto,link,assignment,folder',
            'open_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && \Carbon\Carbon::parse($value)->lt(now()->subMinutes(1))) {
                        $fail('Open Date cannot be in the past.');
                    }
                },
            ],
            'due_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && \Carbon\Carbon::parse($value)->lt(now()->subMinutes(1))) {
                        $fail('Due Date cannot be in the past.');
                    }
                    if ($value && $request->open_date && \Carbon\Carbon::parse($value)->lte(\Carbon\Carbon::parse($request->open_date))) {
                        $fail('Due Date must be after the Open Date.');
                    }
                },
            ],
        ]);

        $filePath = null;
        $type = 'document';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Check extension for drawio
            $extension = strtolower($file->getClientOriginalExtension());
            $allowed = ['pdf', 'docx', 'xlsx', 'pptx', 'png', 'jpg', 'jpeg', 'zip', 'rar', 'txt', 'drawio'];
            if (!in_array($extension, $allowed)) {
                return back()->withErrors(['file' => 'File type not allowed.']);
            }

            $filePath = $file->store('materials', 'public');
            
            if ($request->type_selection === 'auto') {
                $type = match($extension) {
                    'pdf'           => 'pdf',
                    'docx'          => 'document',
                    'xlsx'          => 'spreadsheet',
                    'pptx'          => 'presentation',
                    'png','jpg','jpeg' => 'image',
                    'zip','rar'     => 'archive',
                    'txt'           => 'txt',
                    'drawio'        => 'drawio',
                    default         => 'document',
                };
            }
        }

        if ($request->type_selection !== 'auto') {
            $type = $request->type_selection;
        } elseif (!$request->hasFile('file')) {
            if (filter_var($request->input('content'), FILTER_VALIDATE_URL)) {
                $type = 'link';
            }
        }

        $openDate = $request->input('open_date') ?: now();

        $material = $classroom->materials()->create([
            'user_id' => Auth::id(),
            'section_id' => $request->input('section_id'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'file_path' => $filePath,
            'type' => $type,
            'open_date' => $openDate,
            'due_date' => $request->input('due_date'),
        ]);

        if ($type === 'folder' && $request->hasFile('folder_files')) {
            foreach ($request->file('folder_files') as $file) {
                $path = $file->store('materials/folders', 'public');
                $material->folderFiles()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return back()->with('success', 'Announcement posted successfully!');
    }

    public function update(Request $request, Material $material)
    {
        $this->authorizeTeacher($material->classroom);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|max:20480',
            'type' => 'required|in:document,pdf,spreadsheet,presentation,image,archive,link,assignment,txt,drawio',
            'open_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && \Carbon\Carbon::parse($value)->lt(now()->subMinutes(1))) {
                        $fail('Open Date cannot be in the past.');
                    }
                },
            ],
            'due_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && \Carbon\Carbon::parse($value)->lt(now()->subMinutes(1))) {
                        $fail('Due Date cannot be in the past.');
                    }
                    if ($value && $request->open_date && \Carbon\Carbon::parse($value)->lte(\Carbon\Carbon::parse($request->open_date))) {
                        $fail('Due Date must be after the Open Date.');
                    }
                },
            ],
        ]);

        $data = [
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $request->input('type'),
            'open_date' => $request->input('open_date') ?: $material->open_date,
            'due_date' => $request->input('due_date'),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $allowed = ['pdf', 'docx', 'xlsx', 'pptx', 'png', 'jpg', 'jpeg', 'zip', 'rar', 'txt', 'drawio'];
            if (!in_array($extension, $allowed)) {
                return back()->withErrors(['file' => 'File type not allowed.']);
            }

            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $data['file_path'] = $file->store('materials', 'public');
        }

        $material->update($data);

        return back()->with('success', 'Announcement updated successfully!');
    }

    public function download(Material $material)
    {
        $this->authorizeMember($material->classroom);

        // Timed Lock Enforcement (Change 3)
        $isTeacher = $material->classroom->users()
            ->where('user_id', Auth::id())
            ->wherePivot('role', 'teacher')
            ->exists();

        if (!$isTeacher) {
            $isLocked = (
                ($material->open_date && now()->lt($material->open_date)) ||
                ($material->due_date  && now()->gt($material->due_date))
            );

            if ($isLocked) {
                return back()->withErrors(['access' => 'This announcement is no longer accessible.']);
            }
        }

        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'File not found.');
        }

        // Auto mark as done on download
        MaterialCompletion::updateOrCreate([
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
        ], [
            'is_done' => true, 
            'done_at' => now()
        ]);

        return Storage::disk('public')->download($material->file_path, $material->title . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION));
    }

    public function submitAssignment(Request $request, Material $material)
    {
        $this->authorizeMember($material->classroom);
        
        if ($material->type !== 'assignment') {
            abort(403, 'This announcement is not an assignment.');
        }

        $now = now();
        
        // Recompute lock logic server-side (Bug 1 Fix)
        $isLocked = (
            ($material->open_date && $now->lt($material->open_date)) ||
            ($material->due_date  && $now->gt($material->due_date))
        );

        if ($isLocked) {
            $message = ($material->due_date && $now->gt($material->due_date))
                ? 'Submission closed. The deadline was ' . $material->due_date->format('D, d M Y H:i') . '.'
                : 'This assignment is not open yet. It opens on ' . $material->open_date->format('D, d M Y H:i') . '.';
            return back()->withErrors(['file' => $message]);
        }

        $request->validate([
            'file' => 'required|file|max:20480|mimes:pdf,docx,xlsx,pptx,txt,zip,rar,png,jpg,jpeg',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExts = ['pdf','docx','xlsx','pptx','txt','zip','rar','png','jpg','jpeg','drawio'];
        
        if (!in_array($extension, $allowedExts)) {
            return back()->withErrors(['file' => 'File type not allowed. Accepted: PDF, DOCX, XLSX, PPTX, TXT, ZIP, RAR, PNG, JPG, JPEG, DRAWIO (max 20MB)']);
        }

        // Check if user already submitted
        $submission = AssignmentSubmission::where('material_id', $material->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($submission) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $path = $file->store('submissions', 'public');

        AssignmentSubmission::updateOrCreate([
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
        ], [
            'file_path'    => $path,
            'file_name'    => $file->getClientOriginalName(),
            'submitted_at' => now(),
        ]);

        // Auto Mark as Done
        MaterialCompletion::updateOrCreate([
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
        ], [
            'is_done' => true,
            'done_at' => now(),
        ]);

        return back()->with('success', 'Assignment submitted successfully!');
    }

    public function downloadSubmission(AssignmentSubmission $submission)
    {
        $this->authorizeTeacher($submission->material->classroom);

        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($submission->file_path, $submission->file_name);
    }

    public function toggleComplete(Material $material)
    {
        $this->authorizeMember($material->classroom);

        $completion = MaterialCompletion::where('material_id', $material->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($completion && $completion->is_done) {
            return back()->with('info', 'Announcement already marked as done.');
        }

        MaterialCompletion::updateOrCreate([
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
        ], [
            'is_done' => true,
            'done_at' => now()
        ]);

        return back()->with('success', 'Announcement marked as done!');
    }

    public function destroy(Material $material)
    {
        $this->authorizeTeacher($material->classroom);

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', 'Announcement deleted successfully!');
    }

    public function togglePin(Material $material)
    {
        $this->authorizeTeacher($material->classroom);

        $newStatus = !$material->is_pinned;

        if ($newStatus) {
            Material::where('classroom_id', $material->classroom_id)
                    ->where('id', '!=', $material->id)
                    ->update(['is_pinned' => false]);
        }

        $material->update(['is_pinned' => $newStatus]);

        $msg = $newStatus ? 'Announcement pinned to top.' : 'Announcement unpinned.';
        return back()->with('success', $msg);
    }

    public function folderDownload(Material $material)
    {
        // Access check
        $this->authorizeMember($material->classroom);
        
        if ($material->type !== 'folder') {
            abort(403, 'This is not a folder activity.');
        }

        if (!class_exists('ZipArchive')) {
            return back()->withErrors(['error' => 'ZipArchive extension is not enabled on this server.']);
        }

        $zip = new \ZipArchive();
        $zipName = \Illuminate\Support\Str::slug($material->title) . '.zip';
        $zipPath = storage_path('app/temp/' . $zipName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['error' => 'Could not create ZIP file.']);
        }

        if ($material->folderFiles->isEmpty()) {
            $zip->addFromString('empty.txt', 'This folder is empty.');
        } else {
            foreach ($material->folderFiles as $file) {
                $fullPath = storage_path('app/public/' . $file->file_path);
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, $file->file_name);
                }
            }
        }
        
        $zip->close();

        if (!file_exists($zipPath)) {
            return back()->withErrors(['error' => 'ZIP file generation failed.']);
        }

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }
}
