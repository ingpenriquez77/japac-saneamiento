<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use Illuminate\Http\Request;

class EstablecimientoController extends Controller
{
    public function index()
    {
        $establecimientos = Establecimiento::orderBy('id', 'desc')->get();
        return view('establecimientos.index', compact('establecimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cuenta'                 => 'required|string|max:50|unique:establecimientos,cuenta',
            'nombre_establecimiento' => 'required|string|max:100',
            'razon_social'           => 'required|string|max:100',
            'rfc'                    => 'required|string|max:15',
            'actividad'              => 'required|string|max:50',
            'calle'                  => 'required|string|max:100',
            'num_exterior'           => 'required|integer',
            'num_interior'           => 'nullable|string|max:10',
            'colonia'                => 'required|string|max:100',
            'codigo_postal'          => 'required|integer',
            'telefono'               => 'required|string|max:20',
            'num_medidor'            => 'required|string|max:50',
            'correo_electronico'     => 'required|email|max:100',
            'trampas_gra'            => 'nullable|integer',
            'trampas_sst'            => 'nullable|integer',
            'num_permiso'            => 'nullable|string|max:30',
            'fechaemision_permiso'   => 'nullable|date',
            'observaciones'          => 'nullable|string',
            'empresa_nueva'          => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['empresa_nueva'] = $request->has('empresa_nueva') ? true : false;

        Establecimiento::create($data);

        return redirect()->route('establecimientos.index')->with('success', 'Establecimiento registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $establecimiento = Establecimiento::findOrFail($id);

        $request->validate([
            'cuenta'                 => 'required|string|max:50|unique:establecimientos,cuenta,' . $id,
            'nombre_establecimiento' => 'required|string|max:100',
            'razon_social'           => 'required|string|max:100',
            'rfc'                    => 'required|string|max:15',
            'actividad'              => 'required|string|max:50',
            'calle'                  => 'required|string|max:100',
            'num_exterior'           => 'required|integer',
            'num_interior'           => 'nullable|string|max:10',
            'colonia'                => 'required|string|max:100',
            'codigo_postal'          => 'required|integer',
            'telefono'               => 'required|string|max:20',
            'num_medidor'            => 'required|string|max:50',
            'correo_electronico'     => 'required|email|max:100',
            'trampas_gra'            => 'nullable|integer',
            'trampas_sst'            => 'nullable|integer',
            'num_permiso'            => 'nullable|string|max:30',
            'fechaemision_permiso'   => 'nullable|date',
            'status'                 => 'required|string|max:20',
            'observaciones'          => 'nullable|string',
            'empresa_nueva'          => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['empresa_nueva'] = $request->has('empresa_nueva') ? true : false;

        $establecimiento->update($data);

        return redirect()->route('establecimientos.index')->with('success', 'Datos del establecimiento actualizados.');
    }

    public function destroy($id)
    {
        $establecimiento = Establecimiento::findOrFail($id);

        // Sincronizado al nombre real de tu columna: 'status'
        $establecimiento->update(['status' => 'Inactivo']);

        return redirect()->route('establecimientos.index')->with('success', 'El establecimiento ha sido marcado como Inactivo.');
    }
}
