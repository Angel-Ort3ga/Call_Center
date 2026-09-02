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
        Schema::create('llamadas', function (Blueprint $table) {

            $table->id();

            /*
        |--------------------------------------------------------------------------
        | Identificación de la llamada
        |--------------------------------------------------------------------------
        */

            $table->string('folio', 30)->unique();

            $table->date('fecha');

            $table->time('hora');

            /*
        |--------------------------------------------------------------------------
        | Información del ciudadano
        |--------------------------------------------------------------------------
        */

            $table->string('telefono', 20);

            $table->string('nombre', 150);

            /*
        |--------------------------------------------------------------------------
        | Información de la llamada
        |--------------------------------------------------------------------------
        */

            $table->text('motivo');

            $table->string('categoria', 50);

            /*
        |--------------------------------------------------------------------------
        | Departamento
        |--------------------------------------------------------------------------
        */

            $table->foreignId('departamento_id')
                ->constrained('departamentos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
        |--------------------------------------------------------------------------
        | Usuario que registró la llamada
        |--------------------------------------------------------------------------
        */

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

            $table->enum('estado', [
                'en_proceso',
                'transferida',
                'finalizada'
            ])->default('en_proceso');

            /*
        |--------------------------------------------------------------------------
        | Observaciones
        |--------------------------------------------------------------------------
        */

            $table->text('observaciones')->nullable();

            /*
        |--------------------------------------------------------------------------
        | Fechas de control
        |--------------------------------------------------------------------------
        */

            $table->timestamp('transferida_at')->nullable();

            $table->timestamp('finalizada_at')->nullable();

            $table->timestamps();

            /*
        |--------------------------------------------------------------------------
        | Índices
        |--------------------------------------------------------------------------
        */

            $table->index('fecha');

            $table->index('estado');

            $table->index('categoria');

            $table->index('departamento_id');

            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llamadas');
    }
};
