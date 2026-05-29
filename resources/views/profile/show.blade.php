@extends('layouts.app')
 
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header py-4 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="avatar-large mx-auto mb-3 d-flex align-items-center justify-content-center bg-white fw-bold rounded-circle shadow" style="width: 100px; height: 100px; font-size: 2.5rem; color: #764ba2 !important;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-white mb-0">{{ $user->name }}</h3>
                    <p class="text-white-50 mb-0">@ {{$user->username}}</p>
                </div>
 
                <div class="card-body p-4">
                    <!-- View Section -->
                    <div id="profile-view">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-bold">Profile Details</h5>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="toggleEdit()">
                                <i data-lucide="pencil" style="width: 14px; height: 14px;" class="me-1"></i> Edit Profile
                            </button>
                        </div>
 
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="text-muted profile-label small text-uppercase fw-bold">Full Name</label>
                                <p class="mb-0 fs-5 profile-value">{{ $user->name }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted profile-label small text-uppercase fw-bold">Username</label>
                                <p class="mb-0 fs-5 profile-value">{{ $user->username }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted profile-label small text-uppercase fw-bold">Email Address</label>
                                <p class="mb-0 fs-5 profile-value">{{ $user->email }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-muted profile-label small text-uppercase fw-bold">Member Since</label>
                                <p class="mb-0 fs-5 profile-value">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
 
                    <!-- Edit Section -->
                    <div id="profile-edit" style="display: none;">
                        <h5 class="mb-4 fw-bold">Edit Profile</h5>
                        
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label small fw-bold">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label small fw-bold">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
 
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                <button type="button" class="btn btn-light rounded-pill px-4" onclick="toggleEdit()">Cancel</button>
                            </div>
                        </form>
 
                        <hr class="my-5">
 
                        <h5 class="mb-4 fw-bold">Update Password</h5>
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
 
                            <div class="mb-3">
                                <label for="current_password" class="form-label small fw-bold">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
 
                            <div class="mb-3">
                                <label for="new_password" class="form-label small fw-bold">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
 
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label small fw-bold">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            </div>
 
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@push('scripts')
<script>
    function toggleEdit() {
        const view = document.getElementById('profile-view');
        const edit = document.getElementById('profile-edit');
        if (view.style.display === 'none') {
            view.style.display = 'block';
            edit.style.display = 'none';
        } else {
            view.style.display = 'none';
            edit.style.display = 'block';
        }
        lucide.createIcons();
    }
</script>
@endpush
@endsection
