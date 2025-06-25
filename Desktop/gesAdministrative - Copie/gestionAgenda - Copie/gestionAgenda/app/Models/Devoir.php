<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Devoir extends Model {
    use HasFactory;

    protected $fillable = ['titre', 'description', 'dateLimite','classe' ,'fichierJoint', 'enseignant_id'];
    protected $casts = [
    'dateLimite' => 'datetime',
];

    public function eleves() {
        return $this->belongsToMany(Eleve::class,'eleve_devoir')->withPivot('note', 'commentaire')->withTimestamps();
    }

    public function enseignant() {
        return $this->belongsTo(Enseignant::class);
    }

      public function scopeForEnseignant(Builder $query, $enseignantId): Builder
{
    return $query->where('enseignant_id', $enseignantId);
}

public function scopeForClasse(Builder $query, $classe): Builder
{
    return $query->where('classe', $classe);
}
}