<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evenement extends Model {
    use HasFactory;
    
    protected $fillable = ['dateDebut', 'dateFin', 'heure', 'description','classe' ,'image',  'administrateur_id'];
    protected $dates = ['dateDebut', 'dateFin'];
    protected $casts = [
    'dateDebut' => 'datetime',
    'dateFin' => 'datetime',
    'classe'=>'array'
];
    public function eleves() {
        return $this->belongsToMany(Eleve::class,'eleve_evenement')->withTimestamps();
    }

    public function administrateur() {
        return $this->belongsTo(Administrateur::class);
    }

      public function setClassesAttribute($value)
    {
        $this->attributes['classe'] = json_encode($value);
    }
}