<?php
namespace App\Solid\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function getSATWithRelationsAndCondition(array $relations, array $conditions);
    public function getRapidxWithRelationsAndCondition(array $relations, array $conditions);
    public function insert(array $data);
    public function update(int $id, array $data);
}