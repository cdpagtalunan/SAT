<?php
namespace App\Solid\Repositories;

use App\Models\OperationLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Import Interfaces
 */
use App\Solid\Repositories\Interfaces\OperationLineRepositoryInterface;

date_default_timezone_set('Asia/Manila');

class OperationLineRepository implements OperationLineRepositoryInterface
{
    public function get(array $conditions){
        return OperationLine::whereConditions($conditions)->get();
    }

    public function insert(array $data){
        return OperationLine::insert($data);
    }

    public function delete(int $id){
        return OperationLine::where('id', $id)->update(['deleted_at' => now()]);
    }
}