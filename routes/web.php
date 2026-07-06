<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\InspeccionInformalController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\CalculoIndIncumplimientoController;
use App\Http\Controllers\VisitaInspeccionController;
use App\Http\Controllers\InicioProcedimientoController;
use Illuminate\Support\Facades\Route;

// --- REDIRECCIÓN INICIAL ---
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =========================================================================
// 🔒 MÓDULOS DE JAPAC - ÁREA DE SANEAMIENTO (REQUIEREN INICIAR SESIÓN)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD PRINCIPAL (AdminLTE 4) ---
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- 🗂️ CONTROL DE USUARIOS (SANEAMIENTO) ---
    Route::resource('usuarios', UsuarioController::class);

    // --- 🏢 MÓDULO DE ESTABLECIMIENTOS ---
    Route::resource('establecimientos', EstablecimientoController::class);

    // 🚀 CONSULTA DE EXPEDIENTES FORMALES (PDF DE VISITAS)
    Route::get('visitas-inspeccion/{id}/pdf', [VisitaInspeccionController::class, 'verPdf'])
        ->name('visitas_inspeccion.pdf');

    // 📝 ─── RECURSO DE VISITAS DE INSPECCIÓN (FORMALES) ───
    Route::resource('visitas-inspeccion', VisitaInspeccionController::class)
        ->names('visitas_inspeccion');

    // 🚀 CONSULTA DE EXPEDIENTES LEGALES (PDF DE INICIO DE PROCEDIMIENTO) - ¡DEBE IR ARRIBA!
    Route::get('inicio-procedimientos/{id}/pdf', [InicioProcedimientoController::class, 'verPdf'])
        ->name('inicio_procedimientos.pdf');

    // 🔨 RECURSO DEL MÓDULO FORMAL DE INICIO DE PROCEDIMIENTO - ¡DEBE IR ABAJO!
    Route::resource('inicio-procedimientos', InicioProcedimientoController::class)
        ->names('inicio_procedimientos');

    // 🚀 CONSULTA DE REPORTES DEL SISTEMA (PDF INFORMAL)
    Route::get('inspecciones-informales/{id}/pdf', [InspeccionInformalController::class, 'verPdf'])
        ->name('inspecciones_informales.pdf');

    // 🚀 PDF DE RESOLUTIVO ADMINISTRATIVO
    Route::get('resolutivo-administrativos/{id}/pdf', [App\Http\Controllers\ResolutivoAdministrativoController::class, 'verPdf'])
        ->name('resolutivo_administrativos.pdf');

    // 🔨 RECURSO DE RESOLUTIVO ADMINISTRATIVO
    Route::resource('resolutivo-administrativos', App\Http\Controllers\ResolutivoAdministrativoController::class)
        ->names('resolutivo_administrativos');

    // 🚀 CONSULTA UNIVERSAL POLIMÓRFICA: Renderiza archivos de evidencia cargados por el usuario
    Route::get('archivos-digitales/{archivoId}', [InspeccionInformalController::class, 'verEvidenciaFisica'])
        ->name('archivos_digitales.ver');

    // --- CATÁLOGO DE INSPECCIONES INFORMALES ---
    Route::resource('inspecciones-informales', InspeccionInformalController::class)
        ->names('inspecciones_informales');

    // 🚀 CONSULTA DE HOJA TARIFARIA INSTITUCIONAL (PDF CÁLCULO)
    Route::get('calculo-incumplimientos/{id}/pdf', [CalculoIndIncumplimientoController::class, 'verPdf'])
        ->name('calculo_incumplimientos.pdf');

    // 🧮 ─── MÓDULO DE CÁLCULO DE ÍNDICES DE INCUMPLIMIENTO ───
    Route::resource('calculo-incumplimientos', CalculoIndIncumplimientoController::class)
        ->names('calculo_incumplimientos');

    // --- 🗺️ CATÁLOGO DE DEPARTAMENTOS / ÁREAS ---
    Route::resource('departamentos', DepartamentoController::class);

    // --- 💼 CATÁLOGO DE PUESTOS / ROLES ---
    Route::resource('puestos', PuestoController::class);

    // --- PERFIL DE USUARIO ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // --- API DE CÓDIGOS POSTALES ---
    Route::get('/api/codigo-postal/{cp}', [UsuarioController::class, 'consultarCP'])->name('api.cp');

});

// Carga las rutas de autenticación de Breeze (login, logout, etc.)
require __DIR__.'/auth.php';
