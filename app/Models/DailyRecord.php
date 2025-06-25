<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxi_id',
        'date',
        'kilometers',
        'earnings',
        'expenses',
        'notes',
        'Kilometre',
    ];

    protected $casts = [
        'date' => 'date',  // This will automatically cast 'date' as Carbon instance
    ];

    public function taxi()
    {
        return $this->belongsTo(Taxi::class);
    }
}
