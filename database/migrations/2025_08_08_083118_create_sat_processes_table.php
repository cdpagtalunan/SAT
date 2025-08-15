<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sat_header_id');
            $table->string('process_name');
            $table->decimal('allowance',8,2);
            $table->unsignedBigInteger('user_rapidx_id')->nullable();
            $table->decimal('obs_1',8,2)->nullable();
            $table->decimal('obs_2',8,2)->nullable();
            $table->decimal('obs_3',8,2)->nullable();
            $table->decimal('obs_4',8,2)->nullable();
            $table->decimal('obs_5',8,2)->nullable();
            $table->decimal('lb_no_operator',8,2)->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sat_header_id')->references('id')->on('sat_headers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sat_processes');
    }
}
