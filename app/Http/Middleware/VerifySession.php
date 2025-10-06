<?php

namespace App\Http\Middleware;

use App\Solid\Repositories\Interfaces\ApproverRepositoryInterface;
use Closure;
use Illuminate\Http\Request;

class VerifySession
{
    protected $approverRepository;
    
    public function __construct( ApproverRepositoryInterface $approverRepository) {
        $this->approverRepository = $approverRepository;
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

        $approver = $this->approverRepository->getWithRelationsAndConditions(array(), [
            'emp_id' => $_SESSION['rapidx_employee_number'],
            'deleted_at' => null
        ])->first();

        $request->session()->put('is_approver', false);
        if($approver){
            $request->session()->put('is_approver', true);
        }
        $request->session()->put('rapidx_id', $_SESSION['rapidx_user_id']);
        $request->session()->put('employee_number', $_SESSION['rapidx_employee_number']);

        return $next($request);
    }
}
