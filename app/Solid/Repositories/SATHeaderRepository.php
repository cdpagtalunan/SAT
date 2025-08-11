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
}