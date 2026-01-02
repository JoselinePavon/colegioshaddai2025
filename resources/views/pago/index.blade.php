@extends('layouts.app')

@section('template_title')
    Pago
@endsection


@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

    <div class="container mt-3">
        <div class="card shadow-lg">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 id="card_title" class="font-weight-bold">
                        <i class="bi bi-file-earmark-check"></i> {{ __('Solvencia de alumnos') }}
                    </h4>
                    <span class="text-muted" id="fecha-actual"></span> <!-- Fecha actual -->
                </div>

                <form action="{{ route('pagos.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-4">
                    <select name="grados_id" class="form-select btn btn-outline-dark btn-xs w-25" onchange="this.form.submit()">
                        <option value="">Todos los Grados</option>
                        @foreach($grado as $id => $nombre_grado)
                            <option value="{{ $id }}" {{ request()->get('grados_id') == $id ? 'selected' : '' }}>
                                {{ $nombre_grado }}
                            </option>
                        @endforeach
                    </select>

                    <select name="seccions_id" class="form-select btn btn-outline-dark btn-xs w-25" onchange="this.form.submit()">
                        <option value="">Todas las secciones</option>
                        @foreach($seccion as $id => $nombre)
                            <option value="{{ $id }}" {{ request()->get('seccions_id') == $id ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>

                    <select name="estado" class="form-select btn btn-outline-dark btn-xs w-25" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="solvente" {{ request()->get('estado') == 'solvente' ? 'selected' : '' }}>Solvente</option>
                        <option value="insolvente" {{ request()->get('estado') == 'insolvente' ? 'selected' : '' }}>Insolvente</option>
                    </select>

                    <select name="anio_escolar_id" class="form-select btn btn-outline-dark btn-xs w-25" onchange="this.form.submit()">
                        <option value="">Todos los Años</option>
                        @foreach ($aniosEscolares as $id => $anio)
                            <option value="{{ $id }}" {{ request('anio_escolar_id') == $id ? 'selected' : '' }}>
                                {{ $anio }}
                            </option>
                        @endforeach
                    </select>

                    <button id="download-excel" class="btn btn-success btn-sm shadow-xs " style="font-size: 0.99rem;">
                        <i class="bi bi-download"></i> Descargar Excel
                    </button>
                </form>



                {{-- SweetAlert para mensajes de éxito --}}
                @if ($message = Session::get('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{{ $message }}',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    </script>
                @endif

                <div class="table-responsive">
                    <table id="mediciones" class="table table-striped table-bordered shadow-sm mt-3" style="font-size: 0.75em; margin: 0 auto; text-align: center;">
                    <thead class="text-white" style="background-color: #343a40;">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Correlativo</th>
                            <th scope="col">Alumno</th>
                            <th scope="col">Grado</th>
                            <th scope="col">Sección</th>
                            <th scope="col" style="display:none;">Boletas</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($alumnos as $alumno)
                            <tr data-meses-pagados="{{ json_encode($alumno['mesesPagados']) }}" data-es-solvente="{{ $alumno['esSolvente'] ? 'true' : 'false' }}">
                                <td>{{ ++$i }}</td>
                                <td>{{ $alumno['registroAlumno']->inscripcion->codigo_correlativo ?? 'Sin Correlativo' }}</td>
                                <td>{{ $alumno['registroAlumno']->apellidos }} {{ $alumno['registroAlumno']->nombres }}</td>
                                <td>{{ $alumno['registroAlumno']->inscripcion->grado->nombre_grado }} -
                                    {{ $alumno['registroAlumno']->inscripcion->grado->nivel->nivel }}
                                </td>
                                <td>{{ $alumno['registroAlumno']->inscripcion->seccion->seccion ?? 'Sin Sección' }}</td>
                                <td style="display:none;">
                                    {{ isset($alumno['numerosSerie']) ? implode(' ', $alumno['numerosSerie']) : '' }}
                                </td>
                                <td class="estado">
                                    <span class="badge bg-secondary">● Sin estado</span>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary" href="{{ route('pagos.show', $alumno['registroAlumno']->id) }}">
                                        <i class="fa fa-fw fa-eye"></i> Mostrar Pagos
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let insolventesCount = 0;

            document.querySelectorAll('tr[data-es-solvente]').forEach(row => {

            const estadoCell = row.querySelector('.estado');
            const esSolvente = row.getAttribute('data-es-solvente') === 'true'; // Leer el atributo como booleano

            if (esSolvente) {
                estadoCell.innerHTML = `<span class="badge bg-success">● Solvente</span>`;
            } else {
                estadoCell.innerHTML = `<span class="badge bg-danger">● Insolvente</span>`;
                insolventesCount++; // Incrementar contador si el alumno es insolvente

            }
        });
            // Guardar el total en localStorage
            localStorage.setItem('insolventesCount', insolventesCount);

            const insolventesContainer = document.createElement('div');
            insolventesContainer.classList.add('alert', 'alert-danger', 'mt-3');
            insolventesContainer.innerHTML = `<strong>Total de Alumnos Insolventes:</strong> ${insolventesCount}`;
            document.querySelector('.card.shadow-lg .p-4').prepend(insolventesContainer); // Insertar al inicio del contenido
        });
    </script>


    {{-- SweetAlert para confirmación de eliminación --}}
    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esta acción",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#mediciones').DataTable({
                "language": {
                    "lengthMenu": "Mostrar _MENU_ por página",
                    "zeroRecords": "<i class='fas fa-info-circle'></i> No se encontraron resultados para la búsqueda.",
                    "emptyTable": "<i class='fas fa-info-circle'></i> No hay datos disponibles en la tabla",
                    "info": "Mostrando _PAGE_ de _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "search": "Buscar",
                    "paginate": {
                        "first": "Primera",
                        "last": "Última",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                "drawCallback": function(settings) {
                    if (settings.fnRecordsTotal() == 0) {
                        $(this).find('.dataTables_empty').addClass('alert-style');
                    }
                }
            });
        });
    </script>

    <script>
        document.getElementById('download-excel').addEventListener('click', function() {
            // Obtén la tabla
            var table = document.getElementById('mediciones');

            // Convierte la tabla a una hoja de trabajo de Excel
            var wb = XLSX.utils.table_to_book(table, { sheet: "Solvencia de Alumnos" });

            // Genera el archivo Excel y lo descarga
            XLSX.writeFile(wb, "solvencia_alumnos.xlsx");
        });
    </script>



@endsection
