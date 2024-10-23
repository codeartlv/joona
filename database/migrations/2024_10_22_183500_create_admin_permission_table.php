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
		Schema::create('admin_users_permissions', function (Blueprint $table) {
			$table->string('permission', 55);
			$table->unsignedBigInteger('admin_user_id');
			$table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
			$table->primary(['permission', 'admin_user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('admin_users_permissions');
	}
};
