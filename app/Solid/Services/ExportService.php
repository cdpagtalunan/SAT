<?php
namespace App\Solid\Services;

use Excel;
use App\Exports\ExportSat;

use App\Solid\Services\Interfaces\ExportServiceInterface;
use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;

date_default_timezone_set("Asia/Manila");
class ExportService implements ExportServiceInterface
{
    protected $satHeaderRepository;
    
    public function __construct( SATHeaderRepositoryInterface $satHeaderRepository) {
        $this->satHeaderRepository = $satHeaderRepository;
    }

    public function exportSat(int $id){
        $relations = array(
            'satProcessDetails',
            'approverDetails',
            'approverDetails.approver1Details' => function($query){
                $query->select(['EmpNo','FirstName', 'LastName']);
            },
            'approverDetails.approver2Details' => function($query){
                $query->select(['EmpNo','FirstName', 'LastName']);
            },
            'validatedByDetails', // Prepared by
            'lineBalByDetails' // line balance by
        );
        $conditions = array(
            'id' => $id
        );
        $sat = $this->satHeaderRepository->getWithRelationsConditions($relations, $conditions);
        $sat = collect($sat)->first();

        // return $sat;
        return Excel::download(new ExportSat($sat), "{$sat->device_name} PMI SAT validation.xlsx");

    }
}