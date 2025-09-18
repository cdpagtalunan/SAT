<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemoneHRIS extends Model
{
    // use HasFactory;

    protected $table = 'tbl_EmployeeInfo';
    protected $connection = 'mysql_systemone_hris';
    
    // public function scopeWhereConditions($query, $conditions){
    //     foreach($conditions as $field => $value){
    //         $query->where($field, $value);
    //     }
    //     return $query;
    // }

    public function ScopeWhereConditions($query, $condition){
        foreach ($condition as $key => $value) {
            if (strpos($key, ':') !== false) {
                // [$field, $operator] = explode(':', $key, 2);
                // $operator = trim($operator);
                // $query->where($operator, $field, $value);
                [$operator, $field] = explode(':', $key, 2);
                $operator = trim($operator);

                switch ($operator) {
                    case 'IN':
                        $query->whereIn($field, (array) $value);
                        break;
                    case 'NOTIN':
                        $query->whereNotIn($field, (array) $value);
                        break;
                    case 'LIKE':
                        $query->where($field, 'LIKE', "%{$value}%");
                        break;
                    default:
                        $query->where($field, $operator, $value);
                        break;
                }
            }
            else{
                $query->where($key, $value);
            }
        }
    }
}
