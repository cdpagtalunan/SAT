<?php
namespace App\Solid\Services\Interfaces;

interface CommonServiceInterface
{
    public function getOperators();
    public function getUserList(array $param);
}