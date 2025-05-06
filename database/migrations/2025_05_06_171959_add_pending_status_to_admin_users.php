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
			$table->enum('status', ['active', 'blocked', 'pending'])->after('level')->default('active')->nullable(false)->change();
			$table->string('hash', 65)->nullable(true)->default(null)->after('failed_attempts');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('admin_users', function(Blueprint $table) {
			$table->enum('status', ['active', 'blocked'])->after('level')->default('active')->nullable(false)->change();
			$table->dropColumn('hash');
		});
	}
};
