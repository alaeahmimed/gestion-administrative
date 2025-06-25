@extends('layouts.eleve')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>Mes Devoirs - {{ $eleve->classe }}
                        </h5>
                    </div>
                </div>
                <div >
                    @if($devoirs->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun devoir à afficher pour le moment.
                        </div>
                    @else
                        <div >
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="20%">Matière</th>
                                        <th width="25%">Description</th>
                                        <th width="20%">Donné le</th>
                                        <th width="20%">À rendre pour le</th>
                                        <th width="30%">Fichier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devoirs as $devoir)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                
                                                    <span>{{ $devoir->titre }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;" title="{{ $devoir->description }}">
                                                    {{ $devoir->description }}
                                                </div>
                                            </td>
                                            <td>{{ $devoir->created_at->translatedFormat('d/m/Y') }}</td>
                                            <td>
                                                <span>
                                                    {{ $devoir->dateLimite->translatedFormat('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td>
                                            @if($devoir && !empty($devoir['fichierJoint']))
                                                <a href="{{ route('devoir.download', $devoir['id']) }}" 
                                                class="btn btn-sm btn-outline-primary rounded-pill">
                                                     <i class="fas fa-download me-1"></i>Telecharger
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
    </div>
</div>
@endsection

@section('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .table-responsive {
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection