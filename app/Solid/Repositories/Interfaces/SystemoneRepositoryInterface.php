<?php
namespace App\Solid\Repositories\Interfaces;

interface SystemoneRepositoryInterface
{
    public function getHRIS(array $conditions);
    public function getSubcon(array $conditions);
}