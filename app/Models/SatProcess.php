<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'sat_header_id',
        'process_name',
        'allowance',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function satHeader(){
        return $this->belongsTo(SatHeader::class, 'sat_header_id');
    }

    public function scopeWhereConditions($query, $conditions){
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
    }
}
