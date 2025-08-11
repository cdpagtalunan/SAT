<?php
namespace App\Solid\Repositories\Interfaces;

interface SATHeaderRepositoryInterface
{
    public function insertGetId(array $data);
    public function getWithRelationsConditions(array $relations, array $conditions);
    public function getDetailsById(array $relations, int $id);
    public function update(array $data, int $id);
}