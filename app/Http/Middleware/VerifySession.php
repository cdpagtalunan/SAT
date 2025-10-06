<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Solid\Repositories\Interfaces\UserRepositoryInterface;
use App\Solid\Repositories\Interfaces\ApproverRepositoryInterface;

class VerifySession
{
    protected $approverRepository;
    protected $userRepository;
    
    public function __construct( 
        ApproverRepositoryInterface $approverRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->approverRepository = $approverRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        session_start();
        if ($_SESSION) {
            $moduleIds = array_map(function($access) {
                return $access['module_id'];
            }, $_SESSION['rapidx_user_accesses']);
            if(!in_array(47, $moduleIds)){
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
                // Otherwise, do a normal redirect
                session()->flush();
                return redirect('../');
            }
        }
        else{
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            session()->flush();
            // Otherwise, do a normal redirect
            return redirect('../');
        }
        $request->session()->put('is_approver', false);
        $request->session()->put('is_admin', false);
        $request->session()->put('is_checker', false);


        $approver = $this->approverRepository->getWithRelationsAndConditions(array(), [
            'emp_id' => $_SESSION['rapidx_employee_number'],
            'deleted_at' => null
        ])->first();

        $conditions= array(
            'rapidx_emp_id' => $_SESSION['rapidx_user_id'],
            'deleted_at' => null
        );
        $user_access_details = $this->userRepository->getSATWithRelationsAndCondition(array(), $conditions)->first();

        if(isset($user_access_details)){
            if($user_access_details->admin == 1){
                $request->session()->put('is_admin', true);
            }
            if($user_access_details->checker == 1){
                $request->session()->put('is_checker', true);
            }
        }
        if($approver){
            $request->session()->put('is_approver', true);
        }
        $request->session()->put('rapidx_id', $_SESSION['rapidx_user_id']);
        $request->session()->put('employee_number', $_SESSION['rapidx_employee_number']);

        return $next($request);
    }
}
