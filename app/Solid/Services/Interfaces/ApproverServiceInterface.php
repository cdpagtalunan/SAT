<?php
namespace App\Solid\Services\Interfaces;

interface ApproverServiceInterface
{
    public function saveApprover(array $data);
    public function dtGetApprovers();
    public function deleteApprover(int $id);
    public function dtSatApproval();
    public function approveSat(array $data);
}