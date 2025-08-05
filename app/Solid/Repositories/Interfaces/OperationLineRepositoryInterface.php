<?php
namespace App\Solid\Repositories\Interfaces;

interface OperationLineRepositoryInterface
{
    public function get(array $conditions);
    public function insert(array $data);
    public function delete(int $id);
}