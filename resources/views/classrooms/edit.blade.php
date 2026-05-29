@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <nav class="breadcrumb-nav">
            <a href="{{ route('classrooms.index') }}">My Classrooms</a>
            <span class="breadcrumb-sep">/</span>
            <a href="{{ route('classrooms.show', $classroom) }}">{{ $classroom->name }}</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Settings</span>
        </nav>
        <h2 class="fw-bold">Classroom Settings</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4 border-0 shadow-sm rounded-4">
        <form action="{{ route('classrooms.update', $classroom) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Class Name</label>
                <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $classroom->name) }}" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Description</label>
                <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Tell your students about this class...">{{ old('description', $classroom->description) }}</textarea>
            </div>
            
            <!-- Change 5: Tag Selector -->
            <div class="mb-5">
                <label class="form-label text-muted small fw-bold">Tags <span class="text-muted font-normal">(optional, select up to 5)</span></label>
                <div class="tag-selector" id="tag-selector">
                    @foreach($allTags as $tag)
                        <button type="button"
                                class="tag-chip"
                                data-tag-id="{{ $tag->id }}"
                                onclick="toggleTag(this)">
                            {{ $tag->name }}
                        </button>
                    @endforeach
                </div>
                <div id="selected-tags-inputs"></div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-5">
                    Save Changes
                </button>
                <a href="{{ route('classrooms.show', $classroom) }}" class="btn btn-light rounded-pill px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>

@push('styles')
<style>
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        margin-bottom: 4px;
    }
    .breadcrumb-nav a {
        color: var(--primary);
        text-decoration: none;
    }
    .breadcrumb-nav a:hover { text-decoration: underline; }
    .breadcrumb-sep { color: #64748b; }
    [data-theme="dark"] .breadcrumb-sep { color: #94a3b8; }
    .breadcrumb-current { color: var(--text); font-weight: 500; }

    .tag-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 16px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--card);
        max-height: 250px;
        overflow-y: auto;
    }
    .tag-chip {
        font-size: 0.85rem;
        padding: 6px 16px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: transparent;
        color: var(--text);
        cursor: pointer;
        transition: all 0.15s;
    }
    .tag-chip:hover {
        border-color: #764ba2;
        color: #764ba2;
    }
    .tag-chip--selected {
        background: #764ba2 !important;
        color: white !important;
        border-color: #764ba2 !important;
    }
    [data-theme="dark"] .tag-chip--selected {
        background: #a78bfa !important;
        border-color: #a78bfa !important;
        color: #1e1b4b !important;
    }
</style>
@endpush

@push('scripts')
<script>
    const MAX_TAGS = 5;
    let selectedTags = [];

    function toggleTag(btn) {
        const id = btn.dataset.tagId;
        if (selectedTags.includes(id)) {
            selectedTags = selectedTags.filter(t => t !== id);
            btn.classList.remove('tag-chip--selected');
        } else {
            if (selectedTags.length >= MAX_TAGS) {
                alert('You can select up to 5 tags only.');
                return;
            }
            selectedTags.push(id);
            btn.classList.add('tag-chip--selected');
        }
        // Rebuild hidden inputs
        const container = document.getElementById('selected-tags-inputs');
        container.innerHTML = selectedTags
            .map(id => `<input type="hidden" name="tags[]" value="${id}">`)
            .join('');
    }

    // Pre-select existing tags
    document.addEventListener('DOMContentLoaded', () => {
        const existingTagIds = @json($classroom->tags->pluck('id')->map(fn($id) => (string)$id));
        existingTagIds.forEach(id => {
            const btn = document.querySelector(`[data-tag-id="${id}"]`);
            if (btn) toggleTag(btn);
        });
    });
</script>
@endpush
@endsection
