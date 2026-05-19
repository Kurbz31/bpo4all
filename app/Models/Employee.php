<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'campaign_id', 'status'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
