@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Emplois du temps des enseignants et eleves</h1>

    <div class="row">
        <div class="col-md-6 mb-3">
            <a href="{{route('par-emplois.index')}}" class="btn btn-outline-primary w-100 py-4">
                <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
              Emploi du temps eleves
            </a>
        </div>
        <div class="col-md-6 mb-3">
           <a href="{{route('ens-emplois.index')}}" class="btn btn-outline-primary w-100 py-4">
                <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
              Emploi du temps enseignants
            </a>
        </div>
    </div>
</div>
@endsection
