<?php
namespace App\Solid\Repositories;

use App\Models\SatHeader;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;

class SATHeaderRepository implements SATHeaderRepositoryInterface
{
    public function insertGetId(array $data){
        return SatHeader::insertGetId($data);
    }

    public function getWithRelationsConditions(array $relations, array $conditions){
        return SatHeader::with($relations)->whereConditions($conditions)->get();
    }

    public function getDetailsById(array $relations, int $id){
        return SatHeader::with($relations)->where('id', $id)->first();
    }

    public function update(array $data, int $id){
        return SatHeader::where('id', $id)->update($data);
    }
}