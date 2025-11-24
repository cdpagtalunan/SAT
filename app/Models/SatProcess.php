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

    protected $appends = ['average_obs', 'standard_time', 'uph_time', 'tact_time', 'lb_uph_time'];


    public function rapidxUserDetails(){
        return $this->hasOne(RapidxUser::class, 'id', 'user_rapidx_id');
    }

    public function satHeader(){
        return $this->belongsTo(SatHeader::class, 'sat_header_id');
    }

    // public function scopeWhereConditions($query, $conditions){
    //     foreach ($conditions as $key => $value) {
    //         $query->where($key, $value);
    //     }
    // }
    

    /**
     * Apply dynamic where conditions.
     *
     * Supported shorthand keys:
     * - LIKE:columnName   => performs a LIKE '%value%' on columnName
     * - IN:columnName     => performs a WHERE IN on columnName (value should be array or comma-separated)
     * - NOTIN:columnName  => performs a WHERE NOT IN on columnName
     * - >:columnName      => uses '>' operator on columnName
     * - <:columnName      => uses '<' operator on columnName
     * - >=:columnName     => uses '>=' operator on columnName
     * - <=:columnName     => uses '<=' operator on columnName
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $condition Conditions to apply to the query.
     */
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

        
    public function getAverageObsAttribute()
    {
        $obsValues = [
            $this->obs_1,
            $this->obs_2,
            $this->obs_3,
            $this->obs_4,
            $this->obs_5,
        ];

        $validValues = array_filter($obsValues, function ($v) {
            return $v !== null && $v !== '' && is_numeric($v);
        });

        if (count($validValues) > 0) {
            return array_sum($validValues) / count($validValues);
        }

        return 0;
    }

    public function getStandardTimeAttribute()
    {
        $average = $this->average_obs ?? $this->getAverageObsAttribute();
        $allowance = (float) $this->allowance;

        return $average * (1 + ($allowance / 100));
    }

    public function getUphTimeAttribute(){
        if($this->standard_time == null || $this->standard_time == 0){
            return null;
        }
        // return $this->standard_time;

        $uph = 3600/$this->standard_time;

        return $uph;
    }

    public function getTactTimeAttribute(){
        if($this->average_obs === null || $this->lb_no_operator === null){
            return null;
        }

        $tact = $this->average_obs / (float) $this->lb_no_operator;
        return $tact;
    }

    public function getLbUphTimeAttribute(){
        if($this->tact_time === null){
            return null;
        }
        $lb_uph = 3600/$this->tact_time;
        return $lb_uph;
    }
}
