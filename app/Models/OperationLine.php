<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationLine extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'created_by'
    ];

    public function scopeWhereConditions($query, array $conditions){
        // This method should implement the logic to filter the OperationLine records based on conditions.
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }
    }
}
