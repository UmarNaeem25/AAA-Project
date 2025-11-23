<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requests()
    {
        return $this->hasMany(ShiftRequest::class);
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = ($value === 0 || $value === '0') ? null : $value;
    }
}
