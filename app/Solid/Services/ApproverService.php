<?php
namespace App\Solid\Services;
use Illuminate\Support\Facades\DB;
use App\Solid\Services\Interfaces\ApproverServiceInterface;
use App\Solid\Repositories\Interfaces\ApproverRepositoryInterface;
use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;
// Service implements the ServiceInterface, responsible for saving users.
use DataTables;
date_default_timezone_set('Asia/Manila');
class ApproverService implements ApproverServiceInterface
{
    
    private $approverRepository;
    private $satHeaderRepository;

    public function __construct(
        ApproverRepositoryInterface $approverRepository,
        SATHeaderRepositoryInterface $satHeaderRepository
    )
    {
        $this->approverRepository = $approverRepository;
        $this->satHeaderRepository = $satHeaderRepository;
    }
    

    public function saveApprover(array $data)
    {
        DB::beginTransaction();
        $data_array = array(
            'emp_id'        => $data['name'],
            'approval_type' => $data['approval_type']
        );
        try{
            if($data['approver_id']){ // Update
                $data_array['updated_by'] = session('rapidx_id');
                $result = $this->approverRepository->update($data['approver_id'],$data_array);

            }
            else{ // Insert
                $data_array['created_by'] = session('rapidx_id');
                $data_array['updated_at'] = now();

                $result = $this->approverRepository->insert($data_array);
            }
            DB::commit();
            return response()->json([
                'result' => $result,
                'msg' => 'Successfully Saved!'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function dtGetApprovers(){
        $relations = array('employeeDetails');
        $conditions = array();
        $approvers = $this->approverRepository->getWithRelationsAndConditions($relations, $conditions);
        
        return DataTables::of($approvers)
        ->addColumn('actions', function($approver){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-sm btn-primary btnEditApprover' title='Edit Approver' data-details='{$approver}'><i class='fas fa-edit'></i></button>";
            $result .= "<button class='btn btn-sm btn-danger ml-1 btnDeleteApprover' title='Delete Approver' data-id='{$approver->id}'><i class='fas fa-trash'></i></button>";
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function deleteApprover(int $id){
        DB::beginTransaction();
        try{
            $approvers = $this->approverRepository->delete($id);
            DB::commit();
            return response()->json([
                'result' => true,
                'msg' => 'successfully deleted!'
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

    public function dtSatApproval(){
        $relations_approver = array(
            'employeeDetails'
        );
        $condition_approver = array(
            'emp_id' => session('employee_number'),
            'deleted_at' => null
        );
        $user_approver = $this->approverRepository->getWithRelationsAndConditions($relations_approver, $condition_approver);
        

        $conditions = array(
            'deleted_at' => null,
        );
        $relations = array(
            'sat_details'
        );
        $approval_list = $this->approverRepository->getApprovalWithRelationAndConditions($relations, $conditions);
        
        $approval_list = collect($approval_list)->where('sat_details.status', 3)->values();
        $result = $approval_list->filter(function ($item) use ($user_approver) {
            return (is_null($item['approver_1']) && $user_approver->contains('approval_type', '1'))
            || (is_null($item['approver_2']) && $user_approver->contains('approval_type', '2'));
        })->values();

        return DataTables::of($approval_list)
        ->addColumn('action', function($approval_list) use ($user_approver){
            $result = "";
            $result .= "<center>";
            $result .= "<button class='btn btn-sm btn-info btnSeeSatDetails mr-1'
            data-id='{$approval_list->sat_header_id}' 
            data-assembly-line='{$approval_list->sat_details->assembly_line}'
            data-device-name='{$approval_list->sat_details->device_name}'
            data-no-of-pins='{$approval_list->sat_details->no_of_pins}'
            data-operation-line='{$approval_list->sat_details->operation_line}'
            data-qsat='{$approval_list->sat_details->qsat}'
            >
            <i class='fa-solid fa-circle-info'></i></button>";
            if(is_null($approval_list->approver_1)){
                $result .= "<button class='btn btn-sm btn-success btnApprove' data-approver='1' data-approve-id='{$approval_list->id}' title='Approve SAT'><i class='fa-solid fa-check'></i></button>";
            }
            else if(is_null($approval_list->approver_2)){
                $result .= "<button class='btn btn-sm btn-success btnApprove' data-approver='2' data-approve-id='{$approval_list->id}' title='Approve SAT'><i class='fa-solid fa-check'></i></button>";
            }
            $result .= "</center>";
            return $result;
        })
        ->addColumn('status', function($approval_list){
            $result = "";
            $result .= "<center>";
            if(is_null($approval_list->approver_1)){
                $result .= " <span class='badge bg-primary'>For Engineering Section Head</span>";
            }
            else if(is_null($approval_list->approver_2)){
                $result .= " <span class='badge bg-primary'>For Production Section Head</span>";
            }
            else{
                $result .= " <span class='badge bg-success'>Approved</span>";
            }
            $result .= "</center>";
            return $result;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function approveSat(array $data){
        DB::beginTransaction();
        try{
            switch ($data['approval_type']) {
                case 1:
                    $update_array = array(
                        'approver_1'    => session('employee_number'),
                        'approver_1_at' => NOW()
                    );
                    $result = $this->approverRepository->updateSatApproval($data['approval_id'], $update_array);
                    $case = 1;
                    break;
                case 2:
                    $update_array = array(
                        'approver_2'    => session('employee_number'),
                        'approver_2_at' => NOW()
                    );
                    $result = $this->approverRepository->updateSatApproval($data['approval_id'], $update_array);
                    $approval_details = $this->approverRepository->getApprovalById($data['approval_id']);

                    $update_array = array(
                        'status' => 4
                    );
                    $this->satHeaderRepository->update($update_array, $approval_details->sat_header_id);
                    $case = 2;
                    break;
                default:
                    $result = false;
                    break;
            }
            DB::commit();
            return response()->json([
                'result' => $result,
                'case' => $case
            ]);
        }catch(xception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
}