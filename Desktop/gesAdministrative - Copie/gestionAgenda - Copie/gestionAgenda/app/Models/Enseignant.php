<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enseignant extends Model {
    use HasFactory;


    protected $fillable = ['matiere','classe', 'user_id'];

     protected $casts = [
    'classe' => 'array',
    
];

    public function user() {
        return $this->belongsTo(User::class);
    }


    public function devoir() {
        return $this->hasMany(Devoir::class);
    }

    public function absence() {
        return $this->hasMany(Absence::class);
    }

   // app/Models/Enseignant.php
public function emploiTemps()
{
    return $this->morphOne(Emploi::class, 'emploisable');
}

public function setClassesAttribute($value)
    {
        $this->attributes['classe'] = json_encode($value);
    }
    
    // Pour récupérer les classes
    public function getClassesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
    
    // Scopes (doivent être dans le modèle où vous les utilisez)
    public function scopeForEnseignant(Builder $query, $enseignantId): Builder
    {
        return $query->where('id', $enseignantId); // Ici c'est pour filtrer les enseignants
    }

    public function scopeForClasse(Builder $query, $classe): Builder
    {
        return $query->whereJsonContains('classe', $classe);
    }
    
    
    // Si vous stockez les matières comme string séparée par des virgules
    
// Ou pour une conversion automatique
public function getMatiere($value)
{
    if (is_array($value)) {
        return $value;
    }
    
    $json = json_decode($value, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $json;
    }
    
    return array_map('trim', explode(',', $value));
}
  

public function setMatiere($value)
{
    $this->attributes['matiere'] = is_array($value) 
        ? implode(',', $value)
        : $value;
}
}

