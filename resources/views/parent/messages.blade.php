@extends('layouts.parent')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4" style="font-size: 2.4rem; color: #0077C8; font-weight: bold;">
         Messagerie
    </h2>

    <!-- Message de succès -->
    @if($successMessage)
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle me-1" style="color: green;"></i> {{ $successMessage }}
        </div>
    @endif

    <div class="d-flex flex-column align-items-center gap-4 mt-4">
        <a href="{{ route('parent.askTeacherForm') }}" class="btn btn-outline-primary btn-lg w-75 shadow-sm">
            <i class="fas fa-question-circle me-2" style="color: grey;"></i> Poser une question à un enseignant
        </a>

        <a href="{{ route('parent.justifyAdminForm') }}" class="btn btn-outline-success btn-lg w-75 shadow-sm">
            <i class="fas fa-file-alt me-2" style="color: grey;"></i> Envoyer une justification à l'administration
        </a>
    </div>
</div>
@endsection
