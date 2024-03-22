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
		Schema::create('admin_permissions_role', function (Blueprint $table) {
			$table->string('permission', 55);
			$table->unsignedBigInteger('role_id');
			$table->foreign('role_id')->references('id')->on('admin_roles')->onDelete('cascade');
			$table->primary(['permission', 'role_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('admin_permissions');
	}
};
