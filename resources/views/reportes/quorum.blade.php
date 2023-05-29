<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>REPORTE DE QUORUM</title>
    {{--    <link href="{{ public_path('css/print.css') }}" rel="stylesheet">--}}
    <link href="{{ public_path('css/print.css') }}" rel="stylesheet">
</head>
<body>
<header class="clearfix">
    <div id="logo">
        <img src="{{asset('assets/images/comfamiliar.png')}}" height="90px" alt="Comfamiliar">
    </div>
    <div id="company">
        <h2 class="name">Comfamiliar Cartagena y Bolívar</h2>
        <div>Barrio Centro, Ave. Escallón N° 34 - 62 edificio Banco de Bogotá Piso 2, 4 y 6</div>
        <div>+57 (5) 641 1600</div>
        <div><a href="https://comfamiliar.org.co/#">https://comfamiliar.org.co/#</a></div>
    </div>
</header>

<h2>ASAMBLEA GENERAL ORDINARIA DE EMPRESAS AFILIADAS A LA CAJA DE COMPENSACIÓN FAMILIAR DE CARTAGENA Y BOLÍVAR “COMFAMILIAR” VIGENCIA 2023</h2>
<h3>Cartagena de Indias D. T. y C., Junio 9, 2020</h3>
<h3>Hora de impresión: {{date('h:i:s A')}}</h3>
<table>
    <thead>
    <tr>
        <th>Total empresas <br> habilitadas</th>
        <th>Total empresas <br> presentes</th>
        <th>Total empresas <br> registradas</th>
        <th>Quorum 25%</th>
        <th>Votos presentes <br> válidos</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{$informacion['total_empresas']['porcentaje']}}%</td>
        <td>{{$informacion['total_empresas_presentes']['porcentaje']}}%</td>
        <td>{{$informacion['total_registrados']['porcentaje']}}%</td>
        <td>{{floor($informacion['total_25']['porcentaje'])}}%</td>
        <td></td>
    </tr>
    <tr>
        <td>{{$informacion['total_empresas']['valor']}}</td>
        <td>{{$informacion['total_empresas_presentes']['valor']}}</td>
        <td>{{$informacion['total_registrados']['valor']}}</td>
        <td>{{floor($informacion['total_25']['valor'])}}</td>
        <td>{{$total_votos_validos}}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
