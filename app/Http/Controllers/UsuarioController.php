<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // 👈 Agregado para que no falle la API de CP

class UsuarioController extends Controller
{
    public function index()
    {
        // 🚀 OPTIMIZADO: Cargamos las relaciones de golpe para que la navbar y la tabla no hagan consultas repetitivas
        $usuarios = User::with(['puesto', 'departamento'])->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        // 🔒 Validamos TODOS los campos nuevos obligatorios que manda tu modal
        $request->validate([
            'usuario'          => 'required|string|unique:users,usuario',
            'nombre'           => 'required|string',
            'paterno'          => 'required|string',
            'materno'          => 'nullable|string',
            'sexo'             => 'required|string|max:1',
            'fechanacimiento'  => 'required|date',
            'lugar_nacimiento' => 'required|string',
            'curp'             => 'required|string|max:18|unique:users,curp',
            'password'         => 'required|string|min:4',
            'puesto_id'        => 'required|exists:puestos,id',
            'departamento_id'  => 'required|exists:departamentos,id',
            'email'            => 'required|email|unique:users,email',
            'telefono'         => 'required|string',
            'tipo_telefono'    => 'required|string',
            'nss'              => 'nullable|string|max:11',
            'codigopostal'     => 'required|string|max:5',
            'estado'           => 'required|string',
            'municipio'        => 'required|string',
            'colonia'          => 'required|string',
            'calle'            => 'required|string',
            'numerocasa'       => 'required|string',
        ]);

        // 💾 Guardamos el registro completo en la base de datos de JAPAC
        User::create([
            'usuario'          => $request->usuario,
            'nombre'           => $request->nombre,
            'paterno'          => $request->paterno,
            'materno'          => $request->materno,
            'sexo'             => $request->sexo,
            'fechanacimiento'  => $request->fechanacimiento,
            'lugar_nacimiento' => $request->lugar_nacimiento,
            'curp'             => $request->curp,
            'password'         => Hash::make($request->password),
            'puesto_id'        => $request->puesto_id,
            'departamento_id'  => $request->departamento_id,
            'email'            => $request->email,
            'telefono'         => $request->telefono,
            'tipo_telefono'    => $request->tipo_telefono,
            'nss'              => $request->nss,
            'estado_operativo' => 'Activo',
            'codigopostal'     => $request->codigopostal,
            'estado'           => $request->estado,
            'municipio'        => $request->municipio,
            'colonia'          => $request->colonia,
            'calle'            => $request->calle,
            'numerocasa'       => $request->numerocasa,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // 🔒 Validaciones para actualizar
        $request->validate([
            'usuario'          => 'required|string|unique:users,usuario,' . $id,
            'nombre'           => 'required|string',
            'paterno'          => 'required|string',
            'materno'          => 'nullable|string',
            'sexo'             => 'required|string|max:1',
            'fechanacimiento'  => 'required|date',
            'lugar_nacimiento' => 'required|string',
            'curp'             => 'required|string|max:18|unique:users,curp,' . $id,
            'puesto_id'        => 'required|exists:puestos,id',
            'departamento_id'  => 'required|exists:departamentos,id',
            'email'            => 'required|email|unique:users,email,' . $id,
            'telefono'         => 'required|string',
            'tipo_telefono'    => 'required|string',
            'nss'              => 'nullable|string|max:11',
            'estado_operativo' => 'required|string',
            'codigopostal'     => 'required|string|max:5',
            'estado'           => 'required|string',
            'municipio'        => 'required|string',
            'colonia'          => 'required|string',
            'calle'            => 'required|string',
            'numerocasa'       => 'required|string',
            'password'         => 'nullable|string|min:4',
        ]);

        // Obtenemos todos los inputs excepto el password inicial
        $data = $request->except(['password']);

        // Si escribieron una nueva contraseña, la encriptamos y la agregamos al array
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->usuario === 'admin') {
            return redirect()->route('usuarios.index')->with('error', 'No puedes dar de baja al administrador principal.');
        }

        // 💡 Modificado para hacer "Baja lógica" cambiando el estado, tal como pide tu tabla
        $usuario->update(['estado_operativo' => 'Baja']);

        return redirect()->route('usuarios.index')->with('success', 'Usuario dado de baja correctamente.');
    }

    /**
     * API Interna: Retorna la cartografía postal para autocompletar formularios vía AJAX.
     */
    public function consultarCP($cp)
    {
        if (!preg_match('/^[0-9]{5}$/', $cp)) {
            return response()->json(['exito' => false, 'error' => 'Formato de C.P. inválido.']);
        }

        try {
            // Buscamos en tu base de datos de códigos postales
            $resultados = \App\Models\ZipCode::where('codigo_postal', $cp)->get();

            if ($resultados->isNotEmpty()) {
                $primerRegistro = $resultados->first();
                $colonias = $resultados->pluck('colonia')->unique()->values()->all();

                return response()->json([
                    'exito'     => true,
                    'estado'    => strtoupper($primerRegistro->estado),
                    'municipio' => strtoupper($primerRegistro->municipio),
                    'colonias'  => $colonias
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error consultando BD de C.P.: " . $e->getMessage());
        }

        return response()->json(['exito' => false, 'error' => 'Código Postal no localizado.']);
    }
}
