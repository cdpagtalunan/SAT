<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApproverList extends Model
{
    use HasFactory;

    protected $table = "approver_lists";
    protected $connection = "mysql";

    protected $fillable = [
        'emp_id',
        'approval_type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function employeeDetails(){
        return $this->hasOne(SystemoneHRIS::class, 'EmpNo', 'emp_id');
    }

    public function ScopeWhereConditions($query, $condition){
        foreach ($condition as $key => $value) {
            if (strpos($key, ':') !== false) {
                [$field, $operator] = explode(':', $key, 2);
                $operator = trim($operator);
                $query->where($operator, $field, $value);
            }
            else{
                $query->where($key, $value);
            }
        }
    }
}
