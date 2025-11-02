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
        Schema::create('debtors', function (Blueprint $table) {
            $table->id();
             // Cedente
            $table->enum('tipo_cedente', ['publico', 'privado']);
            $table->string('nombre_cedente');

            // Deudor
            $table->string('nombre_deudor');
            $table->decimal('monto_deuda_total', 15, 2);

            // Diagnóstico con abreviaturas
            $table->enum('diagnostico', [
                'CP',   // Compromiso de Pago - green
                'PP',   // Quiere Pagar - yellow
                'NP',   // No Puede Pagar - gray
                'NQP',  // No Quiere Pagar - purple
                'CONT', // Contactado - orange
                'NU'    // No Ubicado - red
            ]);

            // Estado
            $table->enum('estado', [
                'asignacion',
                'investigacion',
                'contactabilidad',
                'negociacion',
                'seguimiento'
            ]);

            // Judicial
            $table->boolean('judicial')->default(false);

            // Gestión
            $table->dateTime('ultima_gestion')->nullable();
            $table->string('tipo_ultima_gestion')->nullable();
            $table->dateTime('proxima_gestion')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debtors');
    }
};
