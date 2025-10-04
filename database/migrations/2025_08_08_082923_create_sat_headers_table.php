<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_headers', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('status')->default(0)->comment="0-For edit, 1-Observation, 2-Line balance, 3-Approval, 4-Done";
            $table->string('device_name');
            $table->string('operation_line');
            $table->string('assembly_line');
            $table->integer('no_of_pins');
            $table->decimal('qsat', 10, 2);
            $table->string('validated_by')->nullable();
            $table->string('validated_at')->nullable();
            $table->string('line_bal_by')->nullable();
            $table->string('line_bal_at')->nullable();
            $table->decimal('lb_ppc_output_per_hr',8,2)->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('sat_headers');
    }
}
