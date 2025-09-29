<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifySession
{
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

        $request->session()->put('rapidx_id', $_SESSION['rapidx_user_id']);
        $request->session()->put('employee_number', $_SESSION['rapidx_employee_number']);
        return $next($request);
    }
}
