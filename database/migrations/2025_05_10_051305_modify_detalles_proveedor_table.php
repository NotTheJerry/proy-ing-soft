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
        Schema::dropIfExists('detalles_proveedor');
        
        Schema::create('detalles_proveedor', function (Blueprint $table) {
            $table->id('id_detalle_proveedor');
            $table->unsignedBigInteger('proveedor_id');
            $table->unsignedBigInteger('producto_id');
            $table->decimal('precio', 10, 2);
            $table->integer('cantidad_minima');
            $table->timestamps();
            
            $table->foreign('proveedor_id')->references('id_proveedor')->on('proveedores');
            $table->foreign('producto_id')->references('id_producto')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_proveedor');
    }
};
