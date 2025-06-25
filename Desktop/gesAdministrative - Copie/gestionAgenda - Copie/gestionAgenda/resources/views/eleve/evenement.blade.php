@extends('layouts.eleve')

@section('content')
<div class="container">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>
                    <i class="fas fa-calendar-alt"></i>
                    Événements - Classe: {{ $eleve->classe }}
                </h4>
            </div>

            <div class="card-body">
                @if($evenements->isEmpty())
                    <div class="alert alert-info">
                        Aucun événement prévu pour cette classe.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                   
                                    <th>Description</th>
                                    <th>Date Debut</th>
                                    <th>Date Fin</th>
                                    <th>Fichier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evenements as $evenement)
                                <tr>
                                    
                                    <td>{{ Str::limit($evenement->description, 50) }}</td>
                                    <td>
                                        {{ $evenement->dateDebut->format('d/m/Y') }}
                                        
                                    </td>
                                     <td>
                                        {{ $evenement->dateFin->format('d/m/Y') }}
                                        
                                    </td>
                                    
                                    <td>
                                            @if(!empty($evenement->image))
                                                <a href="{{ route('eleve.evenement.voir', $evenement->id) }}" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                                   <i class="fas fa-download me-1"></i>Voir
                                                </a>
                                            @else
                                                <span class="text-muted">Aucun disponible</span>
                                            @endif
                                        </td> 
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                
                @endif
            </div>
        </div>
   
</div>
@endsection