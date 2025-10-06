<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\StatusRequest;
use App\Solid\Services\Interfaces\UserServiceInterface;

class UserController extends Controller
{

    protected $userService;
    
    public function __construct( UserServiceInterface $userService) {
        $this->userService = $userService;
    }
    public function dtGetUsers(Request $request){
        return $this->userService->dtGetUsers();
    }
    
    public function updateStatus(StatusRequest $request){
        $data = $request->filterParameters();
        return $this->userService->updateStatus($data);
    }

  
}
