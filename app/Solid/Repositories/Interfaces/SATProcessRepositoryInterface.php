<?php
namespace App\Solid\Repositories\Interfaces;

interface SATProcessRepositoryInterface
{
    public function insert(array $data);
    public function delete(array $conditions);
}