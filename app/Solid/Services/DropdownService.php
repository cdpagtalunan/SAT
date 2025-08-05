<?php

namespace App\Solid\Services;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Solid\Services\Interfaces\DropdownServiceInterface;
use App\Solid\Repositories\Interfaces\DropdownRepositoryInterface;

use App\Solid\Repositories\Interfaces\AssemblyLineRepositoryInterface;
use App\Solid\Repositories\Interfaces\OperationLineRepositoryInterface;

class DropdownService implements DropdownServiceInterface
{
    
    private $dropdownRepository;
    private $operationLineRepository;
    private $assemblyLineRepository;

    public function __construct(
        DropdownRepositoryInterface $dropdownRepository,
        OperationLineRepositoryInterface $operationLineRepository,
        AssemblyLineRepositoryInterface $assemblyLineRepository
    )
    {
        $this->dropdownRepository = $dropdownRepository;
        $this->operationLineRepository = $operationLineRepository;
        $this->assemblyLineRepository = $assemblyLineRepository;
    }
    

    public function getDropdowns()
    {
        return $this->dropdownRepository->get();
    }

    public function dtGetDropdownItems(int $dropdown_id){
        $data = "";
        $conditions = array(
            'deleted_at' => null,
        );
        switch ($dropdown_id) {
            case '1': // Operation Line
                $data = $this->operationLineRepository->get($conditions);
                break;
            case '2': // Assembly Line
                $data = $this->assemblyLineRepository->get($conditions);
                break;
            default:
                break;
        }

        return DataTables::of($data)
        ->addColumn('action', function($data){
            $result = "";
            $result = "<center>
                <button class='btn btn-sm btn-danger btnDelete' data-id='{$data->id}'><i class='fa-solid fa-trash'></i></button>
            </center>";
            return $result;
        })
        ->make(true);
    }

    public function saveDropdownItem(array $data){
        date_default_timezone_set('Asia/Manila');
        DB::beginTransaction();

        try{
            $insert_data = array(
                'name' => $data['dropdown_item_name'],
                'created_by' => '1'
            );
            switch($data['dropdown_id']){
                case 1:
                    $result = $this->operationLineRepository->insert($insert_data);
                    break;
                case 2:
                    $result = $this->assemblyLineRepository->insert($insert_data);
                    break;
                default:
                    break;
            }
            DB::commit();

            return response()->json([
                'result' => $result,
                'message' => 'Dropdown item saved successfully.'
            ]);
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function deleteDropdownItem(array $data){
        DB::beginTransaction();
        try{
            switch($data['dropdown_id']){
                case 1:
                    $result = $this->operationLineRepository->delete($data['id']);
                    break;
                case 2:
                    $result = $this->assemblyLineRepository->delete($data['id']);
                    break;
                default:
                    break;
            }
            DB::commit();
            return response()->json([
                'result' => $result
            ]);
        }catch(Exemption $e){
            DB::rollback();
            return $e;
        }
    }

}