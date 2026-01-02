@extends('layouts.app')

@section('template_title')
    Contrato
@endsection

@section('content')
    <div class="contract-container">
        <div class="page-number"></div>
        <div class="contract-header">
            <h5><strong>CONTRATO DE ADHESIÓN POR PRESTACIÓN DE</strong></h5>
            <h5><strong>SERVICIOS EDUCATIVOS</strong></h5>
            <h6><strong>COLEGIO PARTICULAR MIXTO SHADDAI</strong></h6><br>
            <div style="display: flex; align-items: center; justify-content: flex-start; gap: 5px; margin: 0;">
                <!-- Texto (label) alineado correctamente con el input -->
                <span style="white-space: nowrap; font-size: 12px;">Correlativo Interno Contrato No.</span>

                <!-- Formulario con input y botón alineados -->
                <form method="POST" action="{{ route('buscar.alumno') }}" style="display: flex; gap: 0; margin: 0;">
                    @csrf
                    <!-- Input sin bordes -->
                    <input
                        type="text"
                        id="codigo-correlativo"
                        name="codigo_correlativo"
                        value="{{ old('codigo_correlativo', $codigoCorrelativo ?? '') }}"
                        required
                        style="padding: 5px; border: 1px solid transparent; border-radius: 4px; outline: none; width: 120px; height: 25px; font-size: 12px; box-sizing: border-box;"
                    >
                    <!-- Botón con el icono ajustado al tamaño del input -->
                    <button
                        type="submit"
                        class="btn btn-primary"
                        style="padding: 0; border: 1px solid transparent; background-color: #007bff; color: white; cursor: pointer; font-size: 12px; border-radius: 4px; height: 27px; width: 40px; display: flex; align-items: center; justify-content: center; box-sizing: border-box;"
                    >
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
            <!-- Párrafo alineado a la izquierda -->
            <p style="text-align: left; margin: 0; font-weight: bold;">Aprobado y registrado según Resolución DIACO: DDC-342-2022 </p><br>

            <p class="contract-body">En el municipio Puerto Barrios, del departamento de Izabal,  <input type="date" name="contract_date" id="contract_date" style="width: 65px; border: none; background: transparent; font-family: inherit; font-size: inherit;"><strong>NOSOTROS:HERLINDO ARTIGA MARROQUÍN</strong> de <input type="number" id="edad" name="edad" min="67" max="80" value="66" style="width: 15px; -moz-appearance: textfield; appearance: textfield;">años de edad, casado, guatemalteco, bachiller, de este domicilio, me identifico con Documento Personal de Identificación, Código Único de Identificación –CUI— número 2679 12609 1804, extendido por el Registro Nacional de las Personas de la República de Guatemala, propietario del centro educativo COLEGIO PARTICULAR MIXTO SHADDAI, ubicado en 3ª. Calle 1ª. Avenida, Colonia San Agustín, Santo Tomás de Castilla, municipio de Puerto Barrios, departamento de Izabal; según lo acredito con la Constancia de Inscripción y Actualización de Datos y Registro Tributario Unificado 8319383, emitido por la Superintendencia de Administración Tributaria. Y por la otra parte:</p>

            <div class="contract-header">
        <div class="contract-body">
            <h6><strong>DATOS DEL REPRESENTANTE DEL EDUCANDO</strong></h6>
            <div class="form-grid-3">

                <div class="form-group">
                    <label for="nombre-completo">Nombre Completo:</label>
                    <input type="text" id="nombre-completo" name="nombre_completo" value="{{ old('nombre_completo', $nombreCompleto ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label for="edad">Edad:</label>
                    <input type="text" id="edad_encargado" name="edad" value="{{ old('edad_encargado', $edad_encargado ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label for="estado-civil">Estado Civil:</label>
                    <input type="text" id="estado-civil" name="estado_civil" value="{{ old('estado_civil', $estadoCivil ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label for="oficio">Oficio:</label>
                    <input type="text" id="oficio" name="oficio" value="{{ old('oficio', $oficio ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label for="identificacion">Número de Identificación:</label>
                    <input type="text" id="identificacion" name="identificacion" value="{{ old('identificacion', $identificacion ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label for="residencia">Residencia:</label>
                    <input type="text" id="residencia" name="residencia" value="{{ old('residencia', $residencia ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label for="telefono-casa">Número de Teléfono en Casa:</label>
                    <input type="text" id="telefono-casa" name="telefono_casa" value="{{ old('telefono_casa', $telefonoCasa ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label for="telefono-celular">Número de Celular:</label>
                    <input type="text" id="telefono-celular" name="telefono_celular" value="{{ old('telefono_celular', $telefonoCelular ?? '') }}" style="border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
                </div>
            </div>
        </div>
    </div>


    <p style="text-align: justify;">declarando que la información personal proporcionada, es de carácter confidencial. Los comparecientes aseguramos ser de los datos de identificación anotados, estar en el libre ejercicio de nuestros derechos civiles y la calidad que se ejercita es amplia y suficiente para la celebración del CONTRATO DE ADHESIÓN POR PRESTACIÓN DE SERVICIOS EDUCATIVOS, de conformidad con las siguientes cláusulas:</p>

    <div class="clause">
        <h6><strong>PRIMERA: INFORMACIÓN DEL EDUCANDO Y SERVICIO EDUCATIVO CONTRATADO.</strong></h6>
        <div class="form-grid-3">
            <div class="form-group">
                <label for="nombre-educando" style="text-align: left;">Nombre del Educando:</label>
                <input
                    type="text"
                    id="nombre-educando"
                    name="nombre_educando"
                    value="{{ old('nombre_educando', $nombreEducando ?? '') }}"
                    style="width: 180%; border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
            </div>
            <div class="form-group">
                <label for="grado-nivel" style="margin-left: 5px;">Grado y Nivel:</label>
                <input
                    type="text"
                    id="grado-nivel"
                    name="grado_nivel"
                    value="{{ old('grado_nivel', ($grado ?? '') . ' ' . ($nivel ?? '')) }}"
                    style="width: 100%; margin-left: 80px; border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
            </div>
            <div class="form-group">
                <label for="jornada" style="margin-left: 5px;">Jornada:</label>
                <input
                    type="text"
                    id="jornada"
                    name="jornada"
                    value="{{ old('jornada', $jornada ?? '') }}"
                    style="width: 60%; margin-left: 90px; border: none; border-bottom: 1px solid #ccc; background-color: #f5f5f5;">
            </div>
        </div>


        <p style="text-align: justify;">Servicios educativos debidamente autorizados por el Ministerio de Educación, de conformidad con las siguientes resoluciones: a) No. AMP/DESPACHO/006-2020; b) No. REV/DESPACHO/026-2020 ambas de fecha 30 de diciembre de 2020, emitidas por la Dirección Departamental de Educación de Izabal, mismas que se ponen a la vista.</p>
    </div>

    <div class="clause">
        <h6><strong>SEGUNDA: VOLUNTARIEDAD EN LA CONTRATACIÓN DEL SERVICIO.</strong></h6>
        <p style="text-align: justify;">Manifiesta el Representante del Educando que, conociendo la amplia oferta de instituciones privadas que prestan servicios educativos, de manera voluntaria y espontánea ha elegido al Centro Educativo para la educación académica del educando.</p>
    </div>

    <div class="clause">
        <h6><strong>TERCERA: PLAZO.</strong></h6>
        <p style="text-align: justify;">El servicio educativo convenido en este contrato será válido para el ciclo escolar 2025
            durante su vigencia no puede ser modificada ninguna de sus cláusulas, las que deberán cumplirse a cabalidad. El Representante del Educando y el Centro Educativo podrán suscribir un nuevo contrato para el ciclo escolar inmediato siguiente, en caso acuerden la continuidad del educando.</p>
    </div>

    <div class="clause">
        <h6><strong>CUARTA: DERECHOS DEL EDUCANDO Y SU REPRESENTANTE.</strong></h6>
        <p style="text-align: justify;">El Educando y su Representante como usuarios del servicio educativo contratado, tendrán derecho a:</p>
        <ol type="a">
            <li style="text-align: justify;">La protección a la vida, salud y seguridad en la adquisición, consumo y uso de bienes y servicios: Las instalaciones del Centro Educativo están dotadas de todos los servicios básicos, condiciones higiénicas y adecuadas para que los educandos no corran ninguna clase de riesgo que ponga en peligro su integridad física, siempre y cuando hagan uso correcto de las mismas.</li>
            <li style="text-align: justify;">La libertad de Elección y Contratación: El Representante del Educando goza del derecho a elegir y contratar el Centro Educativo que se adecúe a sus necesidades y capacidades económicas.</li>
            <li style="text-align: justify;">La información veraz, suficiente, clara y oportuna sobre los bienes y servicios: El Centro Educativo proporcionará al Representante del Educando la información completa sobre los bienes y servicios contratados, especialmente horarios de clases, grados y carreras autorizadas por el Ministerio de Educación, sistemas de evaluación, cursos adicionales, el monto de las cuotas tanto de inscripción como cuota mensual, así como de las actividades extraescolares de carácter voluntario u optativas que se puedan realizar durante el ciclo escolar respectivo.</li>
            <li style="text-align: justify;">Utilizar el Libro de Quejas o el medio legalmente autorizado por la Dirección de Atención y Asistencia al Consumidor para dejar registro de su disconformidad con respecto a un bien adquirido o servicio contratado: El Representante del Educando podrá hacer constar su inconformidad respecto al bien adquirido o el servicio contratado en el libro de quejas y esperar un período de ocho días para que la misma sea resuelta por las autoridades del Centro Educativo, transcurrido ese tiempo sin que exista una solución satisfactoria podrá interponer la queja correspondiente ante la Dirección de Atención y Asistencia al Consumidor -DIACO-, quien procederá según corresponda.</li>
            <li style="text-align: justify;">Observancia a las leyes y reglamentos en materia educativa. El Centro Educativo deberá velar por el cumplimiento de las normas aplicables en materia educativa, respetando los valores culturales y derechos inherentes del Educando en su calidad de ser humano, a su vez proporcionar conocimientos científicos, técnicos y humanísticos a través de una metodología adecuada, así como evaluar con objetividad y justicia.</li>
        </ol>
    </div>

    <div class="clause">
        <h6><strong>QUINTA: OBLIGACIONES DEL REPRESENTANTE DEL EDUCANDO.</strong></h6>
        <p style="text-align: justify;">El Representante del Educando, en armonía con el Artículo 5 de la Ley de Protección al Consumidor y Usuario, tendrá las siguientes obligaciones:</p>
        <ol type="a">
            <li style="text-align: justify;">Pagar al Centro Educativo por los servicios proporcionados en el tiempo, modo y condiciones establecidas mediante el presente contrato.</li>
            <li style="text-align: justify;">Utilizar los bienes y servicios en observancia a su uso normal, de conformidad con las especificaciones proporcionadas por el Centro Educativo y cumplir con las condiciones pactadas en el presente contrato, debiendo para tal efecto instruir al educando sobre el cuidado tanto de las instalaciones, como del mobiliario y equipo del Centro Educativo. En caso de daños y/o perjuicios ocasionados por el educando, el Representante del Educando será el responsable, siempre y cuando sean debidamente comprobados y atribuidos al mismo.</li>
            <li style="text-align: justify;">Ser orientadores en el proceso educativo de los educandos y velar porque cumplan con las obligaciones establecidas en las leyes y reglamentos internos del Centro Educativo.</li>
        </ol>
    </div>

    <div class="clause">
        <h6><strong>SEXTA: CUOTAS.</strong></h6>
        <p style="text-align: justify;">Como Representante del Educando me comprometo a efectuar únicamente los siguientes pagos, sin necesidad de cobro, ni requerimiento alguno:</p>
        <table>
            <tr>
                <th>EN CONCEPTO DE:</th>
                <th>LA CANTIDAD DE:</th>
            </tr>
            <tr>
                <td>a) INSCRIPCIÓN POR EDUCANDO: (UN SÓLO PAGO ANUAL)</td>
                <td>
                    <select style="border: none; background: transparent; font-size: 1em; padding: 0; appearance: none; -moz-appearance: none; -webkit-appearance: none; cursor: pointer;">
                        <option value="310">Q. 395.00</option>
                        <option value="330">Q. 430.00</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>b) COLEGIATURA MENSUAL: (10 CUOTAS EN LOS MESES DE ENERO A OCTUBRE)</td>
                <td>
                    <select style="border: none; background: transparent; font-size: 1em; padding: 0; appearance: none; -moz-appearance: none; -webkit-appearance: none; cursor: pointer;">
                        <option value="310">Q. 310.00</option>
                        <option value="330">Q. 330.00</option>
                        <option value="345">Q. 345.00</option>
                    </select>
                </td>
                </td>

            </tr>
        </table>
        <p style="text-align: justify;">Cuotas debidamente autorizadas por el Ministerio de Educación, según la siguiente resolución: a) No. AMP/DESPACHO/006-2020, de fecha 30 de diciembre de 2020, y b) DTP No. 093-2021, de fecha 10 de mayo de 2021; emitida por la Dirección Departamental de Educación de Izabal, valores que se informan a continuación:</p>
        <table>
            <tr>
                <th colspan="3">Jornada Matutina</th>
            </tr>
            <tr>
                <th>NIVEL DE EDUCACIÓN</th>
                <th>Inscripción</th>
                <th>Colegiatura mensual</th>
            </tr>
            <tr>
                <td>Preprimaria</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
            <tr>
                <td>Primaria</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
            <tr>
                <td>Ciclo Básico</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
        </table>
        <table>
            <tr>
                <th colspan="3">Jornada Vespertina</th>
            </tr>
            <tr>
                <th>NIVEL DE EDUCACIÓN</th>
                <th>Inscripción</th>
                <th>Colegiatura mensual</th>
            </tr>
            <tr>
                <td>Preprimaria</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
            <tr>
                <td>Ciclo Básico</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
            <tr>
                <td>Perito en Administración</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
            <tr>
                <td>Bachillerato en Ciencias y Letras con Orientación en Diseño Gráfico</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
            <tr>
                <td>Magisterio de Educación Infantil Intercultural</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
            <tr>
                <td>Bachillerato en Ciencias y Letras con Orientación en Computación</td>
                <td>Q. 430.00</td>
                <td>Q. 345.00</td>
            </tr>
        </table>

        <p style="text-align: justify;">Para el pago de las cuotas, ambas partes acordamos que sea en forma anticipada, debiendo efectuar el pago durante los últimos cinco días hábiles del mes al cual corresponde el servicio educativo brindado.</p>
    </div>

    <div class="clause">
        <h6><strong>SÉPTIMA: INCUMPLIMIENTO DEL PAGO.</strong></h6>
        <p style="text-align: justify;">En caso que el Representante del Educando se atrase o incumpla con los pagos normados en la cláusula anterior, el Centro Educativo podrá exigir al Representante del Educando el cumplimiento de las obligaciones contraídas en el presente contrato.</p>
    </div>

    <div class="clause">
        <h6><strong>OCTAVA: DERECHOS Y OBLIGACIONES DEL CENTRO EDUCATIVO:</strong></h6>
        <p style="text-align: justify;">De conformidad con la legislación aplicable y lo establecido en el presente contrato, tendrá los derechos siguientes:</p>
        <ol type="a">
            <li style="text-align: justify;">Exigir al Representante del Educando el cumplimiento de los contratos válidamente celebrados.</li>
            <li style="text-align: justify;">El libre acceso a los órganos administrativos y judiciales para la solución de conflictos que surjan por la prestación del servicio educativo.</li>
            <li style="text-align: justify;">Los demás que establecen las leyes del país.</li>
        </ol>
        <p style="text-align: justify;">El Centro Educativo deberá cumplir con lo siguiente:</p>
        <ol type="a">
            <li style="text-align: justify;">Atender los reclamos formulados por el Representante del Educando.</li>
            <li style="text-align: justify;">Generar mecanismos para la información continua con el Representante del Educando, así como crear espacios que promuevan el aprendizaje de los educandos.</li>
            <li style="text-align: justify;">Asegurar un ambiente escolar que favorezca la autoestima, resolución pacífica de problemas, el reconocimiento de la dignidad humana, el respeto y la valorización de las identidades étnicas y culturales, la equidad de género, la formación de valores y los derechos humanos.</li>
            <li style="text-align: justify;">Cumplir con las leyes tributarias del país en lo aplicable.</li>
            <li style="text-align: justify;">Las demás que establecen las leyes del país.</li>
        </ol>
    </div>

    <div class="clause">
        <h6><strong>NOVENA: CHEQUES RECHAZADOS.</strong></h6>
        <p style="text-align: justify;">Por concepto de cheques rechazados el Centro Educativo podrá cobrar como máximo el valor que por tal motivo debita o cobra el Banco que rechazó el pago del mismo.</p>
    </div>

    <div class="clause">
        <h6><strong>DÉCIMA: TRASLADO O RETIRO DEL EDUCANDO.</strong></h6>
        <p style="text-align: justify;">De conformidad con lo establecido por el artículo 38 del Acuerdo Gubernativo número 52-2015 o cualquier otra disposición legal aplicable, el traslado o retiro del educando podrá realizarse en cualquier momento del ciclo escolar a otro Centro Educativo ya sea privado o público, siempre y cuando se cumpla con las regulaciones que para el efecto emita la autoridad competente.</p>
        <p style="text-align: justify;">El Representante del Educando debe cancelar la cuota mensual hasta el mes en que efectivamente sea retirado el educando del plantel educativo, sin que esto sea motivo o justificación para retener el expediente.</p>
        <p style="text-align: justify;">El establecimiento que recibe al estudiante queda obligado a informar sobre el traslado a la Unidad de Planificación de la Dirección Departamental de Educación, manteniendo el mismo código personal del estudiante y dentro de los quince días siguientes de efectuado.</p>
    </div>

    <div class="clause">
        <h6><strong>DÉCIMA PRIMERA: COPIA DEL CONTRATO.</strong></h6>
        <p style="text-align: justify;">El Centro Educativo deberá entregar una copia del presente contrato, quedando el original en poder de la autoridad del mismo, con el propósito que cada uno de los comparecientes estén enterados de sus derechos y obligaciones para que los ejerciten y cumplan de conformidad con lo establecido. La copia será entregada al Representante del Educando al momento de firmar el contrato.</p>
        <p style="text-align: justify;">Ambas partes acuerdan que la legalización de las firmas del presente contrato, correrán por cuenta del Centro Educativo.</p>
    </div>

    <div class="clause">
        <h6><strong>DÉCIMA SEGUNDA: DERECHO DE RETRACTO.</strong></h6>
        <p style="text-align: justify;">El Representante del Educando tendrá derecho a retractarse dentro de un plazo no mayor de cinco días hábiles, contados a partir de la firma del contrato. Si ejercita oportunamente este derecho le serán restituidos en su totalidad los valores pagados, siempre que no hubiere hecho uso del servicio.</p>
    </div>

    <div class="clause">
        <h6><strong>DÉCIMA TERCERA: ACEPTACIÓN.</strong></h6>
        <p style="text-align: justify;">Nosotros los comparecientes, damos lectura íntegra al presente contrato, enterados de su contenido, objeto, validez y demás efectos legales, lo ratificamos, aceptamos y firmamos.</p>
    </div>

        <div class="signatures" style="display: flex; justify-content: center; align-items: center; gap: 5cm; text-align: center;">
            <div>
                <div class="signature-line" style="text-align: center; border-bottom: 1px solid rgba(0,0,0,0); width: 100%; position: relative; padding-bottom: 10px;">
                Herlindo Artiga Marroquín<br>Propietario
                </div>
            </div>
            <div>
               <center><div class="signature-line" style="border-bottom: 1px solid rgba(0,0,0,0); width: 50%;"></center>
                <input type="text" id="nombre-completo" name="nombre_completo"
                           value="{{ old('nombre_completo', $nombreCompleto ?? '') }}"
                           style="border: none; outline: none; background-color: transparent; width: auto; padding: 2px; min-width: 48ch; text-align: center;"
                           oninput="this.style.width = (this.value.length + 1) + 'ch';">
                    <br>Representante del Educando
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
        <div class="no-print">
        <button id="printButton" class="print-button">Imprimir Contrato</button>
    </div>
    <style>
        @media screen {
            .contract-container {
                max-width: 1300px;
                margin: 0 auto;
                padding: 20px;
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
            }
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .contract-container {
                font-family: Arial, sans-serif;
                font-size: 10px;
                line-height: 1.4;
                color: #000;
                background-color: #fff;
                padding: 15px;
            }

            .contract-body {
                text-align: justify;
            }

            .contract-body span,
            .contract-body input[type="date"],
            .contract-body input[type="number"] {
                display: inline;
                margin: 0;
            }

            input[type="date"],
            input[type="number"] {
                border: none;
                border-bottom: 1px solid #000;
                background-color: transparent;
                font-size: 10px;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .btn i {
                display: none;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: avoid;
            }

            th, td {
                border: 1px solid #000;
                padding: 5px;
                text-align: left;
            }

            h5 {
                page-break-after: avoid;
                text-align: left;
            }

            h6 {
                text-align: left;
            }

            p, ol, ul {
                page-break-inside: avoid;
            }

            .signatures {
                display: flex;
                justify-content: space-between;
                font-size: 12px; /* Reduce font size for signatures */
                margin-top: 10px; /* Reduce top margin */
            }

            .signature-line {
                width: 80%; /* Reduce width of signature line */
                margin-top: 10px; /* Reduce top margin of signature line */
                border-top: 1px solid #333;
                text-align: center;
            }

            .signature-line input {
                width: 100%; /* Asegura que el input ocupe todo el ancho disponible dentro de su contenedor */
                border: none;
                border-bottom: 1px solid transparent;
                background-color: transparent;
                padding: 2px;
                font-size: 20px;
            }

            .page-number {
                display: none;
            }

            /* Estilos específicos para el input de edad */
            #edad {
                width: 15px !important;
                -moz-appearance: textfield;
                appearance: textfield;
                margin: 0;
                padding: 0;
                text-align: center;
            }

            /* Ocultar las flechas del input number en todos los navegadores */
            #edad::-webkit-inner-spin-button,
            #edad::-webkit-outer-spin-button {
                -webkit-appearance: none;
                appearance: none;
                margin: 0;
            }

            /* Específicamente para el input de fecha */
            .contract-body input[type="date"] {
                width: 60px;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }


        }

        @page {
            margin: 10mm;
        }

        .contract-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .contract-body {
            text-align: justify;
        }

        .clause {
            margin-bottom: 20px;
        }

        h5 {
            color: #2c3e50;
            font-weight: bold;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-line {
            width: 110%;
            border-top: 1px solid #333;
            margin-top: 20px;
            text-align: center;
        }

        .signature-line input {
            width: 100%;
            border: none;
            border-bottom: 1px solid transparent;
            background-color: transparent;
            padding: 2px;
        }

        .print-button {
            background-color: #EAB308;
            color: white;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            border: none;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #CA8A04;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Estilos para inputs en línea dentro del texto */
        .contract-body input[type="date"],
        .contract-body input[type="number"] {
            display: inline-block;
            vertical-align: baseline;
            margin: 0;
            padding: 0 2px;
            height: auto;
            font-size: inherit;
            line-height: inherit;
            border: none;
            background: transparent;
            font-family: inherit;
        }

        /* Específicamente para el input de fecha */
        .contract-body input[type="date"] {
            width: 85px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Ocultar el ícono del calendario en WebKit */
        .contract-body input[type="date"]::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        /* Específicamente para el input de edad */
        .contract-body input[type="number"]#edad {
            width: 30px;
            min-width: 30px;
            max-width: 30px;
            -moz-appearance: textfield;
            appearance: textfield;
            margin: 0;
            text-align: center;
        }

        /* Ocultar los controles de incremento/decremento en Chrome, Safari, Edge, Opera */
        .contract-body input[type="number"]::-webkit-inner-spin-button,
        .contract-body input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .contract-body span,
        .contract-body input {
            line-height: inherit;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const printButton = document.getElementById('printButton');
            if (printButton) {
                printButton.addEventListener('click', function() {
                    this.textContent = 'Imprimiendo...';
                    window.print();
                    this.textContent = 'Descargar Contrato';
                });
            }

            const edadInput = document.getElementById('edad');
            if (edadInput) {
                edadInput.addEventListener('input', function() {
                    const edad = this.value;
                    const edadLetras = document.getElementById('edadLetras');
                    const edades = ['sesenta y seis', 'sesenta y siete', 'sesenta y ocho', 'sesenta y nueve', 'setenta'];
                    if (edadLetras) {
                        edadLetras.textContent = edades[edad - 66] || '';
                    }
                });
            }

            // Generar número de correlativo
            const correlativoElement = document.getElementById('correlativo');
            if (correlativoElement) {
                correlativoElement.textContent = Math.floor(Math.random() * 1000) + 1 + '-' + new Date().getFullYear();
            }

            // Establecer la fecha actual
            const fechaContratoElement = document.getElementById('fechaContrato');
            if (fechaContratoElement) {
                fechaContratoElement.valueAsDate = new Date();
            }
        });

    </script>
@endsection

