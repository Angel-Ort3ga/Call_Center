
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
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            // Información personal
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('username', 50)->unique();

            // Acceso
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Información laboral
            $table->string('telefono', 20)->nullable();
            $table->string('extension', 10)->nullable();
            $table->string('foto')->nullable();

            // Relaciones
            // Las claves foráneas se agregan en una migración posterior.
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('departamento_id')->nullable();

            // Estado
            $table->boolean('activo')->default(true);
            $table->integer('new_notifications')->default(0);
            $table->timestamp('ultimo_acceso')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
