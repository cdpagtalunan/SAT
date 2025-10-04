<?php

namespace App\Models;

use App\Models\SystemoneHRIS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SatApproval extends Model
{
    use HasFactory;

    protected $table = "sat_approvals";
    protected $connection = "mysql";

    public function sat_details(){
        return $this->hasOne(SatHeader::class, 'id', 'sat_header_id');
    }

    public function approver1Details(){
        return $this->hasOne(SystemoneHRIS::class, 'EmpNo', 'approver_1');
    }
     public function approver2Details(){
        return $this->hasOne(SystemoneHRIS::class, 'EmpNo', 'approver_2');
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
