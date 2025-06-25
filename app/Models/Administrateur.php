<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Administrateur extends Model {
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function justification() {
        return $this->hasMany(JustificationAbsence::class);
    }
    
    public function bulletin() {
        return $this->hasMany(Bulletin::class);
    }

    public function emploi() {
        return $this->hasMany(Emploi::class);
    }

    public function evenement() {
        return $this->hasMany(Evenement::class);
    }
}