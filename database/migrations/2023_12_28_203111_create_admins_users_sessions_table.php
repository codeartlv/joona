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
		Schema::create('admin_users_sessions', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('admin_users')->cascadeOnDelete();
			$table->dateTime("started")->nullable(false);
			$table->dateTime("ended")->nullable(true)->default(null);
			$table->enum("end_reason", ['logout', 'stopped', 'auto'])->nullable(true)->default(null);
			$table->dateTime("last_action")->nullable(true)->default(null);
			$table->string("login_ip", 128)->nullable(true)->default(null);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('admin_users_sessions');
	}
};
