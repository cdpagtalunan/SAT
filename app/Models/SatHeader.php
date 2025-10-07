<?php

namespace App\Models;

use App\Models\RapidxUser;
use App\Models\SatApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SatHeader extends Model
{
    use HasFactory;

    public function satProcessDetails(){
        return $this->hasMany(SatProcess::class, 'sat_header_id', 'id');
    }

    public function approverDetails(){
        return $this->hasOne(SatApproval::class, 'sat_header_id', 'id');
    }

    public function validatedByDetails(){
        return $this->hasOne(RapidxUser::class, 'id', 'validated_by');
    }

    public function lineBalByDetails(){
        return $this->hasOne(RapidxUser::class, 'id', 'line_bal_by');
    }
   
    public function ScopeWhereConditions($query, $condition){
        foreach ($condition as $key => $value) {
            if (strpos($key, ':') !== false) {
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
