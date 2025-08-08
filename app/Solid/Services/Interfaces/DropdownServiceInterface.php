<?php
namespace App\Solid\Services\Interfaces;

interface DropdownServiceInterface
{
    public function getDropdowns();
    public function dtGetDropdownItems(int $dropdown_id);
    public function saveDropdownItem(array $data);
    public function deleteDropdownItem(array $data);
    public function getDropdownSAT(array $data);
}