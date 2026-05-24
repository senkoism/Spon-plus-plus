@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <h2 class="fw-bold">Dashboard</h2>
        <p class="text-muted">Welcome to Spon++, your integrated learning platform.</p>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card p-4 text-center">
                <div class="display-6 fw-bold text-primary mb-2">0</div>
                <div class="text-muted small">Enrolled Courses</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card p-4 text-center">
                <div class="display-6 fw-bold text-success mb-2">0</div>
                <div class="text-muted small">Completed Tasks</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card p-4 text-center">
                <div class="display-6 fw-bold text-info mb-2">0</div>
                <div class="text-muted small">Pending Quizzes</div>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h5 class="fw-bold mb-4">Your Information</h5>
        <table class="table table-borderless">
            <tr>
                <td class="text-muted pb-3" style="width: 200px;">Full Name</td>
                <td class="fw-bold pb-3">{{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td class="text-muted pb-3">Username</td>
                <td class="fw-bold pb-3">{{ auth()->user()->username }}</td>
            </tr>
            <tr>
                <td class="text-muted pb-3">Email Address</td>
                <td class="fw-bold pb-3">{{ auth()->user()->email }}</td>
            </tr>
            <tr>
                <td class="text-muted pb-3">Role</td>
                <td class="fw-bold pb-3"><span class="badge bg-primary rounded-pill px-3">{{ ucfirst(auth()->user()->role) }}</span></td>
            </tr>
        </table>
    </div>
@endsection
