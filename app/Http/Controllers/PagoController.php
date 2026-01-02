<?php

namespace App\Http\Controllers;

use App\Models\Me;
use App\Models\Pago;
use App\Models\Grado;
use App\Models\RegistroAlumno;
use App\Models\Tipopago;
use App\Models\estado;
use App\Models\Inscripcion;
use App\Models\AnioEscolar;
use App\Models\Seccion;
use App\Http\Requests\PagoRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


/**
 * Class PagoController
 * @package App\Http\Controllers
 */
class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Guardar filtros en sesión si se proporcionan en la solicitud
        if ($request->has('grados_id')) {
            session(['filtro_grados_id' => $request->get('grados_id')]);
        }
        if ($request->has('seccions_id')) {
            session(['filtro_seccions_id' => $request->get('seccions_id')]);
        }
        if ($request->has('estado')) {
            session(['filtro_estado' => $request->get('estado')]);
        }
        if ($request->has('anio_escolar_id')) {
            session(['filtro_anio_escolar_id' => $request->get('anio_escolar_id')]);
        }

        // Recuperar filtros de la sesión si no se proporcionan en la solicitud
        $grados_id = $request->get('grados_id', session('filtro_grados_id'));
        $seccions_id = $request->get('seccions_id', session('filtro_seccions_id'));
        $estado = $request->get('estado', session('filtro_estado'));
        $anioEscolarId = $request->get('anio_escolar_id', session('filtro_anio_escolar_id'));

        $mesActual = Carbon::now()->month;
        $añoActual = Carbon::now()->year;
        $mesLimite = 10;

        Log::debug("======= INICIO DE PROCESAMIENTO DE SOLVENCIA =======");
        Log::debug("Mes actual: $mesActual, Año: $añoActual");

        // Iniciar la consulta con join para ordenar por apellidos y nombres en la tabla de registro_alumnos
        $query = Pago::join('registro_alumnos', 'pagos.registro_alumnos_id', '=', 'registro_alumnos.id')
            ->select('pagos.*')
            ->with([
                'registroAlumno.inscripcion.grado.nivel',
                'registroAlumno.inscripcion.seccion',
                'estado',
                'mes',
                'tipopago'
            ])
            ->orderBy('registro_alumnos.apellidos', 'asc')
            ->orderBy('registro_alumnos.nombres', 'asc');

        // Aplicar filtro por año escolar si se ha proporcionado
        if ($anioEscolarId) {
            $query->whereHas('registroAlumno.inscripcion', function ($q) use ($anioEscolarId) {
                $q->where('anio_escolar_id', $anioEscolarId);
            });
        }

        // Filtrar por grados_id si se ha proporcionado
        if ($grados_id) {
            $query->whereHas('registroAlumno.inscripcion.grado', function ($q) use ($grados_id) {
                $q->where('id', $grados_id);
            });
        }

        // Filtrar por seccions_id si se ha proporcionado
        if ($seccions_id) {
            $query->whereHas('registroAlumno.inscripcion.seccion', function ($q) use ($seccions_id) {
                $q->where('id', $seccions_id);
            });
        }

        // Obtener los pagos filtrados y ordenados
        $pagos = $query->get();
        Log::debug("Total de pagos encontrados: " . $pagos->count());

        // Agrupar los pagos por alumno, manteniendo el orden original de la consulta
        $alumnos = collect();
        $alumnosIds = []; // Para evitar duplicados

        foreach ($pagos as $pago) {
            $alumnoId = $pago->registro_alumnos_id;

            // Evitar procesar el mismo alumno más de una vez
            if (in_array($alumnoId, $alumnosIds)) {
                continue;
            }

            $alumnosIds[] = $alumnoId;
            $pagosAlumno = $pagos->where('registro_alumnos_id', $alumnoId);

            // Obtener el nombre del alumno y el ID
            $alumnoNombre = $pago->registroAlumno->apellidos ?? 'Desconocido';
            Log::debug("======= PROCESANDO ALUMNO: $alumnoNombre (ID: $alumnoId) =======");

            // Obtener el nivel del alumno
            $nivelId = $pago->registroAlumno->inscripcion->grado->nivel->id ?? null;
            $tiposPagoPorNivel = [];

            switch ($nivelId) {
                case 1: case 2:
                $tiposPagoPorNivel = [2]; break;
                case 3:
                    $tiposPagoPorNivel = [3]; break;
                case 4:
                    $tiposPagoPorNivel = [4]; break;
                default:
                    $tiposPagoPorNivel = [2, 3, 4];
            }

            Log::debug("Nivel: $nivelId, Tipos de pago: " . implode(', ', $tiposPagoPorNivel));

            // No filtrar por tipos de pago aquí para incluir todos los pagos relacionados
            $pagosFiltrados = $pagosAlumno;
            Log::debug("Total de pagos del alumno: " . $pagosFiltrados->count());

            $pagosPorMes = [];

            // Primero, agrupar todos los pagos por mes
            foreach ($pagosFiltrados as $pagoItem) {
                $mesPago = $pagoItem->mes_id;

                if (!isset($pagosPorMes[$mesPago])) {
                    $pagosPorMes[$mesPago] = [
                        'total_abonado' => 0,
                        'monto_esperado' => 0,
                        'pagos' => []
                    ];
                }

                // Agregar el pago a la lista de pagos del mes
                $pagosPorMes[$mesPago]['pagos'][] = $pagoItem;

                // Actualizar el monto esperado si es un pago de colegiatura
                if (in_array($pagoItem->tipopagos_id, $tiposPagoPorNivel)) {
                    $pagosPorMes[$mesPago]['monto_esperado'] = $pagoItem->tipopago->monto ?? 0;
                }
            }

            // Calcular el total abonado para cada mes
            foreach ($pagosPorMes as $mes => &$datosMes) {
                $totalAbonado = 0;

                foreach ($datosMes['pagos'] as $pagoItem) {
                    // Si es un pago incompleto (abono)
                    if (isset($pagoItem->abono) && $pagoItem->abono > 0) {
                        $totalAbonado += $pagoItem->abono;
                        Log::debug("Mes $mes: Sumando abono de {$pagoItem->abono}");
                    }
                    // Si es un pago completo o completar pago
                    elseif ($pagoItem->estados_id == 1 || $pagoItem->estados_id == 3) {
                        // Si es un pago de colegiatura
                        if (in_array($pagoItem->tipopagos_id, $tiposPagoPorNivel)) {
                            $totalAbonado = $pagoItem->tipopago->monto ?? 0;
                            Log::debug("Mes $mes: Pago completo detectado, estableciendo total a {$totalAbonado}");
                            // Un pago completo siempre cubre el mes completo
                            break;
                        } else {
                            // Otros tipos de pagos (completar pagos)
                            $totalAbonado += $pagoItem->monto ?? 0;
                            Log::debug("Mes $mes: Sumando pago adicional de {$pagoItem->monto}");
                        }
                    }
                }

                $datosMes['total_abonado'] = $totalAbonado;
                Log::debug("Mes $mes: Total abonado final = {$totalAbonado}, Monto esperado = {$datosMes['monto_esperado']}");
            }

            // Determinar los meses pagados
            $mesesPagados = [];

            foreach ($pagosPorMes as $mes => $datosPago) {
                $totalAbonado = $datosPago['total_abonado'];
                $montoEsperado = $datosPago['monto_esperado'];

                if ($totalAbonado >= $montoEsperado && $montoEsperado > 0) {
                    $mesesPagados[] = $mes;
                    Log::debug("Mes $mes PAGADO: abonado=$totalAbonado, esperado=$montoEsperado");
                } else {
                    Log::debug("Mes $mes NO PAGADO: abonado=$totalAbonado, esperado=$montoEsperado");
                }
            }

            sort($mesesPagados);
            Log::debug("Meses pagados: " . implode(', ', $mesesPagados));

            $esSolvente = false;

            // Regla especial: si pagó el mes anterior, es solvente todo el mes actual
            if ($mesActual == 1) {
                // Enero: es solvente si ya pagó enero
                $esSolvente = in_array(1, $mesesPagados);
            } else if ($mesActual >= 2 && $mesActual <= 10) {
                // Verificar que TODOS los meses anteriores estén pagados (incluyendo el mes actual si ya se pagó)
                if (in_array($mesActual, $mesesPagados)) {
                    // Si pagó el mes actual, verificar que todos los meses anteriores también estén pagados
                    $mesesRequeridos = range(1, $mesActual - 1);
                    $mesesFaltantes = array_diff($mesesRequeridos, $mesesPagados);
                    $esSolvente = empty($mesesFaltantes);
                } else {
                    // Si no pagó el mes actual, verificar que el mes anterior esté pagado
                    // Y que todos los meses anteriores a ese también estén pagados
                    $mesRequeridoParaSolvencia = $mesActual - 1;
                    $esSolvente = in_array($mesRequeridoParaSolvencia, $mesesPagados);

                    // Verificar también que todos los meses anteriores estén pagados
                    $mesesRequeridos = range(1, $mesRequeridoParaSolvencia - 1);
                    $mesesFaltantes = array_diff($mesesRequeridos, $mesesPagados);
                    $esSolvente = $esSolvente && empty($mesesFaltantes);
                }

            } else if ($mesActual == 11 || $mesActual == 12) {
                // Noviembre/Diciembre: debe tener todos los meses hasta octubre
                $mesesCompletos = range(1, 10);
                $mesesFaltantes = array_diff($mesesCompletos, $mesesPagados);
                $esSolvente = empty($mesesFaltantes);
            }

            Log::debug("Resultado solvencia para {$alumnoNombre}: " . ($esSolvente ? "SOLVENTE" : "INSOLVENTE"));

            $numerosSerie = $pagosAlumno->pluck('num_serie')->filter()->unique()->toArray();
            $alumnoInfo = [
                'registroAlumno' => $pago->registroAlumno,
                'mesesPagados' => $mesesPagados,
                'esSolvente' => $esSolvente,
                'numerosSerie' => $numerosSerie,
            ];

            // Filtrar por estado si se especifica
            if (!$estado || ($estado === 'solvente' && $esSolvente) || ($estado === 'insolvente' && !$esSolvente)) {
                $alumnos->push($alumnoInfo);
            }
        }

        // Obtener los grados, secciones y años escolares para la vista
        $grado = \App\Models\Grado::pluck('nombre_grado', 'id');
        $seccion = \App\Models\Seccion::pluck('seccion', 'id');
        $aniosEscolares = \App\Models\AnioEscolar::pluck('nombre', 'id');

        // Verificar el orden final de los alumnos
        Log::debug("Orden final de alumnos:");
        foreach ($alumnos as $key => $alumno) {
            $apellidos = $alumno['registroAlumno']->apellidos ?? 'Sin apellido';
            $nombres = $alumno['registroAlumno']->nombres ?? 'Sin nombre';
            Log::debug("[$key] $apellidos, $nombres");
        }

        return view('pago.index', compact(
            'pagos',
            'grado',
            'seccion',
            'alumnos',
            'mesActual',
            'aniosEscolares',
            'grados_id',      // Pasar los valores de filtro a la vista
            'seccions_id',
            'estado',
            'anioEscolarId'
        ))->with('i', 0);
    }


    public function show(Request $request, $registro_alumnos_id) {
        // Obtener el año escolar seleccionado en el filtro
        $anio_escolar_id = $request->input('anio');

        // Obtener el registro del alumno
        $registroAlumno = \App\Models\RegistroAlumno::findOrFail($registro_alumnos_id);

        // Obtener los pagos filtrados por ciclo escolar
        $pagos = Pago::where('registro_alumnos_id', $registro_alumnos_id)
            ->with(['registroAlumno.inscripcion', 'tipopago', 'mes', 'estado'])
            ->when($anio_escolar_id, function ($query) use ($anio_escolar_id) {
                return $query->where('anio_escolar_id', $anio_escolar_id); // cambio aquí
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular el total de pagos
        $totalPagos = $pagos->sum(function ($pago) {
            if (in_array($pago->tipopagos_id, [5, 6])) {
                return $pago->abono ?? 0;
            }
            return $pago->tipopago->monto ?? 0;
        });

        // Valores para el regreso
        $filtro_grados_id = session('filtro_grados_id');
        $filtro_seccions_id = session('filtro_seccions_id');
        $filtro_estado = session('filtro_estado');
        $filtro_anio_escolar_id = session('filtro_anio_escolar_id');

        // Obtener todos los ciclos escolares disponibles (para mostrar en el filtro)
        $aniosEscolares = \App\Models\AnioEscolar::all();

        return view('pago.show', compact(
            'pagos',
            'totalPagos',
            'anio_escolar_id',
            'registroAlumno',
            'registro_alumnos_id',
            'filtro_grados_id',
            'filtro_seccions_id',
            'filtro_estado',
            'filtro_anio_escolar_id',
            'aniosEscolares'
        ));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aniosEscolares = AnioEscolar::orderBy('nombre', 'desc')->get();
        $anioActual = date('Y');
        $pago = new Pago();
        $tipos = Tipopago::pluck('tipo_pago', 'id');
        $mes = Me::whereBetween('id', [1, 10])->pluck('mes', 'id');
        $registro_alumnos = RegistroAlumno::pluck('nombres', 'id');
        $montos = Tipopago::pluck('monto', 'id');

        // ★ NUEVO: Obtener el monto de la mora
        $montoMora = Tipopago::where('tipo_pago', 'LIKE', '%mora%')->first();
        $montoMoraValue = $montoMora ? $montoMora->monto : 0;

        $alumnoId = request()->input('registro_alumnos_id');
        $inscripcionPagada = false;

        if ($alumnoId) {
            $inscripcionPagada = Pago::where('registro_alumnos_id', $alumnoId)
                ->where('tipopagos_id', 1)
                ->exists();

            if ($inscripcionPagada) {
                $tipos = $tipos->except(1);
            }
        }

        $pagosPorMes = [];
        if ($alumno = RegistroAlumno::first()) {
            $pagosPorMes = Pago::where('registro_alumnos_id', $alumno->id)
                ->select('mes_id', 'tipopagos_id')
                ->get()
                ->toArray();
        }

        return view('pago.form', compact(
            'aniosEscolares', 'anioActual', 'pago', 'montos', 'tipos',
            'registro_alumnos', 'mes', 'pagosPorMes', 'inscripcionPagada',
            'montoMoraValue' // ★ NUEVO
        ));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(PagoRequest $request)
    {
        // Validación de los campos
        $request->validate([
            'anio_escolar_id' => 'required|exists:anios_escolares,id',
            'num_serie' => 'required',
            'registro_alumnos_id' => 'required',
            'tipopagos_id' => 'nullable',
            'fecha_pago' => 'required|date',
            'mes_id' => $request->input('tipopagos_id') == 6 ? 'nullable|exists:mes,id' : 'required|exists:mes,id',
            'pagos_combinados' => 'nullable|array',
            'pagos_combinados.*' => 'exists:tipopagos,id',
            'incluir_mora' => 'nullable|boolean', // ★ NUEVO: Validación para mora
        ], [
            'num_serie.unique' => 'El número de boleta ya está en uso.',
            'num_serie.required' => 'El número de boleta es obligatorio.',
            'mes_id.required_unless' => 'El mes es obligatorio para este tipo de pago.',
            'pagos_combinados.required' => 'Debe seleccionar al menos un tipo de pago en Pago Combinado.',
        ]);

        $data = $request->all();

        // ★ NUEVO: Manejar mora para colegiatura
        if (in_array($data['tipopagos_id'], [2, 3, 4]) && $request->has('incluir_mora') && $request->incluir_mora) {
            // Obtener el monto de la mora
            $tipoMora = Tipopago::where('tipo_pago', 'LIKE', '%mora%')->first();
            $montoMora = $tipoMora ? $tipoMora->monto : 0;

            // Obtener el monto de la colegiatura
            $montoColegiatura = Tipopago::find($data['tipopagos_id'])->monto ?? 0;

            // Sumar colegiatura + mora
            $montoTotal = $montoColegiatura + $montoMora;

            // Si el monto del formulario es diferente al total calculado, usar el del formulario
            if (isset($data['monto']) && (float)$data['monto'] != $montoTotal) {
                $data['abono'] = $data['monto'];
                unset($data['monto']);
                $data['estados_id'] = 4; // Estado para monto diferente
            } else {
                // Usar el monto total calculado
                $data['monto'] = $montoTotal;
                $data['estados_id'] = 1; // Estado solvente
            }

            // ★ CREAR REGISTRO ADICIONAL DE MORA
            $dataMora = $data;
            $dataMora['tipopagos_id'] = $tipoMora->id; // ID del tipo de pago mora
            $dataMora['monto'] = $montoMora;
            $dataMora['estados_id'] = 3; // Estado para mora
            unset($dataMora['abono']); // Remover abono si existe

            // Crear el pago de mora por separado
            Pago::create($dataMora);
        }

        // Resto del código existente para validaciones...
        $montoOriginal = Tipopago::find($data['tipopagos_id'])->monto ?? 0;

        // Limitar el monto de computación
        if ($data['tipopagos_id'] == 6) {
            if ((float)$data['abono'] > 500) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El pago de computación no puede exceder los 500 quetzales.');
            }

            $totalComputacion = Pago::where('registro_alumnos_id', $data['registro_alumnos_id'])
                ->where('tipopagos_id', 6)
                ->sum('abono');

            if ($totalComputacion + (float)$data['abono'] > 500) {
                $montoDisponible = 500 - $totalComputacion;
                return redirect()->back()
                    ->withInput()
                    ->with('error', "El total de pagos de computación no puede exceder los 500 quetzales. Total actual: Q." . number_format($totalComputacion, 2) . ". Monto disponible: Q." . number_format($montoDisponible, 2) . ".");
            }
        }

        if (in_array($data['tipopagos_id'], [5, 6])) {
            if (empty($data['abono'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Debe ingresar un abono para el pago de Computación.');
            }
            $data['estados_id'] = 3;
        }

        // Manejo de pagos combinados
        if ($request->input('tipopagos_id') === 'combinado') {
            if (empty($data['pagos_combinados']) || !is_array($data['pagos_combinados'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Debe seleccionar al menos un tipo de pago en Pago Combinado.');
            }

            foreach ($data['pagos_combinados'] as $tipopagos_id) {
                $mesId = ($tipopagos_id == 1) ? 13 : $data['mes_id'];
                if ($tipopagos_id == [5, 6]) {
                    continue;
                }

                $estado_id = in_array($tipopagos_id, [2, 3, 4]) ? 1 : 3;

                Pago::create([
                    'anio_escolar_id' => $request->anio_escolar_id,
                    'num_serie' => $data['num_serie'],
                    'registro_alumnos_id' => $data['registro_alumnos_id'],
                    'tipopagos_id' => $tipopagos_id,
                    'fecha_pago' => $data['fecha_pago'],
                    'mes_id' => $mesId,
                    'estados_id' => $estado_id,
                ]);
            }

            return redirect()->route('pagos.create')
                ->with('success', 'Pago combinado registrado exitosamente.');
        }

        // Manejo de inscripción
        if ($data['tipopagos_id'] == 1) {
            $pagoInscripcion = Pago::where('registro_alumnos_id', $data['registro_alumnos_id'])
                ->where('tipopagos_id', 1)
                ->first();

            if ($pagoInscripcion) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El alumno ya ha realizado el pago de inscripción.');
            }
            $data['mes_id'] = 13;
            $data['estados_id'] = 3;
        }

        // Manejo de colegiaturas (solo si no se procesó mora anteriormente)
        if (in_array($data['tipopagos_id'], [2, 3, 4]) && !($request->has('incluir_mora') && $request->incluir_mora)) {
            if ((float)$data['monto'] != $montoOriginal) {
                $data['abono'] = $data['monto'];
                unset($data['monto']);
                $data['estados_id'] = 4;
            } else {
                $data['estados_id'] = 1;
            }

            $pagoExistente = Pago::where('registro_alumnos_id', $data['registro_alumnos_id'])
                ->where('tipopagos_id', $data['tipopagos_id'])
                ->where('mes_id', $data['mes_id'])
                ->first();

            if ($pagoExistente) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El alumno ya ha realizado un pago para el mes seleccionado.');
            }
        }

        if (!isset($data['estados_id'])) {
            $data['estados_id'] = 3;
        }

        // Crear el pago principal
        Pago::create($data);

        return redirect()->route('pagos.create')
            ->with('success', 'Pago registrado exitosamente.' .
                ($request->has('incluir_mora') && $request->incluir_mora ? ' (Incluyendo mora)' : ''));
    }
    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $anioActual = date('Y');
        $pago = Pago::find($id);
        $tipos = Tipopago::pluck('tipo_pago', 'id'); // Obtener todos los tipos de pago
        $registro_alumnos = RegistroAlumno::pluck('nombres', 'id'); // Obtener todos los alumnos
        $montos = Tipopago::pluck('monto', 'id'); // Agregar esto para obtener los montos

        return view('pago.edit', compact( 'pago', 'anioActual','montos','tipos', 'registro_alumnos'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(PagoRequest $request, Pago $pago)
    {
        $pago->update($request->validated());

        return redirect()->route('pagos.index')
            ->with('success', 'Pago updated successfully');
    }

    public function destroy($id)
    {

        try {
        Pago::find($id)->delete();

        return redirect()->route('pagos.index')
            ->with('success', 'Pago Eliminado Exitosamente');
        }catch (\Exception $exception) {
                // Manejar errores y registrar en el log
                Log::debug($exception->getMessage());
                return redirect()->route('pagos.index')->with('alerta', 'no');
            }
    }

    public function buscar()
    {
        $anioActual = date('Y');
        return view('pago.form', 'anioActual');
    }

    public function resultadosp(Request $request)
    {
        $anioActual = date('Y');
        $pago = new Pago();
        $registro_alumno = RegistroAlumno::pluck('nombres', 'id');
        $tipos = Tipopago::pluck('tipo_pago', 'id');
        $montos = Tipopago::pluck('monto', 'id');
        $mes = Me::whereBetween('id', [1, 10])->pluck('mes', 'id');
        $aniosEscolares = AnioEscolar::orderBy('nombre', 'desc')->get();

        // ★ NUEVO: Obtener monto de mora
        $montoMora = Tipopago::where('tipo_pago', 'LIKE', '%mora%')->first();
        $montoMoraValue = $montoMora ? $montoMora->monto : 0;

        $inscripcionPagada = false;
        $search = $request->input('search');
        $error = null;

        $alumno = RegistroAlumno::where('id', 'LIKE', "%$search%")
            ->orWhere('nombres', 'LIKE', "%$search%")
            ->orWhereHas('inscripciones', function ($query) use ($search) {
                $query->where('codigo_correlativo', 'LIKE', "%$search%");
            })
            ->first();

        $grado = null;
        $seccion = null;
        $pagosPorMes = [];
        $tipos = Tipopago::pluck('tipo_pago', 'id');

        if ($alumno) {
            $inscripcion = Inscripcion::where('registro_alumnos_id', $alumno->id)->first();
            $grado = $inscripcion ? $inscripcion->grado : null;
            $seccion = $inscripcion ? $inscripcion->seccion : null;

            if ($grado && $grado->nivels_id) {
                switch ($grado->nivels_id) {
                    case 1:
                    case 2:
                        $tipos = $tipos->except([3, 4]);
                        break;
                    case 3:
                        $tipos = $tipos->except([2, 4]);
                        break;
                    case 4:
                        $tipos = $tipos->except([2, 3]);
                        break;
                }
            }

            $pagosPorMes = Pago::where('registro_alumnos_id', $alumno->id)
                ->select('mes_id', 'tipopagos_id')
                ->get()
                ->toArray();

            $inscripcionPagada = Pago::where('registro_alumnos_id', $alumno->id ?? null)
                ->where('tipopagos_id', 1)
                ->exists();

            if ($inscripcionPagada) {
                $tipos = $tipos->except(1);
            }
        } else {
            $error = "Alumno no encontrado.";
        }

        return view('pago.create', compact(
            'alumno', 'montos', 'grado', 'seccion', 'pago', 'tipos',
            'registro_alumno', 'error', 'pagosPorMes', 'mes', 'inscripcionPagada',
            'tipos', 'anioActual', 'aniosEscolares', 'montoMoraValue' // ★ NUEVO
        ));
    }
    public function indexp(Request $request) {
        // Obtener los grados y secciones para los filtros
        $grado = Grado::pluck('nombre_grado', 'id');
        $seccion = Seccion::pluck('seccion', 'id');
        $aniosEscolares = AnioEscolar::pluck('nombre', 'id'); // ← para la vista

        // Iniciar la consulta base
        $query = RegistroAlumno::with(['pagos', 'inscripcion', 'encargado']);

        /*━━━━━━━━━━ 3. Filtro por Año Escolar ━━━━━━━━━━*/
        $anioEscolarId = $request->get('anio_escolar_id');
        if ($anioEscolarId) {
            $query->whereHas('inscripcion', fn ($q) => $q->where('anio_escolar_id', $anioEscolarId));
        }

        // Verificar si hay filtros y si la relación inscripcion existe
        if ($request->filled('grados_id')) {
            $query->whereHas('inscripcion', function($q) use ($request) {
                $q->where('grados_id', $request->grados_id);
            });
        }

        if ($request->filled('seccions_id')) {
            $query->whereHas('inscripcion', function($q) use ($request) {
                $q->where('seccions_id', $request->seccions_id);
            });
        }

        // Ordenar primero por apellidos y luego por nombres en orden alfabético
        $query->orderBy('apellidos', 'asc')
            ->orderBy('nombres', 'asc');

        $registroAlumnos = $query->get();

        return view('pago.pagoinscripcion', compact('registroAlumnos', 'grado', 'seccion', 'aniosEscolares'));
    }
}
