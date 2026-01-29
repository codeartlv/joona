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
		Schema::create('admin_notifications', function (Blueprint $table) {
			$table->id();
			$table->string('type', 55)->nullable(false);
			$table->string('notifiable_id', 55)->nullable(false);
			$table->json('data')->nullable(true)->default(null);
			$table->boolean('is_global')->nullable(false)->default(false);
			$table->timestamps();
		});

		Schema::create('admin_notifications_users', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('notification_id')->unsigned()->nullable(false);
			$table->bigInteger('user_id')->unsigned()->nullable(false);
			$table->dateTime('read_at')->nullable(true)->default(null);

			$table->foreign('notification_id')->references('id')->on('admin_notifications')->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreign('user_id')->references('id')->on('admin_users')->cascadeOnDelete()->cascadeOnUpdate();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('admin_notifications');
	}
};
