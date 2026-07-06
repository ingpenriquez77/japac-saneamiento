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
        // 1️⃣ CRAMOS PRIMERO LAS TABLAS DE REFERENCIA
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('codigo')->nullable();
            $table->timestamps();
        });

        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('nivel_acceso')->default('operador');
            $table->timestamps();
        });

        // 2️⃣ AHORA SÍ CREAMOS LA TABLA USERS CON SUS LLAVES FORÁNEAS CORRECTAS
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // 🔐 Credenciales y Acceso Base
            $table->string('usuario', 50)->unique();
            $table->string('password');
            $table->string('email', 100)->unique();

            // 👤 Información Personal Base
            $table->string('nombre', 50);
            $table->string('paterno', 50);
            $table->string('materno', 50)->nullable();
            $table->char('sexo', 1)->nullable();
            $table->date('fechanacimiento')->nullable();
            $table->string('lugar_nacimiento', 50)->nullable();
            $table->string('curp', 18)->unique()->nullable();

            // 🛡️ Puesto, Contacto y Seguridad Social
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->onDelete('set null');
            $table->foreignId('puesto_id')->nullable()->constrained('puestos')->onDelete('set null');

            $table->string('telefono', 20)->nullable();
            $table->string('tipo_telefono', 15)->default('CELULAR');
            $table->string('nss', 11)->nullable();
            $table->string('estado_operativo', 20)->default('Activo');

            // 🏠 Localización / Domicilio
            $table->string('codigopostal', 5)->nullable();
            $table->string('estado', 50)->nullable();
            $table->string('municipio', 50)->nullable();
            $table->string('colonia', 100)->nullable();
            $table->string('calle', 100)->nullable();
            $table->string('numerocasa', 10)->nullable();

            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('puestos');
        Schema::dropIfExists('departamentos');
    }
};
