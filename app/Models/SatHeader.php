<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatHeader extends Model
{
    use HasFactory;

    public function satProcessDetails(){
        return $this->hasMany(SatProcess::class, 'sat_header_id', 'id');
    }

    public function scopeWhereConditions($query, array $conditions){
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
    }
}
