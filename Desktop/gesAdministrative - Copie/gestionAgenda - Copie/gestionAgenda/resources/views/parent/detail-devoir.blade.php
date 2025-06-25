@extends('layouts.parent')

@section('content')
<div class="container mt-5 d-flex justify-content-center">

    <div class="w-100" style="max-width: 600px;">
        <!-- Bouton retour stylisé -->
        <a href="{{ route('parent.devoirs.eleve', ['eleveId' => $eleve->id]) }}" class="btn btn-outline-primary mb-4">
            <i class="fas fa-arrow-left me-1"></i> Retour aux devoirs
        </a>

        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h3 class="text-center text-primary mb-4">
                    <i class="fas fa-book-open me-2"></i>{{ $devoir->titre }}
                </h3>

                <div class="mb-4 text-center">
                    
                       
                                            @if($devoir && !empty($devoir['fichierJoint']))
                                                <a href="{{ route('devoir.download', $devoir['id']) }}" 
                                                class="btn btn-sm btn-outline-primary rounded-pill">
                                                     <i class="fas fa-download me-1"></i>Telecharger
                                                </a>
                                            @else
                                                <span class="text-muted">Aucun disponible</span>
                                            @endif
                                         
                    
                </div>

                <hr>
            </div>
        </div>
    </div>

</div>
@endsection
