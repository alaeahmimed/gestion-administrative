@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Emplois du temps & Événements</h1>

    <div class="row">
        <div class="col-md-6 mb-3">
            <a href="{{route('Admin.ens-eleve')}}" class="btn btn-outline-primary w-100 py-4">
                <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
               les emplois du temps
            </a>
        </div>
        <div class="col-md-6 mb-3">
            <a href="{{ route('Admin.liste-eve') }}" class="btn btn-outline-success w-100 py-4">
                <i class="fas fa-calendar-day fa-2x mb-2"></i><br>
               les événements
            </a>
        </div>
    </div>
</div>
@endsection
