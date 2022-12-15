<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Event extends Model
{
    use HasFactory;
    protected $casts = [
        'properties' => "array"
    ];
    protected $dates = ['datetime'];

    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo("App\Models\User");
    }
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id')->withPivot(['invited', 'present']);

        #caso de erro, na linha acima tinha 2 'event_id's. 
    }

    public $timestamps = false; 
}
