<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemoneSubcon extends Model
{
    // use HasFactory;
    protected $table = 'tbl_EmployeeInfo';
    protected $connection = 'mysql_systemone_subcon';

    public function scopeWhereConditions($query, $conditions){
        foreach($conditions as $field => $value){
            $query->where($field, $value);
        }
        return $query;
    }

}
