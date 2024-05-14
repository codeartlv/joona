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
		Schema::create('admin_users_log', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('admin_users')->cascadeOnDelete();
			$table->foreignId('session_id')->constrained('admin_users_sessions')->cascadeOnDelete();
			$table->string("action", 255)->nullable(false);
			$table->string("category", 55)->nullable(false);
			$table->string("object_id", 55)->nullable(true)->default(null);
			$table->text("parameters")->nullable(true)->default(null);
			$table->string("ua", 255)->nullable(true)->default(null);
			$table->index(["category", "object_id"]);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users_log');
	}
};
