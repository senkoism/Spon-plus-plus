@php
    $now = now();
    $isLocked = (
      ($material->open_date && $now->lt($material->open_date)) ||
      ($material->due_date  && $now->gt($material->due_date))
    );
    $icon = $iconMap[$material->type] ?? $iconMap['document'];
@endphp

<div class="activity-row activity-type-{{ $material->type }}">
  {{-- Icon --}}
  <div class="activity-icon-wrapper type-{{ $material->type }}">
    <i data-lucide="{{ $icon['icon'] }}"
       style="color: {{ $icon['color'] }}; width:20px; height:20px;"></i>
  </div>

  {{-- Info --}}
  <div class="activity-info">
    <span class="activity-title">{{ $material->title }}</span>
    @if($material->type === 'assignment' && ($material->open_date || $material->due_date))
      <span class="activity-dates small text-muted ms-2">
        @if($material->open_date) Opens: {{ $material->open_date->format('D, d M Y, g:i A') }} @endif
        @if($material->due_date)  &nbsp; Due: {{ $material->due_date->format('D, d M Y, g:i A') }} @endif
      </span>
    @endif
  </div>

  {{-- Actions --}}
  <div class="activity-actions d-flex align-items-center justify-content-end">
    @if($material->type === 'assignment')
      @if($userRole === 'member')
        @if(!$isLocked)
          <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 d-flex align-items-center gap-2" 
                  onclick="openSubmitModal({{ $material->id }}, '{{ addslashes($material->title) }}')">
             @if(isset($memberSubmissions[$material->id]))
                <i data-lucide="refresh-cw" style="width:14px;height:14px"></i> Re-submit
             @else
                <i data-lucide="upload" style="width:14px;height:14px"></i> Submit
             @endif
          </button>
        @else
          <span class="text-muted small">Submission closed.</span>
        @endif
      @elseif($userRole === 'teacher')
        <span class="text-muted small me-2">{{ $material->submissions->count() }} submitted</span>
      @endif
    @else
      {{-- Regular stream item --}}
      @if($material->file_path)
        <a href="{{ route('announcements.download', $material) }}" class="btn btn-sm btn-light rounded-pill px-3 d-flex align-items-center gap-1">
          <i data-lucide="download" style="width:14px;height:14px"></i> Download
        </a>
      @endif
    @endif

    @if($userRole === 'teacher')
      <div class="ms-2 d-flex gap-1">
        <button class="btn btn-icon-only btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $material->id }}" title="Edit">
           <i data-lucide="pencil" style="width:14px;height:14px"></i>
        </button>
        <button type="button" class="btn btn-icon-only btn-sm text-danger"
          onclick="showConfirmModal({
              title: 'Delete Item',
              message: 'Are you sure you want to delete \'{{ $material->title }}\'?',
              onConfirm: () => document.getElementById('delete-announcement-{{ $material->id }}').submit()
          })" title="Delete">
          <i data-lucide="trash-2" style="width:14px;height:14px"></i>
        </button>
        <form id="delete-announcement-{{ $material->id }}" method="POST"
              action="{{ route('announcements.destroy', $material) }}" style="display:none;">
          @csrf @method('DELETE')
        </form>
      </div>
    @endif
  </div>
</div>
