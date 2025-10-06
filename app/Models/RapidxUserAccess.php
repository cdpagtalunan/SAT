<?php

namespace App\Models;

use App\Models\RapidxUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RapidxUserAccess extends Model
{
    use HasFactory;

    protected $connection = "mysql_rapidx";
    protected $table = "user_accesses";

    public function rapidx_user(){
        return $this->hasOne(RapidxUser::class, 'id', 'user_id');
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
