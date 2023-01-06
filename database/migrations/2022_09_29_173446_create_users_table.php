<?php

use Illuminate\Database\Migrations\Migration;
use Core\Providers\Facades\Schema\CustomBlueprint as Blueprint;
use Core\Providers\Facades\Schema\CustomSchema as Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->comment('メールアドレス');
            $table->string('password', 128)->comment('パスワード');
            $table->string('last_name', 255)->comment('名前（姓）');
            $table->string('first_name', 255)->comment('名前（名）');
            $table->string('avatar', 255)->nullable()->comment('写真');
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
};
