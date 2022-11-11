<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
			$table->string('user_id',20);
            $table->string('username',50);
			$table->string('email',50)->unique();
			$table->string('nama',100);
			$table->string('no_ponsel',15);
			$table->string('alamat',200);
			$table->string('password',50);
			$table->integer('agent_id')->unsigned();
			$table->string('images',100);
            $table->string('api_token');
			$table->string('created_by',100)->nullable();
			$table->string('updated_by',100)->nullable();
			$table->rememberToken();
            $table->timestamps();
			$table->softDeletes();
        });
		
		Schema::table( 'users', function ( Blueprint $table ) {
			$table->foreign( 'agent_id' )->references( 'id' )->on( 'agent' );
		} );
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
}
