@extends('layouts.parent')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4" style="font-size: 2.4rem; color: #0077C8; font-weight: bold;">
        <i class="fas fa-question-circle me-2" style="color: grey;"></i> Poser une question à un enseignant
    </h3>

    @if(session('success'))
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle me-1" style="color: green;"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('parent.askTeacher') }}" method="POST" class="bg-white shadow-sm rounded p-4">
        @csrf

        <div class="mb-3">
            <label for="teacherSearch" class="form-label fw-bold text-secondary">
                <i class="fas fa-search me-1" style="color: grey;"></i> Rechercher un enseignant
            </label>
            <input type="text" name="teacher_search" id="teacherSearch" class="form-control" placeholder="Nom ou prénom">
            @error('teacher_search') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="message" class="form-label fw-bold text-secondary">
                <i class="fas fa-comment-alt me-1" style="color: grey;"></i> Votre question
            </label>
            <textarea name="message" id="message" class="form-control" rows="4"></textarea>
            @error('message') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1" style="color: white;"></i> Envoyer
            </button>
            <a href="{{ route('parent.messages') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1" style="color: white;"></i> Retour
            </a>
        </div>
    </form>
</div>
@endsection
