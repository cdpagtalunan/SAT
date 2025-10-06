<?php
namespace App\Solid\Services\Interfaces;

interface UserServiceInterface
{
    public function dtGetUsers();
    public function updateStatus(array $data);
}