<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SATRequest;
use App\Solid\Services\SATService;
use App\Solid\Services\DropdownService;
use App\Http\Requests\SATProcessRequest;
use App\Http\Requests\LineBalanceRequest;
use App\Solid\Services\LineBalanceService;

class SATController extends Controller
{
    protected $dropdownService;
    protected $satService;
    protected $lineBalanceService;
    
    public function __construct( 
        DropdownService $dropdownService,
        SATService $satService,
        LineBalanceService $lineBalanceService
    ) {
        $this->dropdownService = $dropdownService;
        $this->satService = $satService;
        $this->lineBalanceService = $lineBalanceService;
    }

    public function getDropdownData(Request $request){
        return $this->dropdownService->getDropdownSAT($request->all());
    }

    public function saveSAT(SATRequest $request){
        $data = $request->filterParameters();
        return $this->satService->saveSAT($data);
    }

    public function dtGetSat(Request $request){
        return $this->satService->dtGetSat();
    }

    public function getSatById(Request $request){
        return $this->satService->getSatDetails($request->id);
    }

    public function proceedObs(Request $request){
        return $this->satService->proceedObs($request->id);
    }

    public function dtGetProcessForObservation(Request $request){
        return $this->satService->dtGetProcessForObservation($request->id);
    }

    public function saveProcessObs(SATProcessRequest $request){
        $data = $request->filterParameters();
        return $this->satService->saveSatProcessObs($data);
    }

    public function doneObs(Request $request){
        return $this->satService->doneObs($request->all());
    }

    public function dtGetProcessForLineBalance(Request $request){
        return $this->satService->dtGetProcessForObservation($request->id);
    }

    public function saveLineBalance(LineBalanceRequest $request){
        $data = $request->exeptTokenParameters();
        return $this->lineBalanceService->saveLineBalance($data);
    }

    public function proceedForApproval(Request $request){
        return $this->satService->proceedApproval($request->sat_id);
    }
}
