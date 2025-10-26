<?php

use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new \Tobuli\Services\PermissionService())->addToAll('global_gprs_templates');
        (new \Tobuli\Services\PermissionService())->addToAll('global_sms_templates');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
