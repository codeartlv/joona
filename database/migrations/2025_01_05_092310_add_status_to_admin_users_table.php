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
		Schema::table('admin_users', function (Blueprint $table) {
			$table->enum('status', ['active', 'blocked'])->after('level')->default('active')->nullable(false);
			$table->integer('failed_attempts')->after('status')->default(0)->nullable(false);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('admin_users', function(Blueprint $table) {
			$table->dropColumn('status');
			$table->dropColumn('failed_attempts');
		});
	}
};
