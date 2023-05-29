<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>REPORTE DE VOTACIÓN</title>
    <link href="{{ public_path('css/print.css') }}" rel="stylesheet">
{{--    <link href="{{ asset('css/print.css') }}" rel="stylesheet">--}}
</head>
<body>
<header class="clearfix">
    <div id="logo">
        <img src="{{asset('assets/images/comfamiliar.png')}}" height="90px" alt="Comfamiliar">
    </div>
    <div id="company">
        <h2 class="name">Comfamiliar Cartagena y Bolívar</h2>
        <div>Barrio Centro, Ave. Escallón N° 34 - 62 edificio Banco de Bogotá Piso 2, 4 y 6</div>
        <div>+57 (5)641 1600 </div>
        <div><a href="https://comfamiliar.org.co/#">https://comfamiliar.org.co/#</a></div>
    </div>
</header>

<h2>ASAMBLEA GENERAL ORDINARIA DE EMPRESAS AFILIADAS A LA CAJA DE COMPENSACIÓN FAMILIAR DE CARTAGENA Y BOLÍVAR “COMFAMILIAR” VIGENCIA 2023</h2>
<h3>Cartagena de Indias D. T. y C., Junio 9, 2023</h3>
<h3>Hora de impresión: {{date('h:i:s A')}}</h3>
<table class="table table-striped table-sm" width="100%">
    <thead>
    <tr>
        <th colspan="3">Ítem: {{$datos_pregunta['question']->pregunta}}</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>A FAVOR</td>
            <td>EN CONTRA</td>
            <td>BLANCO</td>
        </tr>
        <tr>
            <td>{{$datos_pregunta['total']['SI']}}</td>
            <td>{{$datos_pregunta['total']['NO']}}</td>
            <td>{{$datos_pregunta['total']['BLANCO']}}</td>
        </tr>
        <tr>
            <td>{{$porcentaje_votos['P_VOTOS_SI']}}%</td>
            <td>{{$porcentaje_votos['P_VOTOS_NO']}}%</td>
            <td>{{$porcentaje_votos['P_VOTOS_BLANCO']}}%</td>
        </tr>

    </tbody>
</table>
<table width="100%">
    <thead>
        <tr>
            <th>Total empresas habilitadas</th>
            <th>Total empresas presentes</th>
            <th>Quorum 25%</th>
            <th>Votos presentes válidos</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{$total_empresas}}</td>
            <td>{{$numero_votantes}}</td>
            <td>{{floor($total_empresas * 0.25)}}</td>
            <td>{{$total_votos_validos}}</td>
        </tr>
    </tbody>
</table>
<table width="100%">
    <thead>
        <tr>
            <th>NIT</th>
            <th>EMPRESA</th>
            <th>VOTOS VALIDOS</th>
            <th>REPRESENTANTE LEGAL <br>Y/O APODERADO</th>
            <th>VOTACION</th>
        </tr>
    </thead>
    <tbody>
        @if($respuestas->count() > 0)
            @foreach($respuestas as $respuesta)
                <tr>
                    <td>{{$respuesta->nit_sin_digito}}</td>
                    <td>{{$respuesta->razon_social}}</td>
                    <td>{{$respuesta->votacion_valida}}</td>
                    <td>{{$respuesta->name}}</td>
                    @if($respuesta->valor == "SI")
                        <td>A FAVOR</td>
                    @elseif($respuesta->valor == "NO")
                        <td>EN CONTRA</td>
                    @elseif($respuesta->valor  == "BLANCO")
                        <td>EN BLANCO</td>
                    @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5">Sin registros.</td>
            </tr>
        @endif
    </tbody>
</table>
</body>
</html>
