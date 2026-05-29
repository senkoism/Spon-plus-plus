@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="fw-bold">Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->name }}! Here's what's happening.</p>
        
        <div class="search-wrapper mt-3" style="max-width: 400px;">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="dashboard-search" placeholder="Search classrooms by name or tag..." class="search-input">
        </div>
    </div>

    <!-- Section A — Pending Assignments -->
    <div class="mb-5">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i data-lucide="clipboard-list" class="text-warning"></i> Pending Assignments
        </h5>
        @forelse($pendingAssignments as $assignment)
            <div class="card p-3 mb-2 shadow-sm border-0 assignment-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle p-2 rounded-3 text-primary">
                            <i data-lucide="file-text"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-main assignment-title">{{ $assignment->title }}</div>
                            <div class="small text-muted assignment-meta">Class: {{ $assignment->classroom->name }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="text-end">
                            <div class="small text-muted assignment-meta">Due Date</div>
                            <div class="fw-bold assignment-date {{ $assignment->due_date && \Carbon\Carbon::parse($assignment->due_date)->lt(now()->addDay()) ? 'text-danger' : 'text-main' }}">
                                {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('D, d M Y H:i') : '—' }}
                            </div>
                        </div>
                        <a href="{{ route('classrooms.show', $assignment->classroom) }}" class="btn btn-primary rounded-pill px-4 btn-sm">
                            Submit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-4 text-center border-0 bg-lighter rounded-4">
                <div class="text-success mb-2" style="font-size: 2rem;">🎉</div>
                <p class="m-0 text-muted empty-state-text">All caught up! No pending assignments.</p>
            </div>
        @endforelse
    </div>

    <!-- Section B — Recently Accessed Classrooms (Horizontal Scroll) -->
    <div class="recent-classrooms-wrapper">
        <div class="recent-classrooms-header">
            <h5 class="fw-bold m-0 d-flex align-items-center gap-2">
                <i data-lucide="clock" class="text-primary"></i> Recently Accessed
            </h5>
            <div class="scroll-arrows">
                <button type="button" id="scroll-left" class="scroll-arrow" title="Scroll left">&#8249;</button>
                <button type="button" id="scroll-right" class="scroll-arrow" title="Scroll right">&#8250;</button>
            </div>
        </div>
        <div class="recent-classrooms-track" id="classrooms-track">
            @forelse($recentClassrooms as $classroom)
                <a href="{{ route('classrooms.show', $classroom) }}" class="recent-classroom-card text-decoration-none">
                    {{-- Banner or gradient --}}
                    <div class="recent-card-banner" style="
                        {{ $classroom->banner_path
                        ? 'background-image: url(' . \Illuminate\Support\Facades\Storage::url($classroom->banner_path) . '); background-size: cover; background-position: center;'
                        : 'background: linear-gradient(135deg, #764ba2, #667eea);' }}
                    ">
                        <span class="recent-card-name text-truncate">{{ $classroom->name }}</span>
                    </div>
                    <div class="recent-card-meta">
                        <span class="recent-card-role role-{{ $classroom->pivot->role }}">
                            {{ ucfirst($classroom->pivot->role) }}
                        </span>
                        <span class="recent-card-desc small text-muted text-truncate w-100">{{ $classroom->description ?: 'No description' }}</span>
                    </div>
                </a>
            @empty
                <div class="w-100 text-center py-4 bg-light rounded-4">
                    <p class="m-0 text-muted">No recently accessed classrooms.</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('styles')
    <style>
        .bg-primary-subtle { background-color: rgba(118, 75, 162, 0.1); }

        .recent-classrooms-wrapper { margin-bottom: 32px; }
        .recent-classrooms-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 12px;
        }
        .scroll-arrows { display: flex; gap: 8px; }
        .scroll-arrow {
            width: 32px; height: 32px; border-radius: 50%;
            border: 1px solid var(--border); background: var(--card);
            color: var(--text); font-size: 1.2rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .scroll-arrow:hover { background: var(--primary); color: white; border-color: var(--primary); }

        .recent-classrooms-track {
            display: flex; gap: 16px;
            overflow-x: auto; scroll-snap-type: x mandatory;
            -ms-overflow-style: none; scrollbar-width: none; /* hide scrollbar */
            padding: 4px 4px 12px 4px;
        }
        .recent-classrooms-track::-webkit-scrollbar { display: none; }

        .recent-classroom-card {
            flex: 0 0 240px; scroll-snap-align: start;
            border-radius: 12px; overflow: hidden;
            border: 1px solid var(--border); background: var(--card);
            text-decoration: none; color: var(--text);
            transition: transform 0.15s, box-shadow 0.15s;
            display: flex; flex-direction: column;
        }
        .recent-classroom-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }

        .recent-card-banner {
            height: 110px; display: flex;
            align-items: flex-end; padding: 10px;
            position: relative;
        }
        .recent-card-banner::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.4));
        }
        .recent-card-name {
            color: white; font-weight: 700;
            font-size: 0.95rem; text-shadow: 0 1px 4px rgba(0,0,0,0.5);
            position: relative; z-index: 1;
            width: 100%;
        }
        .recent-card-meta { padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; }
        .recent-card-role {
            font-size: 0.7rem; font-weight: 600; border-radius: 999px;
            padding: 2px 8px; width: fit-content;
        }
        .role-teacher { background: #dcfce7; color: #166534; }
        .role-member  { background: #dbeafe; color: #1e40af; }
        [data-theme="dark"] .role-teacher { background: #14532d; color: #86efac; }
        [data-theme="dark"] .role-member  { background: #1e3a5f; color: #93c5fd; }
        .recent-card-desc { display: block; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }

        /* Dark Mode Specifics for Pending Assignments */
        [data-theme="dark"] .assignment-card {
            background-color: var(--card);
            border: 1px solid var(--border) !important;
        }
        [data-theme="dark"] .assignment-title,
        [data-theme="dark"] .assignment-date.text-main {
            color: var(--text) !important;
        }
        [data-theme="dark"] .assignment-meta,
        [data-theme="dark"] .empty-state-text {
            color: #94a3b8 !important;
        }
        [data-theme="dark"] .bg-lighter {
            background-color: rgba(255,255,255,0.03) !important;
        }
        .text-main { color: #1e293b; }
        [data-theme="dark"] .text-main { color: var(--text); }

        /* Search styling (copied for consistency) */
        .search-wrapper { position: relative; }
        .search-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); width: 18px; height: 18px;
            color: var(--sidebar-link);
        }
        .search-input {
            width: 100%; padding: 10px 14px 10px 42px;
            border-radius: 999px; border: 1px solid var(--border);
            background: var(--card); color: var(--text);
            transition: all 0.2s;
        }
        .search-input:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('classrooms-track');
            const scrollAmount = 280; // px per click, roughly one card width

            document.getElementById('scroll-left')?.addEventListener('click', () => {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            document.getElementById('scroll-right')?.addEventListener('click', () => {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            // Feature 1: Dashboard search filtering
            const searchInput = document.getElementById('dashboard-search');
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.recent-classroom-card');
                let foundMatch = false;

                cards.forEach(card => {
                    const name = card.querySelector('.recent-card-name')?.innerText.toLowerCase() || '';
                    const desc = card.querySelector('.recent-card-desc')?.innerText.toLowerCase() || '';
                    // The dashboard cards don't have explicit tag data attributes, but we can search name/desc
                    const isMatch = name.includes(query) || desc.includes(query);
                    card.style.display = isMatch ? '' : 'none';
                    if (isMatch) foundMatch = true;
                });
            });
        });

    </script>
    @endpush
@endsection
