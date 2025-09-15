<?php
namespace App\Solid\Services;
use Illuminate\Support\Facades\DB;
use App\Solid\Services\Interfaces\SATServiceInterface;
use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;
use App\Solid\Repositories\Interfaces\SATProcessRepositoryInterface;
// Service implements the ServiceInterface, responsible for saving users.

use DataTables;
date_default_timezone_set('Asia/Manila');
class SATService implements SATServiceInterface
{
    
    private $satHeaderRepository;
    private $satProcessRepository;

    public function __construct(
        SATHeaderRepositoryInterface $satHeaderRepository,
        SATProcessRepositoryInterface $satProcessRepository
    )
    {
        $this->satHeaderRepository = $satHeaderRepository;
        $this->satProcessRepository = $satProcessRepository;
    }
    
    /**
     * Saves or updates SAT header and process records within a database transaction.
     * - If 'sat_id' is present, updates the header and replaces all related process records.
     * - If 'sat_id' is not present, creates a new header and inserts related process records.
     * - Adds audit fields (created_by, updated_by, timestamps) automatically.
     * - Returns a JSON response on success, or the exception on failure.
     */
    public function saveSAT(array $data){
        DB::beginTransaction();
        $header_array = array(
            'device_name'    => $data['device_name'],
            'operation_line' => $data['operation_line'],
            'assembly_line'  => $data['assembly_line'],
            'no_of_pins'     => $data['no_of_pins'],
            'qsat'           => $data['qsat'],
        );
        try{
            if(isset($data['sat_id'])){ // Update
                $header_array['updated_at'] = now();
                $header_array['updated_by'] = session('rapidx_id');
                $this->satHeaderRepository->update($header_array, $data['sat_id']);

                $delete_condition = array(
                    'sat_header_id' => $data['sat_id']
                );
                $this->satProcessRepository->delete($delete_condition);
                
                // Prepare the process list for bulk insert by adding audit fields and the current SAT header ID to each row.
                // This ensures each process record is linked to the correct SAT header and includes updated_by/updated_at info.
                $process_list = array_map(function ($row) use ($data) {
                    return $row + [
                        'sat_header_id' => $data['sat_id'],
                        'created_by' => session('rapidx_id'),
                        'created_at' => now()
                    ];
                }, $data['process_list']);
                $result = $this->satProcessRepository->insert($process_list);
            }
            else{ // Create
                $header_array['created_at'] = now();
                $header_array['created_by'] = session('rapidx_id');
                $satHeaderId = $this->satHeaderRepository->insertGetId($header_array);

                // Prepare the process list for bulk insert by adding audit fields and the current SAT header ID to each row.
                // This ensures each process record is linked to the correct SAT header and includes created_by/created_at info.
                $process_list = array_map(function ($row) use ($satHeaderId) {
                    return $row + [
                        'sat_header_id' => $satHeaderId,
                        'created_by' => session('rapidx_id'),
                        'created_at' => now()
                    ];
                }, $data['process_list']);
                $result = $this->satProcessRepository->insert($process_list);

            }
            DB::commit();
            return response()->json([
                'result' => $result,
                'msg' => 'Successfully Saved!'
            ]);
        }catch(Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function dtGetSat(){
        $conditions = array(
            'deleted_at' => null
        );
        $relations = array();
        $sat = $this->satHeaderRepository->getWithRelationsConditions($relations, $conditions);
        return DataTables::of($sat)
        ->addColumn('actions', function($data){
            $result = "";
            $result .= "<center>";
            switch ($data->status) {
                case 0:
                    $result .= "<button class='btn btn-sm btn-primary btnEditSAT' data-id='{$data->id}' title='Edit SAT'><i class='fa-solid fa-edit'></i></button>";
                    $result .= "<button class='btn btn-sm btn-warning btnProceedObs ml-1' data-id='{$data->id}' title='Proceed for observation'><i class='fa-solid fa-paper-plane'></i></button>";
                    break;
                case 1:
                    $result .= "<button class='btn btn-sm btn-primary btnAddObs' data-id='{$data->id}' title='Add observation'><i class='fa-solid fa-list-check'></i></button>";
                    $result .= "<button class='btn btn-sm btn-success btnDoneObs ml-1' data-id='{$data->id}' title='Proceed for line balance'><i class='fa-solid fa-check'></i></button>";
                    break;
                case 2:
                    $result .= "<button class='btn btn-sm btn-primary btnAddLineBalance' data-id='{$data->id}' title='Add Line Balance'><i class='fa-solid fa-circle-info'></i></button>";
                    break;
                default:
                    # code...
                    break;
            }
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function getSatDetails(int $id){
        $relations = array('satProcessDetails');
        return $this->satHeaderRepository->getDetailsById($relations, $id);
    }

    public function proceedObs(int $id){
        DB::beginTransaction();
        
        try{
            $header_array = array(
                'status' => 1,
                'updated_by' => session('rapidx_id')
            );
            $result = $this->satHeaderRepository->update($header_array, $id);
            DB::commit();
            return response()->json([
                'result' => $result,
                'msg' => 'Transaction Success!'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function dtGetProcessForObservation(int $id){
        $relations = array(
            'rapidxUserDetails'
        );
        $conditions = array(
            "sat_header_id" => $id
        );
        $sat_processess = $this->satProcessRepository->getWithRelationsConditions($relations, $conditions);
        return DataTables::of($sat_processess)
        ->setRowId('id')
        ->addColumn('actions', function($data){
            $result = "";
            $result .= "<center>";
            $result .= "<button type='button' class='btn btn-sm btn-primary btnAddProcessObs' data-id='{$data->id}'><i class='fa-solid fa-pen'></i></button>";
            $result .= "<div id='divButtonProcessObs' class='d-none'>
                            <button type='button' class='btn btn-sm btn-success btnSaveProcessObs' data-id='{$data->id}'><i class='fa-solid fa-check'></i></button>
                            <button type='button' class='btn btn-sm btn-danger ml-1 btnCancel' data-id='{$data->id}'><i class='fa-solid fa-xmark'></i></button>
                        </div>";
            $result .= "</center>";
            return $result;
        })
        ->addColumn('observed_time', function($data){
            $obsValues = [
                $data->obs_1,
                $data->obs_2,
                $data->obs_3,
                $data->obs_4,
                $data->obs_5
            ];

            $filtered = array_filter($obsValues, function ($value) {
                return $value !== null;
            });

            if(count($filtered) == 0){
                $average = null;
                return $average;
            }

            $average = array_sum($filtered) / count($filtered);
            // $data->observed_time = round($average, 2);
            $data->observed_time = $average;
            $round_up = round($average, 2);
            return $round_up;

        })
        ->addColumn('nt', function($data){
            $data->nt = null;

            $result = $data->observed_time;
            $data->nt = $result;
            $round_up = round($result, 2);
            return $round_up;
        })
        ->addColumn('st', function($data){
            $data->st = null;
            if($data->nt === null){
                return $data->st;
            }
            $st = $data->nt*(1+($data->allowance / 100));
            $round_up = round($st, 2);
            $data->st = $st;
            return $round_up;
        })
        ->addColumn('uph', function($data){
            $data->uph = null;

            if($data->st === null){
                return $data->uph;
            }
            $uph = 3600/$data->st;
            $round_up = round($uph, 2);
            $data->uph = $uph;
            return $round_up;
        })
        ->addColumn('tact', function($data){
            $data->tact = null;

            if($data->nt === null || $data->lb_no_operator === null){
                return $data->tact;
            }

            $tact = $data->nt / $data->lb_no_operator;
            $round_up = round($tact, 2);
            $data->tact = $tact;
            return $round_up;
        })
        ->addColumn('lb_uph', function($data){
            $data->lb_uph = null;
            if($data->tact === null){
                return $data->lb_uph;
            }
            $lb_uph = 3600/$data->tact;
            $round_up = round($lb_uph, 2);
            $data->lb_uph = $lb_uph;
            return $round_up;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function saveSatProcessObs(array $data){
        DB::beginTransaction();
        try{
            $update_array = array(
                'operator_name' => $data['operator'],
                'obs_1'         => $data['obs1'],
                'obs_2'         => $data['obs2'],
                'obs_3'         => $data['obs3'],
                'obs_4'         => $data['obs4'],
                'obs_5'         => $data['obs5'],
                'updated_at'    => NOW(),
                'updated_by'    => session('rapidx_id'),
            );
            $result = $this->satProcessRepository->update($update_array, $data['id']);
            DB::commit();

            return response()->json([
                'result' => $result,
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function doneObs(array $data){
        DB::beginTransaction();
        try{
            $sat_process_relations = array();
            $sat_process_conditions = array(
                'sat_header_id' => $data['sat_id']
            );
            // $sat_header = $this->satHeaderRepository->getDetailsById($sat_header_relations, $data['sat_id']);
            $sat_process = $this->satProcessRepository->getWithRelationsConditions($sat_process_relations, $sat_process_conditions);
            $collection = collect($sat_process)->filter(function($process){
                return is_null($process['obs_1']);
            });
            if(count($collection) != 0){
                return response()->json([
                    'result' => false,
                    'msg' => "Please complete observation process"
                ], 409);
            }

            $sat_update = array(
                'status' => 2,
                'validated_by' => session('rapidx_id'),
                'validated_at' => now()
            );
            $result = $this->satHeaderRepository->update($sat_update, $data['sat_id']);
            DB::commit();
            return response()->json([
                'result' => $result,
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }
}