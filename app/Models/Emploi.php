<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emploi extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'cycle',
        'classe', 
        'file_path',  
        'administrateur_id'
    ];

    protected $appends = ['annee_scolaire'];

    public function administrateur(): BelongsTo
    {
        return $this->belongsTo(Administrateur::class);
    }

   public function eleves()
    {
        return $this->hasMany(Eleve::class, 'classe', 'classe')
                   ->where('cycle', $this->cycle);
    }

    public function getAnneeScolaireAttribute(): string
    {
        $year = now()->year;
        return now()->month >= 9 
            ? "{$year}-".($year + 1) 
            : ($year - 1)."-{$year}";
    }
}