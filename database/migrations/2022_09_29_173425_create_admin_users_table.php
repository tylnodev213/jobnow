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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->comment('メールアドレス');
            $table->string('password', 128)->comment('パスワード');
            $table->string('name', 255)->comment('名前');
            $table->char('role', 1)->comment('権限');
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
        Schema::dropIfExists('admin_users');
    }
};
