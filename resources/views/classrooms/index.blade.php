@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Classrooms</h2>
        <div>
            <button class="btn btn-outline-primary me-2 rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#joinClassModal">
                <i data-lucide="door-open" class="me-1" style="width:18px;height:18px"></i> Join Class
            </button>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createClassModal">
                <i data-lucide="plus" class="me-1" style="width:18px;height:18px"></i> Create Class
            </button>
        </div>
    </div>

    <!-- Change 3a: Search & View Toggle -->
    <div class="classrooms-toolbar mb-4">
        <div class="search-wrapper">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="classroom-search" placeholder="Search classrooms by name or tag..." class="search-input">
        </div>
        <div class="view-toggle">
            <button type="button" id="view-card" class="view-btn active" title="Card view">
                <i data-lucide="layout-grid" style="width:18px;height:18px"></i>
            </button>
            <button type="button" id="view-list" class="view-btn" title="List view">
                <i data-lucide="list" style="width:18px;height:18px"></i>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="classrooms-grid-view">
        <div class="row classrooms-grid" id="classrooms-container">
            @forelse($classrooms as $classroom)
                <div class="col-md-6 mb-4 classroom-card-item searchable-item" 
                     data-name="{{ strtolower($classroom->name) }}"
                     data-tags="{{ strtolower($classroom->tags->pluck('name')->join(' ')) }}">
                    <div class="card h-100 p-4 classroom-card shadow-sm border-0">
                        <div class="classroom-card-content w-100">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge {{ $classroom->pivot->role === 'teacher' ? 'bg-success' : 'bg-primary' }} rounded-pill px-3">
                                    {{ ucfirst($classroom->pivot->role) }}
                                </span>
                                
                                <div class="classroom-card-tags">
                                    @forelse($classroom->tags->take(3) as $tag)
                                        <span class="classroom-tag">{{ $tag->name }}</span>
                                    @empty
                                        <span class="classroom-tag-empty small text-muted">No tags</span>
                                    @endforelse
                                    @if($classroom->tags->count() > 3)
                                        <span class="classroom-tag classroom-tag-more">+{{ $classroom->tags->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>

                            <h4 class="fw-bold mb-2 classroom-name">{{ $classroom->name }}</h4>
                            <p class="text-muted class-description small mb-4">{{ Str::limit($classroom->description, 100) }}</p>
                            
                            <div class="mt-auto classroom-footer">
                                <a href="{{ route('classrooms.show', $classroom) }}" class="btn btn-outline-primary w-100 rounded-pill d-flex align-items-center justify-content-center gap-2">
                                     <i data-lucide="door-open" style="width:18px;height:18px"></i> Enter Class
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 no-results-global">
                    <i data-lucide="book-open" style="width:48px;height:48px" class="text-light mb-3"></i>
                    <p class="text-muted">You haven't joined or created any classes yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Change 2: My Classrooms Spotify-style table -->
    <div id="classrooms-list-view" class="d-none">
        <div class="classrooms-table-wrapper">
            <table class="classrooms-table">
                <thead>
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-thumb"></th>
                        <th class="col-name">Class Name</th>
                        <th class="col-role">Role</th>
                        <th class="col-tags">Tags</th>
                        <th class="col-desc">Description</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classrooms as $index => $classroom)
                    <tr class="classroom-row searchable-item"
                        data-name="{{ strtolower($classroom->name) }}"
                        data-tags="{{ strtolower($classroom->tags->pluck('name')->join(' ')) }}">

                        <td class="col-num font-monospace small">{{ $index + 1 }}</td>

                        <td class="col-thumb">
                            @if($classroom->banner_path)
                                <img src="{{ Storage::url($classroom->banner_path) }}" class="classroom-thumb" alt="">
                            @else
                                <div class="classroom-thumb classroom-thumb-gradient"></div>
                            @endif
                        </td>

                        <td class="col-name">
                            <span class="classroom-row-name">{{ $classroom->name }}</span>
                        </td>

                        <td class="col-role">
                            <span class="role-badge role-{{ $classroom->pivot->role }}">
                                {{ ucfirst($classroom->pivot->role) }}
                            </span>
                        </td>

                        <td class="col-tags">
                            <div class="row-tags-list">
                                @forelse($classroom->tags->take(3) as $tag)
                                    <span class="classroom-tag">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-muted small">—</span>
                                @endforelse
                                @if($classroom->tags->count() > 3)
                                    <span class="classroom-tag classroom-tag-more">+{{ $classroom->tags->count() - 3 }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="col-desc">
                            <span class="text-muted small">{{ Str::limit($classroom->description, 60) }}</span>
                        </td>

                        <td class="col-actions">
                            <a href="{{ route('classrooms.show', $classroom) }}" class="btn-enter-row">
                                Enter Class
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($classrooms->isEmpty())
            <div class="text-center py-5 no-results-global">
                <i data-lucide="book-open" style="width:48px;height:48px" class="text-light mb-3"></i>
                <p class="text-muted">You haven't joined or created any classes yet.</p>
            </div>
        @endif
    </div>

    <!-- No match message container -->
    <div id="no-search-results" class="text-center py-5 d-none">
        <p class="text-muted">No classrooms match your search.</p>
    </div>

    <!-- Create Class Modal -->
    <div class="modal fade" id="createClassModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form action="{{ route('classrooms.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Create New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Class Name</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Mathematics 101" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Description</label>
                            <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Tell your students about this class..."></textarea>
                        </div>
                        
                        <!-- Change 5: Tag Selector -->
                        <div class="mb-0">
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
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2">
                            <i data-lucide="plus" style="width:18px;height:18px"></i> Create Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Join Class Modal -->
    <div class="modal fade" id="joinClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form action="{{ route('classrooms.join') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Join Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Enter the 8-digit code provided by your teacher.</p>
                        <div class="mb-0">
                            <label class="form-label text-muted small fw-bold">Join Code</label>
                            <input type="text" name="join_code" class="form-control rounded-3 text-center fw-bold fs-4" placeholder="ABC123XY" maxlength="8" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2">
                            <i data-lucide="door-open" style="width:18px;height:18px"></i> Join Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('styles')
<style>
    .classrooms-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }
    .search-wrapper {
        position: relative;
        flex: 1;
        max-width: 400px;
    }
    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: var(--text-muted);
    }
    .search-input {
        width: 100%;
        padding: 10px 14px 10px 42px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--text);
        transition: all 0.2s;
    }
    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
    }
    .view-toggle {
        display: flex;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 4px;
    }
    .view-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .view-btn.active {
        background: var(--primary);
        color: white;
    }

    .classroom-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .classroom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .classroom-card-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    .classroom-tag {
        font-size: 0.7rem;
        font-weight: 500;
        background: rgba(118, 75, 162, 0.12);
        color: #764ba2;
        border-radius: 999px;
        padding: 2px 8px;
        border: 1px solid rgba(118, 75, 162, 0.25);
    }
    .classroom-tag-more {
        background: var(--border);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }
    [data-theme="dark"] .classroom-tag {
        background: rgba(167, 139, 250, 0.15);
        color: #a78bfa;
        border-color: rgba(167, 139, 250, 0.3);
    }

    /* Spotify-style Classrooms Table */
    .classrooms-table-wrapper { width: 100%; overflow-x: auto; }
    .classrooms-table {
        width: 100%; border-collapse: collapse; font-size: 0.875rem;
    }
    .classrooms-table thead th {
        padding: 8px 12px;
        color: var(--text-muted); font-weight: 600;
        font-size: 0.75rem; text-transform: uppercase;
        letter-spacing: 0.05em; text-align: left;
        border-bottom: 1px solid var(--border);
    }
    .classrooms-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background-color 0.15s;
    }
    .classrooms-table tbody tr:hover { background-color: var(--sidebar-hover); }
    .classrooms-table tbody tr:last-child { border-bottom: none; }
    .classrooms-table td { padding: 12px 12px; vertical-align: middle; }

    .col-thumb { width: 60px; }
    .classroom-thumb {
        width: 48px; height: 32px;
        border-radius: 6px; object-fit: cover; display: block;
    }
    .classroom-thumb-gradient {
        background: linear-gradient(135deg, #764ba2, #667eea);
        width: 48px; height: 32px; border-radius: 6px;
    }

    .classroom-row-name { font-weight: 600; color: var(--text); }
    .row-tags-list { display: flex; flex-wrap: wrap; gap: 4px; }

    .role-badge {
        font-size: 0.7rem; font-weight: 600; border-radius: 999px;
        padding: 2px 10px; display: inline-block;
    }
    .role-teacher { background: #dcfce7; color: #166534; }
    .role-member { background: #dbeafe; color: #1e40af; }
    [data-theme="dark"] .role-teacher { background: #14532d; color: #86efac; }
    [data-theme="dark"] .role-member { background: #1e3a8a; color: #93c5fd; }

    .btn-enter-row {
        font-size: 0.75rem; font-weight: 600; padding: 6px 16px;
        border-radius: 8px; border: 1px solid var(--border);
        color: var(--text); text-decoration: none; white-space: nowrap;
        background: var(--card); display: inline-block;
        transition: all 0.2s;
    }
    .btn-enter-row:hover { background: var(--primary); color: white; border-color: var(--primary); }


    /* Tag Selector Styles */
    .tag-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: var(--card);
        max-height: 200px;
        overflow-y: auto;
    }
    .tag-chip {
        font-size: 0.8rem;
        padding: 4px 12px;
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
    document.addEventListener('DOMContentLoaded', () => {
        // Change 3a: Search Filter
        const searchInput = document.getElementById('classroom-search');
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('.searchable-item');
            let hasVisible = false;

            items.forEach(item => {
                const name = item.dataset.name || '';
                const tags = item.dataset.tags || '';
                const isMatch = name.includes(query) || tags.includes(query);
                item.style.display = isMatch ? '' : 'none';
                if (isMatch) hasVisible = true;
            });

            const noMatch = document.getElementById('no-search-results');
            if (noMatch) {
                if (!hasVisible && query !== '') {
                    noMatch.classList.remove('d-none');
                } else {
                    noMatch.classList.add('d-none');
                }
            }
        });

        // Change 3b: View Toggle
        const gridView = document.getElementById('classrooms-grid-view');
        const listView = document.getElementById('classrooms-list-view');
        const viewCardBtn = document.getElementById('view-card');
        const viewListBtn = document.getElementById('view-list');

        function setView(view) {
            if (view === 'list') {
                gridView.classList.add('d-none');
                listView.classList.remove('d-none');
                viewListBtn.classList.add('active');
                viewCardBtn.classList.remove('active');
            } else {
                gridView.classList.remove('d-none');
                listView.classList.add('d-none');
                viewCardBtn.classList.add('active');
                viewListBtn.classList.remove('active');
            }
            localStorage.setItem('classrooms-view', view);
        }

        viewCardBtn.addEventListener('click', () => setView('grid'));
        viewListBtn.addEventListener('click', () => setView('list'));

        // Init view from localStorage
        const savedView = localStorage.getItem('classrooms-view') || 'grid';
        setView(savedView);

    });

    // Change 5: Tag Selection
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
</script>
@endpush
@endsection
