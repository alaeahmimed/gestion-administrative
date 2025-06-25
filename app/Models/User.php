<?php

namespace App\Models;
use Spatie\Permission\Traits\HasRoles;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

     
    protected $fillable = [
        'nom', 'prenom', 'login', 'motDePasse', 'role'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'motDePasse',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function parentt() {
        return $this->hasOne(Parentt::class);
    }

    public function administrateur() {
        return $this->hasOne(Administrateur::class);
    }

    public function enseignant() {
        return $this->hasOne(Enseignant::class);
    }
// User.php

public function eleves()
{
    return $this->hasOne(Eleve::class, 'parentt_id');
}


    public function notifications()
{
    return $this->belongsToMany(Notification::class)
                ->withPivot('vue','related_id','related_type', 'created_at')
                ->withTimestamps()
                ->orderByPivot('created_at', 'desc');
}

public function fullName()
{
    return $this->first_name . ' ' . $this->last_name; 
}


    public function getAuthPassword()
{
    return $this->motDePasse;
}

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a teacher.
     */
    public function isEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    /**
     * Check if the user is a parent.
     */
    public function isParent(): bool
    {
        return $this->role === 'parent';
    }
}
