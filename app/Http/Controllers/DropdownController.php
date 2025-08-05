<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DropdownItemRequest;
use App\Solid\Services\Interfaces\DropdownServiceInterface;

class DropdownController extends Controller
{
    protected $dropdownService;
    
    public function __construct(DropdownServiceInterface $dropdownService) {
        $this->dropdownService = $dropdownService;
    }

    public function dtGetDropdownItems(Request $request){
        return $this->dropdownService->dtGetDropdownItems($request->selected_dropdown_id);
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

    /**
     * Save a dropdown item.
     */
    public function saveDropdownItem(DropdownItemRequest $request){
        $data = $request->filterParameters();

        return $this->dropdownService->saveDropdownItem($data);
    }

    public function deleteDropdownItem(Request $request){
        return $this->dropdownService->deleteDropdownItem($request->all());
    }
}
