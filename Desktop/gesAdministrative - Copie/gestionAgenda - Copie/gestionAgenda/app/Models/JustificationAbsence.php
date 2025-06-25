<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JustificationAbsence extends Model {
    use HasFactory;

    protected $fillable = ['raison', 'fichier', 'absence_id', 'parentt_id', 'administrateur_id'];

    public function absence() {
        return $this->belongsTo(Absence::class);
    }

    public function parentt() {
        return $this->belongsTo(Parentt::class);
    }

    public function administrateur() {
        return $this->belongsTo(Administrateur::class);
    }
}