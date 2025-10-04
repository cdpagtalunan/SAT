<?php
namespace App\Solid\Services;

use Illuminate\Support\Facades\DB;
use App\Solid\Services\Interfaces\LineBalanceServiceInterface;
use App\Solid\Repositories\Interfaces\SATHeaderRepositoryInterface;
use App\Solid\Repositories\Interfaces\SATProcessRepositoryInterface;
// Service implements the ServiceInterface, responsible for saving users.
date_default_timezone_set('Asia/Manila');
class LineBalanceService implements LineBalanceServiceInterface
{
    
    private $satProcess;
    private $satHeader;

    public function __construct(
        SATProcessRepositoryInterface $satProcess,
        SATHeaderRepositoryInterface $satHeader
    )
    {
        $this->satProcess = $satProcess;
        $this->satHeader = $satHeader;
    }
    

    public function saveLineBalance(array $data)
    {
        DB::beginTransaction();
        
        try{
            $header_data_array = array(
                // 'lb_ppc_output_per_hr' => $data['ppc_output_per_hr'],
                'updated_by' => session('rapidx_id'),
                'line_bal_by' => session('rapidx_id'),
                'line_bal_date' => NOW(),
            );
            $this->satHeader->update($header_data_array, $data['sat_header_id']);

            foreach($data['tbl_line_bal'] as $line_balance){
                $sat_line_balance_data_array = array(
                    'lb_no_operator' => $line_balance['noOfOperator'],
                    'updated_by' => session('rapidx_id'),
                );
                $this->satProcess->update($sat_line_balance_data_array, $line_balance['satProcessId']);
            }
            DB::commit();
            return response()->json(['result' => true]);
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
       
    }
}