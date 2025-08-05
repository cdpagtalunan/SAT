<?php

namespace App\Http\Controllers;

use App\Solid\Services\Interfaces\DropdownServiceInterface;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    protected $dropdownService;
    
    public function __construct( DropdownServiceInterface $dropdownService) {
        $this->dropdownService = $dropdownService;
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
}
