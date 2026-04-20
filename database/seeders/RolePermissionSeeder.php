<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // ============================================
        // 1. CREAR PERMISOS (solo si no existen)
        // ============================================
        
        $permisos = [
            'crear_productos',
            'escanear_qr',
            'ver_reportes'
        ];
        
        foreach ($permisos as $permiso) {
            // Crear solo si no existe
            if (!Permission::where('name', $permiso)->exists()) {
                Permission::create(['name' => $permiso]);
            }
        }
        
        // ============================================
        // 2. CREAR ROLES (solo si no existen)
        // ============================================
        
        // Rol ADMIN
        if (!Role::where('name', 'admin')->exists()) {
            $admin = Role::create(['name' => 'admin']);
        } else {
            $admin = Role::where('name', 'admin')->first();
        }
        
        // Rol OPERADOR
        if (!Role::where('name', 'operador')->exists()) {
            $operador = Role::create(['name' => 'operador']);
        } else {
            $operador = Role::where('name', 'operador')->first();
        }
        
        // ============================================
        // 3. ASIGNAR PERMISOS A ROLES
        // ============================================
        
        // Admin tiene todos los permisos
        $admin->syncPermissions(Permission::all());
        
        // Operador solo puede escanear
        $operador->syncPermissions(['escanear_qr']);
        
        // ============================================
        // 4. CREAR USUARIO ADMIN (si no existe)
        // ============================================
        
        $user = User::where('email', 'admin@inventario.com')->first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Administrador',
                'email' => 'admin@inventario.com',
                'password' => bcrypt('admin123'),
            ]);
            echo "✅ Usuario admin creado\n";
        } else {
            echo "✅ Usuario admin ya existe\n";
        }
        
        // Asignar rol admin (syncRoles evita duplicados)
        $user->syncRoles(['admin']);
        
        echo "✅ Seeder ejecutado correctamente\n";
        echo "Usuario: admin@inventario.com\n";
        echo "Contraseña: admin123\n";
    }
}