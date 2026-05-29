<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['campaign_id', 'created_by', 'targets', 'target_role', 'date', 'notes'];

    protected $casts = [
        'targets' => 'array',
        'date' => 'date',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
