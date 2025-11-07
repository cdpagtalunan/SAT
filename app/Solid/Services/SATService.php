<?php
namespace App\Solid\Services;

use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Solid\Services\Interfaces\SATServiceInterface;
use App\Solid\Repositories\Interfaces\ApproverRepositoryInterface;
// Service implements the ServiceInterface, responsible for saving users.

use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;
use App\Solid\Repositories\Interfaces\SATProcessRepositoryInterface;
date_default_timezone_set('Asia/Manila');
class SATService implements SATServiceInterface
{
    
    private $satHeaderRepository;
    private $satProcessRepository;
    private $satApproverRepository;

    public function __construct(
        SATHeaderRepositoryInterface $satHeaderRepository,
        SATProcessRepositoryInterface $satProcessRepository,
        ApproverRepositoryInterface $satApproverRepository
    )
    {
        $this->satHeaderRepository = $satHeaderRepository;
        $this->satProcessRepository = $satProcessRepository;
        $this->satApproverRepository = $satApproverRepository;
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

    public function dtGetSat(int $filter){
        $conditions = array(
            'deleted_at' => null
        );
        
        if($filter == 0){
            $conditions['IN:status'] = [0,4];
        }
        else if($filter == 1){
            $conditions['status'] = [1];
        }
        else if($filter == 2){
            $conditions['status'] = [2];
        }
        $relations = array(
            'satProcessDetails'
        );
        $sat = $this->satHeaderRepository->getWithRelationsConditions($relations, $conditions);

        // Filter the SAT collection based on status and created_by conditions
        $sat = collect($sat)->filter(function ($item) {
            if ($item['status'] == 0) {
                return $item['created_by'] == session('rapidx_id');
            }
            // include all status 4
            if ($item['status'] == 4) {
                return true;
            }

            return true;
        })->values();// Reindex the filtered collection to reset the keys
        
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
                    $disabled = '';
                    $msg = "Proceed for Heads Approval";
                    $conditions = array(
                        'sat_header_id' => $data->id
                    );
                    $relations = array();
                    $sat_process = $this->satProcessRepository->getWithRelationsConditions($relations, $conditions);
                    $collections = collect($sat_process)->where('lb_no_operator', null)->count();
                    $result .= "<button class='btn btn-sm btn-primary btnAddLineBalance' data-id='{$data->id}' title='Add Line Balance'><i class='fa-solid fa-circle-info'></i></button>";
                    if($collections == 0){
                        $result .= "<button class='btn btn-sm btn-success btnDoneLineBal ml-1' data-id='{$data->id}' title='Proceed Approval'><i class='fa-solid fa-check'></i></button>";
                    }
                    break;
                case 4:
                    $result .= "<button class='btn btn-info btn-sm btnSeeDetail' data-id='{$data->id}' 
                    data-assy='{$data->assembly_line}' 
                    data-device='{$data->device_name}' 
                    data-no-pins='{$data->no_of_pins}' 
                    data-op-line='{$data->operation_line}' 
                    data-qsat='{$data->qsat}' 
                    title='See Details'><i class='fa-solid fa-circle-info'></i></button>";
                    $result .= "<button class='btn btn-sm btn-secondary btnExport ml-1' data-id='{$data->id}' title='Export SAT'><i class='fa-solid fa-file-excel'></i></button>";
                    break;
                default:
                    $result .= "<button class='btn btn-info btn-sm btnSeeDetail' data-id='{$data->id}' 
                    data-assy='{$data->assembly_line}' 
                    data-device='{$data->device_name}' 
                    data-no-pins='{$data->no_of_pins}' 
                    data-op-line='{$data->operation_line}' 
                    data-qsat='{$data->qsat}' 
                    title='See Details'><i class='fa-solid fa-circle-info'></i></button>";
                    break;
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('raw_status', function($data){
            $result = "";
            $result .= "<center>";
            switch ($data->status) {
                case 0:
                    $result .= "<span class='badge bg-warning'>For Edit</span>";
                    break;
                case 1:
                    $result .= "<span class='badge bg-info'>For Observation</span>";
                    break;
                case 2:
                    $result .= "<span class='badge bg-info'>For Line Balance</span>";
                    break;
                case 3:
                    $conditions = array(
                        'sat_header_id' => $data->id,
                        'deleted_at'    => null
                    );
                    $relations = array();
                    $satApproval = $this->satApproverRepository->getApprovalWithRelationAndConditions($relations, $conditions);
                    $collection = collect($satApproval)->first();
                    if(is_null($collection->approver_1)){
                        $result .= "<span class='badge bg-info'>For Engineering Section Head</span>";
                    }
                    else if (is_null($collection->approver_2)){
                        $result .= "<span class='badge bg-info'>For Production Section Head</span>";
                    }
                    break;
                case 4:
                    $result .= "<span class='badge bg-success'>Approved</span>";
                    break; 
                default:
                    break;
            }
            
            $result .= "</center>";
            return $result;
        })
        ->addColumn('SAT_status', function($data){
            $result = true;
            $sum = 0;
            foreach($data->satProcessDetails AS $process){
                if($process->standard_time == 0){
                    return $result = false;
                }

                $sum = $sum + $process->standard_time;
            }


            if($sum > $data->qsat){
                $result = false;
            }
            return $result;
        })
        ->addColumn('lb_status', function($data){
            $result = true;
            $sum = 0;

            $maxTact = collect($data->satProcessDetails)->max('tact_time');
            $sum_lb_operator = collect($data->satProcessDetails)->sum('lb_no_operator');
            $sum_ave_obs = collect($data->satProcessDetails)->sum('average_obs');
          
            $assySAT = $maxTact * $sum_lb_operator;
            if($assySAT == 0){
                return $result = false;
            }
            $lineBalanceValue = ($sum_ave_obs / $assySAT) * 100;

            $rounded = round($lineBalanceValue,2);

            if($rounded < 85){
                $result = false;
            }
            return $result;
        })
        ->rawColumns(['actions', 'raw_status'])
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

    // public function dtGetProcessForObservation(int $id){
    //     $relations = array(
    //         'rapidxUserDetails'
    //     );
    //     $conditions = array(
    //         "sat_header_id" => $id
    //     );
    //     $sat_processess = $this->satProcessRepository->getWithRelationsConditions($relations, $conditions);
    //     return DataTables::of($sat_processess)
    //     ->setRowId('id')
    //     ->addColumn('actions', function($data){
    //         $result = "";
    //         $result .= "<center>";
    //         $result .= "<button type='button' class='btn btn-sm btn-primary btnAddProcessObs' data-id='{$data->id}'><i class='fa-solid fa-pen'></i></button>";
    //         $result .= "<div id='divButtonProcessObs' class='d-none'>
    //                         <button type='button' class='btn btn-sm btn-success btnSaveProcessObs' data-id='{$data->id}'><i class='fa-solid fa-check'></i></button>
    //                         <button type='button' class='btn btn-sm btn-danger ml-1 btnCancel' data-id='{$data->id}'><i class='fa-solid fa-xmark'></i></button>
    //                     </div>";
    //         $result .= "</center>";
    //         return $result;
    //     })
    //     ->addColumn('attchmnt', function($data){
    //         // Check if there's an attachment first
    //         if (empty($data->attachment)) {
    //             return '<span class="text-muted">No Attachment</span>';
    //         }

    //         // Get extension safely
    //         $ext = pathinfo($data->attachment, PATHINFO_EXTENSION);

    //         // Build full public path (assuming storage:link exists)
    //         $filePath = 'public/storage/file_attachments/' . $data->id.'.'.$ext;

    //         // Return a proper download link
    //         return "<a href='" . asset($filePath) . "' download>Download " . strtoupper($ext) . "</a>";
    //     })
    //     ->addColumn('observed_time', function($data){
    //         // $obsValues = [
    //         //     $data->obs_1,
    //         //     $data->obs_2,
    //         //     $data->obs_3,
    //         //     $data->obs_4,
    //         //     $data->obs_5
    //         // ];

    //         // $filtered = array_filter($obsValues, function ($value) {
    //         //     return $value !== null;
    //         // });

    //         // if(count($filtered) == 0){
    //         //     $average = null;
    //         //     return $average;
    //         // }

    //         // $average = array_sum($filtered) / count($filtered);
    //         // // $data->observed_time = round($average, 2);
    //         // $data->observed_time = $average;
    //         // $round_up = round($average, 2);

    //         // return $round_up;
    //         return round($data->average_obs,2);

    //     })
    //     ->addColumn('nt', function($data){
    //         // $data->nt = null;

    //         // $result = $data->observed_time;
    //         // $data->nt = $result;
    //         // $round_up = round($result, 2);
    //         // return $round_up;
    //         return round($data->average_obs,2);

    //     })
    //     ->addColumn('st', function($data){
    //         // $data->st = null;
    //         // if($data->nt === null){
    //         //     return $data->st;
    //         // }
    //         // $st = $data->nt*(1+($data->allowance / 100));
    //         // $round_up = round($st, 2);
    //         // $data->st = $st;
    //         // return $round_up;
    //         return round($data->standard_time,2);

    //     })
    //     ->addColumn('uph', function($data){
    //         // $data->uph = null;

    //         // if($data->st === null){
    //         //     return $data->uph;
    //         // }
    //         // $uph = 3600/$data->st;
    //         // $round_up = round($uph, 2);
    //         // $data->uph = $uph;
    //         // return $round_up;
    //         return round($data->uph_time,2);
            
    //     })
    //     ->addColumn('tact', function($data){
    //         // $data->tact = null;

    //         // if($data->nt === null || $data->lb_no_operator === null){
    //         //     return $data->tact;
    //         // }

    //         // $tact = $data->nt / $data->lb_no_operator;
    //         // $round_up = round($tact, 2);
    //         // $data->tact = $tact;
    //         // return $round_up;
    //         return round($data->tact_time,2);

    //     })
    //     ->addColumn('lb_uph', function($data){
    //         // $data->lb_uph = null;
    //         // if($data->tact === null){
    //         //     return $data->lb_uph;
    //         // }
    //         // $lb_uph = 3600/$data->tact;
    //         // $round_up = round($lb_uph, 2);
    //         // $data->lb_uph = $lb_uph;
    //         // return $round_up;
    //         return round($data->lb_uph_time,2);

    //     })
    //     ->rawColumns(['actions', 'attchmnt'])
    //     ->make(true);
    // }

    public function dtGetProcessForObservation(int $id)
    {
        $relations = ['rapidxUserDetails'];
        $conditions = ['sat_header_id' => $id];

        $sat_processes = $this->satProcessRepository
            ->getWithRelationsConditions($relations, $conditions)
            ->groupBy('process_name') // Group same process names
            ->map(function ($group) {

                $first = $group->first();

                // $line_bal_no_operator = $group->sum('lb_no_operator');
                // Build operator + observation list
                $operators = collect($group)->map(function ($proc) {
                    return [
                        'operator' => $proc->operator_name,
                        'obs_1' => $proc->obs_1,
                        'obs_2' => $proc->obs_2,
                        'obs_3' => $proc->obs_3,
                        'obs_4' => $proc->obs_4,
                        'obs_5' => $proc->obs_5,
                    ];
                })->values();

                // Compute overall averages from all operators
                $allObs = collect();
                foreach ($group as $proc) {
                    foreach (['obs_1','obs_2','obs_3','obs_4','obs_5'] as $field) {
                        if (is_numeric($proc->$field)) {
                            $allObs->push($proc->$field);
                        }
                    }
                }

                $overallAverage = $allObs->count() ? $allObs->avg() : 0;
                // dd($overallAverage);
                $allowance = (float) $first->allowance;
                $standard_time = round($overallAverage, 2) * (1 + ($allowance / 100));
                $uph_time = $standard_time > 0 ? 3600 / round($standard_time,2) : 0;

                 // --- NEW: tact_time and lb_uph_time using lb_no_operator ---
                // $lb_no_operator = isset($first->lb_no_operator) ? $group->sum('lb_no_operator') : null;
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
                    'id'             => $first->id,
                    'process_name'   => $first->process_name,
                    'allowance'      => $allowance,
                    'attachment'     => $first->attachment,
                    'average_obs'    => $overallAverage,
                    'standard_time'  => $standard_time,
                    'uph_time'       => $uph_time,
                    'tact_time'      => $tact_time,
                    'lb_uph_time'    => $lb_uph_time,
                    'operators'      => $operators,
                    // include lb_no_operator if you want it on frontend
                    'lb_no_operator' => $lb_no_operator,
                ];

            })
            ->values(); // reset array keys

        return DataTables::of($sat_processes)
            ->setRowId('id')
            ->addColumn('actions', function($data){
                return '
                    <center>
                        <button type="button" class="btn btn-sm btn-primary btnAddProcessObs" data-id="'.$data->id.'">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <div id="divButtonProcessObs" class="d-none">
                            <button type="button" class="btn btn-sm btn-success btnSaveProcessObs" data-id="'.$data->id.'">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger ml-1 btnCancel" data-id="'.$data->id.'">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </center>
                ';
            })
            ->addColumn('operator', function($data){
                $html = '';
                foreach ($data->operators as $op) {
                    $html .= '
                        <div class="border rounded p-1 mb-1 bg-light text-center">
                            <strong>'.e($op['operator']).'</strong><br>
                        </div>
                    ';
                }
                
                            // <small>['.$op['obs_1'].', '.$op['obs_2'].', '.$op['obs_3'].', '.$op['obs_4'].', '.$op['obs_5'].']</small>
                return $html;
            })
            ->addColumn('attchmnt', function($data){
                if (empty($data->attachment)) {
                    return '<span class="text-muted">No Attachment</span>';
                }
                $ext = pathinfo($data->attachment, PATHINFO_EXTENSION);
                $filePath = 'public/storage/file_attachments/'.$data->id.'.'.$ext;
                return "<a href='".asset($filePath)."' download>Download ".strtoupper($ext)."</a>";
            })
            ->addColumn('obs1', function($data) { 
                $html = '';
                foreach ($data->operators as $op) {
                    if (is_null($op['obs_1'])) {
                        continue; // Skip null observations
                    }
                    $html .= '
                        <div class="border rounded p-1 mb-1 bg-light text-center">
                            <small>'.$op['obs_1'].'</small>
                        </div>
                    ';
                }
                return $html;
             })
            ->addColumn('obs2', function($data) { 
                $html = '';
                foreach ($data->operators as $op) {
                    if (is_null($op['obs_2'])) {
                        continue; // Skip null observations
                    }
                    $html .= '
                        <div class="border rounded p-1 mb-1 bg-light text-center">
                            <small>'.$op['obs_2'].'</small>
                        </div>
                    ';
                }
                return $html;
             })
            ->addColumn('obs3', function($data) { 
                $html = '';
                foreach ($data->operators as $op) {
                    if (is_null($op['obs_3'])) {
                        continue; // Skip null observations
                    }
                    $html .= '
                        <div class="border rounded p-1 mb-1 bg-light text-center">
                            <small>'.$op['obs_3'].'</small>
                        </div>
                    ';
                }
                return $html;
             })
            ->addColumn('obs4', function($data) { 
                $html = '';
                foreach ($data->operators as $op) {
                    if (is_null($op['obs_4'])) {
                        continue; // Skip null observations
                    }
                    $html .= '
                        <div class="border rounded p-1 mb-1 bg-light text-center">
                            <small>'.$op['obs_4'].'</small>
                        </div>
                    ';
                }
                return $html;
             })
            ->addColumn('obs5', function($data) { 
                $html = '';
                foreach ($data->operators as $op) {
                    if (is_null($op['obs_5'])) {
                        continue; // Skip null observations
                    }
                    $html .= '
                        <div class="border rounded p-1 mb-1 bg-light text-center">
                            <small>'.$op['obs_5'].'</small>
                        </div>
                    ';
                }
                return $html;
             })
            ->addColumn('observed_time', function($data){ return round($data->average_obs,2); })
            ->addColumn('nt', function($data){ return round($data->average_obs,2); })
            ->addColumn('st', function($data){ return round($data->standard_time,2); })
            ->addColumn('uph', function($data){ return round($data->uph_time,2); })
            ->addColumn('lb_no_operator', function($data){ return $data->lb_no_operator; })
            ->addColumn('tact', function($data){ return $data->tact_time !== null ? round($data->tact_time, 2) : 0; })
            ->addColumn('lb_uph', function($data){ return $data->lb_uph_time !== null ? round($data->lb_uph_time, 2) : 0; })
            ->rawColumns(['actions','operator','attchmnt', 'obs1', 'obs2', 'obs3', 'obs4', 'obs5'])
            ->make(true);
    }
    public function saveSatProcessObs(array $data){
        DB::beginTransaction();
        $file     = $data['attachment'];
        $file_name = null;
        try{
            // $file->getClientOriginalName()
            if(!is_null($file)){
                $file_name = $file->getClientOriginalName();
                Storage::putFileAs('public/file_attachments', $file, $data['id'].".".$file->getClientOriginalExtension());
            }

            $observation_data = json_decode($data['observations'], true);

            $original = $this->satProcessRepository->find($data['id']);
             // 🔹 Loop through each observation

            // return response()->json([
            //     'observation_data' => $observation_data,
            //     'original' => $original,
            //     'data_id' => $data['id']
            // ], 500);
            foreach ($observation_data as $index => $obs) {
                $update_array = [
                    'operator_name' => $obs['operator'] ?? null,
                    'attachment'    => $file_name,
                    'obs_1'         => $obs['obs1'] == "" ? null : $obs['obs1'],
                    'obs_2'         => $obs['obs2'] == "" ? null : $obs['obs2'],
                    'obs_3'         => $obs['obs3'] == "" ? null : $obs['obs3'],
                    'obs_4'         => $obs['obs4'] == "" ? null : $obs['obs4'],
                    'obs_5'         => $obs['obs5'] == "" ? null : $obs['obs5'],
                    'updated_at'    => now(),
                    'updated_by'    => session('rapidx_id'),
                ];

                if ($index === 0) {
                    // 🔸 Update the first record
                    $result = $this->satProcessRepository->update($update_array, $data['id']);
                } else {
                    // 🔸 Copy the original and insert a new record
                    $new = $original->replicate();
                    foreach ($update_array as $key => $value) {
                        $new->{$key} = $value;
                    }
                    $new->created_at = now();
                    $new->updated_at = now();
                    $new->created_by = session('rapidx_id');
                    $new->updated_by = session('rapidx_id');
                    $new->save();
                }
            }

            // $update_array = array(
            //     'operator_name' => $data['operator'],
            //     'attachment'    => $file_name,
            //     'obs_1'         => $data['obs1'],
            //     'obs_2'         => $data['obs2'],
            //     'obs_3'         => $data['obs3'],
            //     'obs_4'         => $data['obs4'],
            //     'obs_5'         => $data['obs5'],
            //     'updated_at'    => NOW(),
            //     'updated_by'    => session('rapidx_id'),
            // );

            // Code for multiple user
            // $result = $this->satProcessRepository->update($update_array, $data['id']);
            DB::commit();

            return response()->json([
                'result' => true,
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

    public function proceedApproval(int $satId){
        DB::beginTransaction();
        try{
            $header_update_array = array(
                'status' => 3,
            );
            $this->satHeaderRepository->update($header_update_array, $satId);

            $approval_array = array(
                'sat_header_id' => $satId
            );
            $this->satApproverRepository->insertApproval($approval_array);

            DB::commit();
            return response()->json([
                'result' => true
            ]);
        }catch(Exception $e){
            DB::rollback();
            return $e; 
        }
    }
} 