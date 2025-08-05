<?php
namespace App\Solid\Repositories;

use App\Models\Dropdown;
use Illuminate\Support\Facades\DB;

/**
 * Import Interfaces
 */
use Illuminate\Support\Facades\Auth;
use App\Solid\Repositories\Interfaces\DropdownRepositoryInterface;

/**
 * Import Models
 */

class DropdownRepository implements DropdownRepositoryInterface
{
    public function get(){
        return Dropdown::all();
    }
}