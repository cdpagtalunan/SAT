<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sat_header_id');
            $table->string('approver_1')->nullable();
            $table->string('approver_1_at')->nullable();
            $table->string('approver_2')->nullable();
            $table->string('approver_2_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_approvals');
    }
}
