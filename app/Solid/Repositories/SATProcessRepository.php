<?php
namespace App\Solid\Repositories;

use App\Models\SatProcess;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Repositories\Interfaces\SATProcessRepositoryInterface;


class SATProcessRepository implements SATProcessRepositoryInterface
{
    public function insert(array $data){
        return SatProcess::insert($data);
    }

    public function delete(array $conditions){
        return SatProcess::whereConditions($conditions)->delete();
    }

    public function getWithRelationsConditions(array $relations, array $conditions){
        return SatProcess::with($relations)->whereConditions($conditions)->get();
    }

    public function update(array $data, int $id){
        return SatProcess::where('id', $id)->update($data);
    }
}