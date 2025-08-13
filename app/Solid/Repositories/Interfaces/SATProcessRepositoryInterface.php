<?php
namespace App\Solid\Repositories\Interfaces;

interface SATProcessRepositoryInterface
{
    public function insert(array $data);
    public function delete(array $conditions);
    public function getWithRelationsConditions(array $relations, array $conditions);
    public function update(array $data, int $id);
}