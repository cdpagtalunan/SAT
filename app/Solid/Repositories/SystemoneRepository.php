<?php
namespace App\Solid\Repositories;

use App\Models\SystemoneHRIS;
use App\Models\SystemoneSubcon;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Solid\Repositories\Interfaces\SystemoneRepositoryInterface;


class SystemoneRepository implements SystemoneRepositoryInterface
{
    public function getHRIS(array $conditions){

        return SystemoneHRIS::WhereConditions($conditions)->get();
    }

    public function getSubcon(array $conditions){
        return SystemoneSubcon::WhereConditions($conditions)->get();
    }
}