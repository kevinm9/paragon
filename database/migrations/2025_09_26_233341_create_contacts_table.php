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
        Schema::create('contacts', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('debtor_id')->constrained('debtors')->onDelete('cascade');

            $table->enum('tipo', ['celular','telefono_fijo','email','direccion','otro']);
            $table->enum('categoria', ['personal','trabajo','referencia','familiar','legal','cobranza']);

            $table->string('detalle');
            $table->boolean('validacion')->default(false);
            $table->timestamp('fecha_validacion')->nullable();

            $table->boolean('principal')->default(false);
            $table->text('observacion')->nullable();

            $table->timestamps();

            // Restricción: solo un principal por tipo dentro del deudor
            //$table->unique(['debtor_id', 'tipo', 'principal'], 'unique_principal_per_tipo');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
