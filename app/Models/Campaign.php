<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    public const ATTENDANCE_METHOD_CALL_TIME = 'call_time';
    public const ATTENDANCE_METHOD_PRESENT_ABSENT = 'present_absent';

    protected $fillable = ['name', 'description', 'hours_of_work', 'attendance_method'];

    public static function attendanceMethodOptions(): array
    {
        return [
            self::ATTENDANCE_METHOD_CALL_TIME => 'Call Time',
            self::ATTENDANCE_METHOD_PRESENT_ABSENT => 'Present / Absent',
        ];
    }

    public function attendanceMethodLabel(): string
    {
        return self::attendanceMethodOptions()[$this->attendance_method] ?? 'Not set';
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
