<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
   protected $fillable = ['eleve_id', 'matiere', 'cc1', 'cc2', 'cc3', 'projet'];
    
    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }
}
