<?php
namespace App\Solid\Services;
use Illuminate\Support\Facades\DB;
use App\Solid\Services\Interfaces\SATServiceInterface;
use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;
use App\Solid\Repositories\Interfaces\SATProcessRepositoryInterface;
// Service implements the ServiceInterface, responsible for saving users.

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
    

    public function saveSAT(array $data){
        DB::beginTransaction();
        $header_array = array(
            'device_name' => $data['device_name'],
            'operation_line' => $data['operation_line'],
            'assembly_line' => $data['assembly_line'],
        );
        try{
            if(isset($data['sat_id'])){ // Update

            }
            else{ // Create
                $header_array['created_at'] = now();
                $header_array['created_by'] = session('rapidx_id');
                $satHeaderId = $this->satHeaderRepository->insertGetId($header_array);

                $process_list = array_map(function ($row) use ($satHeaderId) {
                    return $row + [
                        'sat_header_id' => $satHeaderId,
                        'created_by' => session('rapidx_id'),
                        'created_at' => now()
                    ];
                }, $data['process_list']);
                $result = $this->satProcessRepository->insert($process_list);

            }
            return response()->json([
                'result' => $result,
                'msg' => 'Successfully Saved!'
            ]);
            // DB::commit();
        }catch(Exception $e){
            DB::rollback();
            return $e;
        }
    }
}