<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parentt extends Model {
    use HasFactory;


    protected $fillable = ['cin', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function eleves() {
        return $this->hasMany(Eleve::class);
    }

    public function justification() {
        return $this->hasMany(JustificationAbsence::class);
    }
}