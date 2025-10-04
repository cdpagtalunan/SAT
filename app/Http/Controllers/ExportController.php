<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Solid\Services\Interfaces\ExportServiceInterface;

class ExportController extends Controller
{
    protected $exportService;
    protected $satService;
    
    public function __construct( 
        ExportServiceInterface $exportService
    ) {
        $this->exportService = $exportService;
    }
    public function exportSat(Request $request){
        return $this->exportService->exportSat($request->id);
    }
}
