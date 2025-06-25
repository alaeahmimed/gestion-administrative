@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-dark">
                    <h5 class="mb-0">Modifier le parent</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('editParent.update', $parent->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                    id="nom" name="nom" value="{{ old('nom', $parent->user->nom) }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                    id="prenom" name="prenom" value="{{ old('prenom', $parent->user->prenom) }}" required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cin" class="form-label">CIN</label>
                                <input type="text" class="form-control @error('cin') is-invalid @enderror"
                                    id="cin" name="cin" value="{{ old('cin', $parent->cin) }}" required>
                                @error('cin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="login" class="form-label">Login (email)</label>
                                <input type="email" class="form-control @error('login') is-invalid @enderror"
                                    id="login" name="login" value="{{ old('login', $parent->user->login) }}" required>
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="motDePasse" class="form-label">Mot de passe (laisser vide pour ne pas changer)</label>
                                <input type="password" class="form-control @error('motDePasse') is-invalid @enderror"
                                    id="motDePasse" name="motDePasse">
                                @error('motDePasse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('listerParent.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
