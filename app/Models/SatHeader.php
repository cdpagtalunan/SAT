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
    public function scopeWhereConditions($query, array $conditions){
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
    }
}
