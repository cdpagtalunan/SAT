<?php
namespace App\Solid\Repositories\Interfaces;

interface ApproverRepositoryInterface
{
    public function insert(array $data);
    public function getWithRelationsAndConditions(array $relations, array $conditions);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function insertApproval(array $data);
    public function getApprovalWithRelationAndConditions(array $relations, array $conditions);
    public function updateSatApproval(int $id, array $data);
    public function getApprovalById(int $id);
}