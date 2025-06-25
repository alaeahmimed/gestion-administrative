<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model {
    use HasFactory;

    protected $fillable = ['message', 'date','sender_id','receiver_id',"type"];
    protected $casts = [
        'date' => 'datetime',
        'metadata' => 'array'
    ];
    
    public function getDataAttribute()
    {
        return json_decode($this->attributes['message'], true) ?? [
            'type' => 'simple',
            'message' => $this->attributes['message']
        ];
    }
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('vue')
                    ->withTimestamps();
    }
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}