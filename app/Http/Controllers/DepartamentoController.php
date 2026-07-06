<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        // 🚀 ORDENAMIENTO JERÁRQUICO: ADM-GEN va mero arriba, luego los demás
        $departamentos = Departamento::withCount('users')
            ->orderByRaw("CASE WHEN codigo = 'ADM-GEN' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->get();

        return view('departamentos.index', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:departamentos,nombre',
            'codigo' => 'required|string|max:10|unique:departamentos,codigo',
        ]);

        // 🚫 CANDADO DE SEGURIDAD: Impedir que se intente meter otro código reservado admin por request alterado
        if (strtoupper($request->codigo) === 'ADM-GEN') {
            return redirect()->route('departamentos.index')->with('error', 'Acceso denegado: El código ADM-GEN es exclusivo y ya está registrado.');
        }

        Departamento::create([
            'nombre' => $request->nombre,
            'codigo' => strtoupper($request->codigo)
        ]);

        return redirect()->route('departamentos.index')->with('success', 'Área registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $dep = Departamento::findOrFail($id);

        // 🚫 CANDADO DE SEGURIDAD: Bloquear cambios en el Backend para el nodo raíz
        if ($dep->codigo === 'ADM-GEN') {
            return redirect()->route('departamentos.index')->with('error', 'Acceso denegado: El departamento de Administración General está protegido y no puede ser alterado.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100|unique:departamentos,nombre,' . $id,
            'codigo' => 'required|string|max:10|unique:departamentos,codigo,' . $id,
        ]);

        if (strtoupper($request->codigo) === 'ADM-GEN') {
            return redirect()->route('departamentos.index')->with('error', 'Acceso denegado: El código ADM-GEN está reservado.');
        }

        $dep->update([
            'nombre' => $request->nombre,
            'codigo' => strtoupper($request->codigo)
        ]);

        return redirect()->route('departamentos.index')->with('success', 'Área actualizada de forma correcta.');
    }

    public function destroy($id)
    {
        $dep = Departamento::findOrFail($id);

        // 🚫 CANDADO DE SEGURIDAD: Impedir la eliminación física de la raíz
        if ($dep->codigo === 'ADM-GEN') {
            return redirect()->route('departamentos.index')->with('error', 'Acceso denegado: El departamento raíz está blindado por el sistema.');
        }

        if ($dep->users()->count() > 0) {
            return redirect()->route('departamentos.index')->with('error', 'No se puede eliminar el área porque tiene usuarios adscritos.');
        }

        $dep->delete();
        return redirect()->route('departamentos.index')->with('success', 'Área eliminada del sistema.');
    }
}
