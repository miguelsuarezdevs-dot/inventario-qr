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
            $table->integer('numero_unidad')->after('estado');
            $table->integer('total_unidades')->after('numero_unidad');
            $table->string('remesa_id')->after('total_unidades');
        });
    }
    
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['numero_unidad', 'total_unidades', 'remesa_id']);
        });
    }
    
};