<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ParkingTransaction extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'unique_code','license_plate','vehicle_type','entry_time','exit_time','duration_hours','parking_fee','qr_code_path','status','processed_by_entry','processed_by_exit'
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    protected static $logAttributes = [
        'unique_code','license_plate','vehicle_type','status','parking_fee'
    ];
    protected static $logName = 'parking';
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(static::$logAttributes)->useLogName(static::$logName);
    }

    public function entryUser()
    {
        return $this->belongsTo(User::class,'processed_by_entry');
    }

    public function exitUser()
    {
        return $this->belongsTo(User::class,'processed_by_exit');
    }
}
