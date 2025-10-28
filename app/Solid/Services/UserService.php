<?php
namespace App\Solid\Services;

use DataTables;
use Illuminate\Support\Facades\DB;
// Service implements the ServiceInterface, responsible for saving users.
use App\Solid\Services\Interfaces\UserServiceInterface;
use App\Solid\Repositories\Interfaces\UserRepositoryInterface;

date_default_timezone_set('Asia/Manila');
class UserService implements UserServiceInterface
{
    protected $userRepository;
    
    public function __construct( UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }
    public function dtGetUsers(){
        $relations = array('rapidx_user');
        $conditions = array();
        $sat_users = $this->userRepository->getSATWithRelationsAndCondition($relations, $conditions);

        $rapidx_relations = array('rapidx_user');
        $rapidx_conditions = array(
            'module_id' => 47
        );
        $rapidx_user = $this->userRepository->getRapidxWithRelationsAndCondition($rapidx_relations, $rapidx_conditions);

   

        // Merging logic
        // Step 1: get all rapidx_emp_id values
        $empIds = $sat_users->pluck('rapidx_emp_id');
        // Step 2: filter out matching rapidx_user entries
        $filteredRapidX = $rapidx_user->reject(function ($user) use ($empIds) {
            return $empIds->contains($user['user_id']);
        });
        // Step 3: merge both arrays
        // $merged = $sat_users->merge($filteredRapidX)->values();
        $merged = $sat_users->concat($filteredRapidX)->values();


        return DataTables::of($merged)
        ->addColumn('btn_checker', function($merged){
            $result = "";

            if(!isset($merged->checker)){
                $result .= "<button class='btn btn-sm btn-danger btnChecker' data-rapidx-id='{$merged->rapidx_user->id}' data-status='1' data-type='create' title='Set Checker'><i class='fa-solid fa-times'></i></button>";
            }
            else{
                if($merged->checker == 0){
                    $result .= "<button class='btn btn-sm btn-danger btnChecker' data-rapidx-id='{$merged->rapidx_user->id}' data-status='1' data-type='update' title='Set Checker'><i class='fa-solid fa-times'></i></button>";
                }
                else{
                    $result .= "<button class='btn btn-sm btn-success btnChecker' data-rapidx-id='{$merged->rapidx_user->id}' data-status='0' data-type='update' title='Unset Checker'><i class='fa-solid fa-check'></i></button>";

                }
            }

            return $result;
        })
        ->addColumn('btn_admin', function($merged){
            $result = "";
            if(!isset($merged->admin)){
                $result .= "<button class='btn btn-sm btn-danger btnAdmin' data-rapidx-id='{$merged->rapidx_user->id}' data-status='1' data-type='create' title='Set Admin'><i class='fa-solid fa-times'></i></button>";
            }
            else{
                if($merged->admin == 0){
                    $result .= "<button class='btn btn-sm btn-danger btnAdmin' data-rapidx-id='{$merged->rapidx_user->id}' data-status='1' data-type='update' title='Set Admin'><i class='fa-solid fa-times'></i></button>";
                }
                else{
                    $result .= "<button class='btn btn-sm btn-success btnAdmin' data-rapidx-id='{$merged->rapidx_user->id}' data-status='0' data-type='update' title='Unset Admin'><i class='fa-solid fa-check'></i></button>";
                }
            }
            return $result;
        })
        ->rawColumns(['btn_checker', 'btn_admin'])
        ->make(true);
    }

    public function updateStatus(array $data){
        DB::beginTransaction();
        // return $data;
        
        try{
            $status_array = array(
                $data['fn'] => $data['status']
            );
            if($data['type'] == 'create'){
                $status_array['rapidx_emp_id'] = $data['rapidx_id'];
                $status_array['created_by'] = session('rapidx_id');
                $status_array['created_at'] = now();
                $this->userRepository->insert($status_array);
            }
            else{
                $status_array['updated_by'] = session('rapidx_id');
                $this->userRepository->update($data['rapidx_id'],$status_array);
            }
            
            DB::commit();
            return response()->json([
                'result' => true,
            ]);
        }catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    // public function saveAdmin(array $data){
    //     DB::beginTransaction();
        
    //     try{
    //         $admin_array = array(
    //             'admin' => $data['status']
    //         );
    //         if($data['type'] == 'create'){
    //             $admin_array['rapidx_emp_id'] = $data['rapidx_id'];
    //             $admin_array['created_by'] = session('rapidx_id');
    //             $admin_array['created_at'] = now();
    //             $this->userRepository->insert($admin_array);
    //         }
    //         else{
    //             $admin_array['updated_by'] = session('rapidx_id');
    //             $this->userRepository->update($data['rapidx_id'],$admin_array);
    //         }
            
    //         DB::commit();
    //         return response()->json([
    //             'result' => true,
    //         ]);
    //     }catch(Exception $e){
    //         DB::rollback();
    //         return $e->getMessage();
    //     }
    // }
}
