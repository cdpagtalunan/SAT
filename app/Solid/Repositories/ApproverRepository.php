<?php
namespace App\Solid\Repositories;

use App\Models\SatApproval;
use App\Models\ApproverList;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Solid\Repositories\Interfaces\ApproverRepositoryInterface;

class ApproverRepository implements ApproverRepositoryInterface
{
    public function insert(array $data){
        return ApproverList::insert($data);
    }

    public function getWithRelationsAndConditions(array $relations, array $conditions){
        return ApproverList::with($relations)->whereConditions($conditions)->get();
    }
    
    public function update(int $id, array $data){
        return ApproverList::where('id', $id)->update($data);
    }

    public function delete(int $id){
        return ApproverList::where('id', $id)->delete();
    }

    public function insertApproval(array $data){
        return SatApproval::insert($data);
    }

    public function getApprovalWithRelationAndConditions(array $relations, array $conditions){
        return SatApproval::with($relations)->whereConditions($conditions)->get();
    }

    public function updateSatApproval(int $id, array $data){
        return SatApproval::where('id', $id)->update($data);
    }

    public function getApprovalById(int $id){
        return SatApproval::where('id', $id)->first();
        
    }
}