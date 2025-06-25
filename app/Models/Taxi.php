<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxi extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'model',
        'year',
        'calculation_type',
        'fixed_daily_price',
        'price_per_km',
        'notes',
        'is_active',
    ];

    public function dailyRecords()
    {
        return $this->hasMany(DailyRecord::class);
    }
    public function vidanges()
{
    return $this->hasMany(Vidange::class);
}

}
