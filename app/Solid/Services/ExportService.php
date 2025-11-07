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

    // public function exportSat(int $id){
    //     $relations = array(
    //         'satProcessDetails',
    //         'approverDetails',
    //         'approverDetails.approver1Details' => function($query){
    //             $query->select(['EmpNo','FirstName', 'LastName']);
    //         },
    //         'approverDetails.approver2Details' => function($query){
    //             $query->select(['EmpNo','FirstName', 'LastName']);
    //         },
    //         'validatedByDetails', // Prepared by
    //         'lineBalByDetails' // line balance by
    //     );
    //     $conditions = array(
    //         'id' => $id
    //     );
    //     $sat = $this->satHeaderRepository->getWithRelationsConditions($relations, $conditions);
    //     $sat = collect($sat)->first();

    //     return $sat;
    //     return Excel::download(new ExportSat($sat), "{$sat->device_name} PMI SAT validation.xlsx");

    // }

    public function exportSat(int $id){
        $relations = array(
            'satProcessDetails',
            'approverDetails',
            'approverDetails.approver1Details' => function ($query) {
                $query->select(['EmpNo', 'FirstName', 'LastName']);
            },
            'approverDetails.approver2Details' => function ($query) {
                $query->select(['EmpNo', 'FirstName', 'LastName']);
            },
            'validatedByDetails', // Prepared by
            'lineBalByDetails'    // Line balance by
        );

        $conditions = array('id' => $id);

        $sat = $this->satHeaderRepository->getWithRelationsConditions($relations, $conditions);
        $sat = collect($sat)->first();

        // ✅ Process satProcessDetails the same way as before
        if (!empty($sat->satProcessDetails)) {

            $groupedProcesses = collect($sat->satProcessDetails)
                ->groupBy('process_name')
                ->map(function ($group) {

                    $first = $group->first();

                    // ✅ Build operator + observation list safely
                    $operators = collect($group)->map(function ($proc) {
                        return [
                            'operator' => $proc->operator_name ?? $proc->operator ?? 'N/A',
                            'obs_1'    => $proc->obs_1 ?? null,
                            'obs_2'    => $proc->obs_2 ?? null,
                            'obs_3'    => $proc->obs_3 ?? null,
                            'obs_4'    => $proc->obs_4 ?? null,
                            'obs_5'    => $proc->obs_5 ?? null,
                        ];
                    })->values();

                    // ✅ Compute overall average across all obs
                    $allObs = collect();
                    foreach ($group as $proc) {
                        foreach (['obs_1', 'obs_2', 'obs_3', 'obs_4', 'obs_5'] as $field) {
                            if (isset($proc->$field) && is_numeric($proc->$field)) {
                                $allObs->push($proc->$field);
                            }
                        }
                    }

                    $overallAverage = $allObs->count() ? $allObs->avg() : 0;

                    // ✅ Compute allowance and standard/uph times
                    $allowance = isset($first->allowance) ? (float) $first->allowance : 0;
                    $standard_time = round($overallAverage, 2) * (1 + ($allowance / 100));
                    $uph_time = $standard_time > 0 ? 3600 / round($standard_time, 2) : 0;

                    // ✅ Compute tact_time and lb_uph_time
                    $lb_no_operator = isset($first->lb_no_operator) ? (float) $first->lb_no_operator : null;
                    $tact_time = null;
                    $lb_uph_time = null;

                    if ($overallAverage !== null && $lb_no_operator !== null && $lb_no_operator > 0) {
                        $tact_time = $overallAverage / $lb_no_operator;
                        if ($tact_time > 0) {
                            $lb_uph_time = 3600 / $tact_time;
                        }
                    }

                    return (object)[
                        'id'             => $first->id ?? null,
                        'process_name'   => $first->process_name ?? 'N/A',
                        'allowance'      => $allowance,
                        'attachment'     => $first->attachment ?? null,
                        'average_obs'    => $overallAverage,
                        'standard_time'  => $standard_time,
                        'uph_time'       => $uph_time,
                        'tact_time'      => $tact_time,
                        'lb_uph_time'    => $lb_uph_time,
                        'operators'      => $operators,
                        'lb_no_operator' => $lb_no_operator,
                    ];
                })
                ->values();

            // ✅ Replace satProcessDetails with grouped version
            $sat->satProcessDetails = $groupedProcesses;
        }

        // return $sat;
        return Excel::download(new ExportSat($sat), "{$sat->device_name} PMI SAT validation.xlsx");
    }
}