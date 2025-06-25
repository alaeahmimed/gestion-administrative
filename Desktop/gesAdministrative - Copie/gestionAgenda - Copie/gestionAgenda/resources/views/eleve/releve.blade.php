@extends('layouts.eleve')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>Mon releve de notes - {{ $eleve->classe }}
                        </h5>
                    </div>
                </div>
                <div >
                    @if($notes->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun notes à afficher pour le moment.
                        </div>
                    @else
                        <div >
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Matière</th>
                                        <th width="20%">CC1</th>
                                        <th width="20%"> CC2</th>
                                        <th width="20%">CC3</th>
                                        <th width="20%">Projet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes as $note)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                
                                                    <span>{{ $note->matiere }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{ $note->cc1 }}
                                                </div>
                                            </td>
                                            <td>{{ $note->cc2}}</td>
                                            <td>
                                                
                                                    {{ $note->cc3 }}
                                                
                                            </td>
                                            <td>
                                                
                                                    {{ $note->projet }}
                                                
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