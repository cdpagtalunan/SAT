<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssemblyLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by'
    ];

    public function scopeWhereConditions($query, array $conditions){
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
    }
}
