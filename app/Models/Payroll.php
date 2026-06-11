<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = ['campaign_id', 'year', 'month', 'period'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
