@extends('layouts.enseignant')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">Mon Emploi du Temps</h2>
        </div>
    </div>

    @if($emploi)
        <div class="card shadow border-0">
            <div class="card-body p-0" style="height: calc(100vh - 150px);">
                <iframe src="{{ route('enseignant.emploi.view') }}" 
                        style="width: 100%; height: 100%; border: none;"
                        allowfullscreen></iframe>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            Votre emploi du temps n'a pas encore été importé par l'administration.
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    body {
        overflow: hidden;
    }
    
    .card {
        height: calc(100vh - 150px);
    }
    
    iframe {
        min-height: 100%;
        min-width: 100%;
    }
    
    .container-fluid {
        padding: 20px;
        height: 100vh;
    }
</style>
@endsection