<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends Model {
    use HasFactory;

    protected $fillable = ['status', 'dateEnvoi', 'enseignant_id', 'eleve_id'];

    public function enseignant() {
        return $this->belongsTo(Enseignant::class);
    }

    public function eleve() {
        return $this->belongsTo(Eleve::class);
    }

    public function justification() {
        return $this->hasMany(JustificationAbsence::class);
    }
}