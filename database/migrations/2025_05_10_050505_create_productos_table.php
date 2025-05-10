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
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('descripcion', 255);
            $table->decimal('precio', 10, 2);
            $table->unsignedBigInteger('categoria_id');
            $table->json('tallas');
            $table->json('colores');
            $table->unsignedBigInteger('proveedor_id');
            $table->date('fecha_creacion');
            $table->enum('estado', ['activo', 'inactivo']);
            $table->timestamps();

            $table->foreign('categoria_id')->references('id_categoria')->on('categorias');
            $table->foreign('proveedor_id')->references('id_proveedor')->on('proveedores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
