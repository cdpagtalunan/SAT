<?php
namespace App\Solid\Repositories\Interfaces;

interface AssemblyLineRepositoryInterface
{
    public function get(array $conditions);
    public function insert(array $data);
    public function delete(int $id);
}