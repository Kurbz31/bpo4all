<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $fillable = [
        'payroll_id', 'employee_id', 'base_salary', 'total_sale', 'commission', 'total_salary',
        'total_call_time', 'days_present', 'days_absent', 'days_late', 'days_leave'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
