<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Eleve extends Model {
    use HasFactory;

    protected $fillable = ['cef', 'classe','cycle', 'parentt_id', 'emploi_id','user_id'];


    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function absences() {
        return $this->hasMany(Absence::class);
    }
    public function parentt() {
        return $this->belongsTo(Parentt::class);
    }

    public function emploi() {
        return $this->belongsTo(Emploi::class);
    }

public function evenements()
{
    return $this->belongsToMany(Evenement::class, 'eleve_evenement', 'eleve_id', 'evenement_id');
}

    public function devoirs() {
        return $this->belongsToMany(Devoir::class,'eleve_devoir')->withPivot('note', 'commentaire')->withTimestamps();
    }

    public function bulletin() {
        return $this->hasOne(Bulletin::class);
    }

     public function notes()
    {
        return $this->hasMany(Notes::class);
    }
}