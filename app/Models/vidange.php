<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vidange extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxi_id',
        'date',
        'kilometers',
        'cost',
        'notes',
    ];

    public function taxi()
    {
        return $this->belongsTo(Taxi::class);
    }
}
