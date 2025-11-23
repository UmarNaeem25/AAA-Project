<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'user_id',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
