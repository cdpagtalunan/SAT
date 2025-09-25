<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatApproval extends Model
{
    use HasFactory;

    protected $table = "sat_approvals";
    protected $connection = "mysql";
}
