<?php
namespace App\Solid\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Import Interfaces
 */
use App\Solid\Repositories\Interfaces\ApproverRepositoryInterface;
use App\Models\ApproverList;

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
}