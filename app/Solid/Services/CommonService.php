<?php
namespace App\Solid\Services;
use App\Solid\Services\Interfaces\CommonServiceInterface;
use App\Solid\Repositories\Interfaces\SystemoneRepositoryInterface;
// Service implements the ServiceInterface, responsible for saving users.
class CommonService implements CommonServiceInterface
{
    
    private $systemoneRepository;

    public function __construct(SystemoneRepositoryInterface $systemoneRepository)
    {
        $this->systemoneRepository = $systemoneRepository;
    }
    

    public function getOperators()
    {
        $conditions = array(
            'empStatus' => 1, // Active
            // 'fkPosition' => 43, // Operator
        );
        $hris =  $this->systemoneRepository->getHRIS($conditions);

        $operator_details = collect($hris)->map(function($item) {
            return [
                'fullname' => $item->FirstName . ' ' . $item->LastName,
            ];
        })->toArray();

        $subcon =  $this->systemoneRepository->getSubcon($conditions);

        $operator_details_subcon = collect($subcon)->filter(function($item) {
            return !empty($item->EmpNo);
        })->map(function($item) {
            return [
                'fullname' => $item->FirstName . ' ' . $item->LastName,
            ];
        })->values()->toArray();

        return array_merge($operator_details, $operator_details_subcon);
        
    }

    public function getUserList(array $param){
        $conditions = array(
            "IN:fkPosition" => $param['fkPosition'],
            "IN:fkDivision" => $param['fkDivision']
        );

        $users = $this->systemoneRepository->getHRIS($conditions);
        return $users;
    }
}