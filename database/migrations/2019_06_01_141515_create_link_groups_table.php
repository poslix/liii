<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('user_id')->index();
            $table->timestamp('created_at')->index()->nullable();
            $table->timestamp('updated_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_groups');
    }
}
