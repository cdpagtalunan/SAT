<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Solid\Services\Interfaces\CommonServiceInterface;
use App\Solid\Services\Interfaces\DropdownServiceInterface;

class CommonController extends Controller
{
    protected $dropdownService;
    protected $commonService;
    
    public function __construct( 
        DropdownServiceInterface $dropdownService,
        CommonServiceInterface $commonService
    ) {
        $this->dropdownService = $dropdownService;
        $this->commonService = $commonService;
    }
    /**
     * Display the dropdown list.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
    */
    public function getDropdownList(Request $request){
        return $this->dropdownService->getDropdowns();
    }

    public function getOperatorList(Request $request){
        return $this->commonService->getOperators();
    }

}
 