<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('project name');
            $table->text('description')->comment('project description');
            $table->unsignedBigInteger('staff_id')->comment('staff id');
            $table->string('original_file_name')->nullable()->comment('original file name');;
            $table->string('file_name')->nullable()->comment('file name');;
            $table->integer('status')->default(0)->comment('0 = inactive, 1 = active, 2 = hold'); // 0 = inactive, 1 = active, 2 = hold
            $table->softDeletes();  // For soft delete
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
