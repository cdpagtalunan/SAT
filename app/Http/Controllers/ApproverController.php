<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApproverRequest;
use App\Solid\Services\Interfaces\CommonServiceInterface;
use App\Solid\Services\Interfaces\ApproverServiceInterface;

class ApproverController extends Controller
{
    protected $commonService;
    protected $approverService;
    
    public function __construct(
        CommonServiceInterface $commonService,
        ApproverServiceInterface $approverService
    ) {
        $this->commonService = $commonService;
        $this->approverService = $approverService;
    }

    public function dtGetApproverList(Request $request){
        return $this->approverService->dtGetApprovers();
    }

    public function getUserApprover(Request $request){
        return $this->commonService->getUserList($request->all());
    }

    public function saveApprover(ApproverRequest $request){
        $data = $request->filterParameters();
        return $this->approverService->saveApprover($data);
    }

    public function deleteApprover(Request $request){
        return $this->approverService->deleteApprover($request->ap_id);
    }
}
