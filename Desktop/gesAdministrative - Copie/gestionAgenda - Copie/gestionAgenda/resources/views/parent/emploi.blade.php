@extends('layouts.parent')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="mb-0">Emploi du Temps</h2>
        </div>
        <div class="col-md-4 mt-5"> {{-- Ajout de mt-2 ici --}}
            <form method="GET" action="{{ route('parent.emploi') }}">
                <select name="eleve_id" class="form-control" onchange="this.form.submit()">
                    @foreach($eleves as $eleve)
                    <option value="{{ $eleve->id }}" 
                            {{ ($selectedEleve->id == $eleve->id) ? 'selected' : '' }}>
                        {{ $eleve->user->nom}} {{ $eleve->user->prenom }} - {{ $eleve->classe }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($emploi)
    <div class="card shadow border-0">
        <div class="card-body p-0" style="height: 70vh;">
            <iframe src="{{ route('parent.emploi.view', $emploi->id) }}" 
                    style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        Aucun emploi trouvé pour {{ $selectedEleve->user->name }} ({{ $selectedEleve->classe }})
    </div>
    @endif
</div>
@endsection
