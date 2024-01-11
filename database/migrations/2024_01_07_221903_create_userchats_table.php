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
        Schema::create('userchats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger("user_id");
            $table->ipAddress("ip_address")->nullable();
            $table->text('message')->nullable();
            $table->text('response')->nullable();
            $table->boolean("is_failed")->default(1);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userchats');
    }
};
