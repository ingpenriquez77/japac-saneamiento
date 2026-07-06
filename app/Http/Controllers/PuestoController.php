<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\Departamento;
use Illuminate\Http\Request;

class PuestoController extends Controller
{
    public function index()
    {
        // 🚀 ORDENAMIENTO JERÁRQUICO: El nivel 'admin' va mero arriba de la tabla, luego los demás por id descendente
        $puestos = Puesto::with('departamento')
            ->withCount('users')
            ->orderByRaw("CASE WHEN nivel_acceso = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->get();

        // 🔒 Filtro en consulta: Evitamos que el depto exclusivo salga en el formulario para crear más puestos
        $departamentos = Departamento::where('codigo', '!=', 'ADM-GEN')->orderBy('nombre', 'asc')->get();

        return view('puestos.index', compact('puestos', 'departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:100',
            'nivel_acceso'    => 'required|string',
            'departamento_id' => 'required|exists:departamentos,id',
        ]);

        // 🚫 CANDADO DE SEGURIDAD 1: Bloquear intentos de inyección de nivel 'admin'
        if ($request->nivel_acceso === 'admin') {
            return redirect()->route('puestos.index')->with('error', 'Acceso denegado: Solo puede existir un único puesto con rango de Administrador Global.');
        }

        // 🚫 CANDADO DE SEGURIDAD 2: Bloquear inserción dentro de la categoría exclusiva ADM-GEN
        $depExclusivo = Departamento::where('codigo', 'ADM-GEN')->first();
        if ($depExclusivo && (int)$request->departamento_id === $depExclusivo->id) {
            return redirect()->route('puestos.index')->with('error', 'Acceso denegado: No se permite la adición de puestos al departamento de Administración General.');
        }

        // Validar duplicados en el mismo departamento operativo
        $existe = Puesto::where('nombre', $request->nombre)
                        ->where('departamento_id', $request->departamento_id)
                        ->exists();

        if ($existe) {
            return redirect()->route('puestos.index')->with('error', 'Este puesto ya se encuentra registrado en el departamento seleccionado.');
        }

        Puesto::create($request->all());
        return redirect()->route('puestos.index')->with('success', 'Puesto configurado correctamente en su área.');
    }

    public function update(Request $request, $id)
    {
        $puesto = Puesto::findOrFail($id);

        // 🚫 CANDADO DE SEGURIDAD 3: Evitar que alteren el puesto raíz del Seeder
        if ($puesto->nivel_acceso === 'admin') {
            return redirect()->route('puestos.index')->with('error', 'Acceso denegado: Los atributos del Administrador Global no pueden ser alterados.');
        }

        $request->validate([
            'nombre'          => 'required|string|max:100',
            'nivel_acceso'    => 'required|string',
            'departamento_id' => 'required|exists:departamentos,id',
        ]);

        // 🚫 CANDADO DE SEGURIDAD 4: Evitar que un puesto regular sea escalado a rango admin o movido a ADM-GEN
        if ($request->nivel_acceso === 'admin') {
            return redirect()->route('puestos.index')->with('error', 'Operación denegada de protección jerárquica.');
        }

        $depExclusivo = Departamento::where('codigo', 'ADM-GEN')->first();
        if ($depExclusivo && (int)$request->departamento_id === $depExclusivo->id) {
            return redirect()->route('puestos.index')->with('error', 'Acceso denegado: Destino de departamento protegido.');
        }

        $puesto->update($request->all());
        return redirect()->route('puestos.index')->with('success', 'Puesto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $puesto = Puesto::findOrFail($id);

        // 🚫 CANDADO DE SEGURIDAD 5: Impedir la remoción del puesto supremo
        if ($puesto->nivel_acceso === 'admin') {
            return redirect()->route('puestos.index')->with('error', 'Acceso denegado: El puesto Administrador Global está blindado y no puede removerse.');
        }

        if ($puesto->users()->count() > 0) {
            return redirect()->route('puestos.index')->with('error', 'Existen usuarios asignados a este puesto. No se puede eliminar.');
        }

        $puesto->delete();
        return redirect()->route('puestos.index')->with('success', 'Puesto removido de la plantilla.');
    }
}
