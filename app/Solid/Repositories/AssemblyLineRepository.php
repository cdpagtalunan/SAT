<?php
namespace App\Solid\Repositories;

use App\Models\AssemblyLine;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Repositories\Interfaces\AssemblyLineRepositoryInterface;

date_default_timezone_set('Asia/Manila');
class AssemblyLineRepository implements AssemblyLineRepositoryInterface
{
    public function get(array $conditions){
        return AssemblyLine::whereConditions($conditions)->get();
    }

    public function insert(array $data){
        date_default_timezone_set('Asia/Manila');
        return AssemblyLine::create($data);
    }

    public function delete(int $id){
        return AssemblyLine::where('id', $id)->update(['deleted_at' => now()]);
    }
}