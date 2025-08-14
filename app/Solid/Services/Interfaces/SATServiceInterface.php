<?php
namespace App\Solid\Services\Interfaces;

interface SATServiceInterface
{
    public function saveSAT(array $data);
    public function dtGetSat();
    public function getSatDetails(int $id);
    public function proceedObs(int $id);
    public function dtGetProcessForObservation(int $id);
    public function saveSatProcessObs(array $id);
    public function doneObs(array $data);
}