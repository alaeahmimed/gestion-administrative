<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
class Bulletin extends Model {
    use HasFactory;

    protected $fillable = ['fichierPdf', 'administrateur_id', 'eleve_id'];

    public function administrateur() {
        return $this->belongsTo(Administrateur::class);
    }

    public function eleve() {
        return $this->belongsTo(Eleve::class);
    }


    // In Bulletin.php model
    protected static function booted()
    {
        static::deleting(function ($bulletin) {
            if ($bulletin->fichierPdf && Storage::disk('public')->exists($bulletin->fichierPdf)) {
                Storage::disk('public')->delete($bulletin->fichierPdf);
            }
        });
    }
}