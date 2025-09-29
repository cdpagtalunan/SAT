<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatApproval extends Model
{
    use HasFactory;

    protected $table = "sat_approvals";
    protected $connection = "mysql";

    public function sat_details(){
        return $this->hasOne(SatHeader::class, 'id', 'sat_header_id');
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
