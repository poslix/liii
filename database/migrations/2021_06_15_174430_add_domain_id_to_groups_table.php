<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDomainIdToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('link_groups', function (Blueprint $table) {
            $table->integer('domain_id')->index()->nullable();
            $table->string('password')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('clicked_at')->nullable()->index();
            $table->timestamp('delted_at')->nullable()->index();
            $table->bigInteger('clicks_count')->unsigned()->default(0)->index();
            $table->text('utm')->nullable();
            $table->string('type', 20)->index()->default('group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
