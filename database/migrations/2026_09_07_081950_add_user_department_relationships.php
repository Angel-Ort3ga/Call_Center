
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
        /*
        |--------------------------------------------------------------------------
        | users → roles
        |--------------------------------------------------------------------------
        */

        Schema::table('users', function (Blueprint $table) {

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | users → departamentos
        |--------------------------------------------------------------------------
        */

        Schema::table('users', function (Blueprint $table) {

            $table->foreign('departamento_id')
                ->references('id')
                ->on('departamentos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | departamentos → users
        |--------------------------------------------------------------------------
        */

        Schema::table('departamentos', function (Blueprint $table) {

            $table->foreign('responsable_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {

            $table->dropForeign([
                'responsable_id'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign([
                'departamento_id'
            ]);

            $table->dropForeign([
                'role_id'
            ]);
        });
    }
};
