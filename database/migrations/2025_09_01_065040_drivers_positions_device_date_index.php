<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use \Tobuli\Traits\DatabaseRunChangesTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addIndexIfNotExists('user_driver_position_pivot', ['device_id', 'date']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropIndexIfExists('user_driver_position_pivot', ['device_id', 'date']);
    }
};
