<?php
namespace App\Solid\Repositories;

use App\Models\User;
use App\Models\RapidxUser;
use App\Models\RapidxUserAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Solid\Repositories\Interfaces\UserRepositoryInterface;


class UserRepository implements UserRepositoryInterface
{
    public function getSATWithRelationsAndCondition(array $relations, array $conditions){
       return User::with($relations)->whereConditions($conditions)->get();
    }

    public function getRapidxWithRelationsAndCondition(array $relations, array $conditions){
        return RapidxUserAccess::with($relations)->whereConditions($conditions)->get();
    }

    public function insert(array $data){
        return User::insert($data);
    }

    public function update(int $id, array $data){
        return User::where('rapidx_emp_id', $id)->update($data);
    }
}