<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SATRequest;
use App\Solid\Services\SATService;
use App\Solid\Services\DropdownService;

class SATController extends Controller
{
    protected $dropdownService;
    protected $satService;
    
    public function __construct( 
        DropdownService $dropdownService,
        SATService $satService
    ) {
        $this->dropdownService = $dropdownService;
        $this->satService = $satService;
    }

    public function getDropdownData(Request $request){
        return $this->dropdownService->getDropdownSAT($request->all());
    }

    public function saveSAT(SATRequest $request){
        $data = $request->filterParameters();
        return $this->satService->saveSAT($data);
    }
}
