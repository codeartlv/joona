<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('admin_users', function (Blueprint $table) {
			$table->id();
			$table->string('first_name', 55)->nullable(false);
			$table->string('last_name', 55)->nullable(false);
			$table->string('email', 128)->unique()->nullable(false);
			$table->string('password');
			$table->string('class')->nullable()->default(null);
			$table->enum('level', ['admin', 'user'])->default('user');
			$table->datetime('logged_at')->nullable()->default(null);
			$table->string('logged_ip', 128)->nullable()->default(null);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('admin_users');
	}
};
