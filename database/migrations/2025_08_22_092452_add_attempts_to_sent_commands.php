<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tobuli\Traits\DatabaseRunChangesTrait;

return new class extends Migration
{
    use DatabaseRunChangesTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('sent_commands', 'attempts')) {
            return;
        }

        Schema::table('sent_commands', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->nullable()->after('status');

            $this->addIndexIfNotExists('sent_commands', 'status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('sent_commands', 'attempts')) {
            return;
        }

        Schema::table('sent_commands', function (Blueprint $table) {
            $table->dropColumn('attempts');

            $this->dropIndexIfExists('sent_commands', 'status');
        });
    }
};
