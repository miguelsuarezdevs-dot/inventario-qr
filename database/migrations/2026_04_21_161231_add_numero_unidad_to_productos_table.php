// database/migrations/xxxx_add_numero_unidad_to_productos_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('numero_unidad')->default(1);
            $table->integer('total_unidades')->default(1);
            $table->string('remesa_id')->nullable(); // Para agrupar stickers de la misma remesa
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['numero_unidad', 'total_unidades', 'remesa_id']);
        });
    }
};