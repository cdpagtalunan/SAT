<?php

namespace App\Models;

use App\Models\RapidxUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $connection = 'mysql';

    public function rapidx_user(){
        return $this->hasOne(RapidxUser::class, 'id', 'rapidx_emp_id');
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
