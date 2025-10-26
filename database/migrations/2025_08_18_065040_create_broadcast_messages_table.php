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
        if (Schema::hasTable('broadcast_messages')) {
            return;
        }

        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->string('channel');
            $table->unsignedTinyInteger('status')->default(0);
            $table->string('title')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('content')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->unsignedInteger('total');
            $table->unsignedInteger('success')->default(0);
            $table->unsignedInteger('fail')->default(0);
            $table->text('filters');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('broadcast_messages');
    }
};
