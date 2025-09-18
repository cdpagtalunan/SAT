<?php
namespace App\Solid\Services;
use Illuminate\Support\Facades\DB;
use App\Solid\Services\Interfaces\ApproverServiceInterface;
use App\Solid\Repositories\Interfaces\ApproverRepositoryInterface;
// Service implements the ServiceInterface, responsible for saving users.
use DataTables;
date_default_timezone_set('Asia/Manila');
class ApproverService implements ApproverServiceInterface
{
    
    private $approverRepository;

    public function __construct(ApproverRepositoryInterface $approverRepository)
    {
        $this->approverRepository = $approverRepository;
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
}