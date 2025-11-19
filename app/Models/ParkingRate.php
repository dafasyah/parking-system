<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParkingRate extends Model
{
    use HasFactory;
    protected $fillable = ['vehicle_type','hourly_rate'];
}
