<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // INT PK, Auto-Increment

            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->enum('gender', ['male', 'female', 'other']);

            $table->string('country', 80)->index();
            $table->string('department', 100)->nullable();
            $table->string('designation', 100)->nullable();

            $table->dateTime('signup_date')->index();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
