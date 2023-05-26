@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div id="votacion" class="text-center"><h1>Espere mientras se calcula el resultado...</h1></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div id="quorum" class="text-center"><h1>Espere mientras se calcula el Quorum..</h1></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mt-5" >
                <div class="card">
                    <div class="card-body">
                        <table class="table table-sm" id="dt-votacion" width="100%">
                            <thead>
                                <th>Representante/Apoderado</th>
                                <th>Tipo ID</th>
                                <th>ID</th>
                                <th>Empresa Afiliada</th>
                                <th>Afi. Totales</th>
                                <th>Afi. con Subsidio</th>
                                <th>Max Votación (n/15)+1</th>
                                <th>Votación Válida</th>
                                <th>Voto</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script !src="">
        $(function () {
            let data_dt = '';
            const dibujarQuorun = () =>{
                let html = '';
                $.get('quorun_actual', function (res) {
                    html = `<div class="row">
                                <div class="col-md-3 col-sm-3 text-center">
                                    <h4>Total empresas habilitadas</h4>
                                    <h5>${res.total_empresas.valor}</h5>
                                </div>
                                <div class="col-md-3 col-sm-3 text-center">
                                    <h4>Total empresas presentes ${res.total_empresas_presentes.porcentaje}%</h4>
                                    <h5>${res.total_empresas_presentes.valor}</h5>
                                </div>
                                <div class="col-md-3 col-sm-3 text-center">
                                    <h4>Empresas preinscritas ${res.total_registrados.porcentaje}%</h4>
                                    <h5>${res.total_registrados.valor}
                                </div>
                                <div class="col-md-3 col-sm-3 text-center">
                                    <h4>Quorum <br>${res.total_25.porcentaje}%</h4>
                                    <h5>${res.total_25.valor}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 offset-md-4">
                                    <a class="btn btn-danger btn-block" href="reporte_quorum">Descargar Quorum</a>
                                </div>
                            </div>`
                    $("#quorum").html(html);
                })
            }
            const dibujarVotacion = () => {
                let html = '';
                $.get('votacion_encurso', function (res) {
                    let total = res.total.SI + res.total.NO + res.total.BLANCO;
                    html = `<h1 class="text-center">${res.question.pregunta}</h1>
                            <div class="row">
                                <div class="col-md-4 col-sm-4 text-center">
                                    <h4>A FAVOR</h4>
                                    <h5>${res.total.SI} (${total> 0 ?(res.total.SI/total*100).toFixed(2):0}%)</h5>
                                </div>
                                <div class="col-md-4 col-sm-4 text-center">
                                    <h4>EN CONTRA</h4>
                                    <h5>${res.total.NO} (${total>0?(res.total.NO/total*100).toFixed(2):0}%)</h5>
                                </div>
                                <div class="col-md-4 col-sm-4 text-center">
                                    <h4>EN BLANCO</h4>
                                    <h5>${res.total.BLANCO} (${total>0?(res.total.BLANCO/total*100).toFixed(2):0}%)</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 offset-md-4">
                                    <a class="btn btn-danger btn-block" href="reporte_votacion/${res.question.id}">Descargar Reporte</a>
                                </div>
                            </div>`
                    $("#votacion").html(html);
                });
            }
            const dtvotacion = $("#dt-votacion").DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                pageLength: -1,
                scrollX: true,
                dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        text:   'Exportar PDF',
                        filename: function () { return `reporte_votacion_${getNameFile()}`}
                    },
                    {
                        extend: 'excelHtml5',
                        text:   'Exportar Excel',
                        filename: function () { return `reporte_votacion_${getNameFile()}`}
                    }
                ],
                ajax: '{{url('dt_votacion_encurso')}}',
                columns:[
                    {data: 'name'},
                    {data: 'tipo_id'},
                    {data: 'nit_sin_digito'},
                    {data: 'razon_social'},
                    {data: 'total_afiliados'},
                    {data: 'total_afi_subsidio'},
                    {data: 'votacion_habil'},
                    {data: 'votacion_valida'},
                    {
                        data: 'valor',
                        render: (data, type, row)=>{
                            if(data == "SI"){
                                return "A FAVOR"
                            }else if(data == "NO"){
                                return  "EN CONTRA"
                            }else if(data == "BLANCO"){
                                return "BLANCO"
                            }
                        }
                    },
                ]
            });

            function getNameFile() {
                let pregunta = $("#votacion > h1").text();
                let pregunta_sanitizada = pregunta.replace(' ','_')
                let dt = new Date();
                let full_date = `${dt.getDate().toString().padStart(2, '0')}_${(dt.getMonth()+1).toString().padStart(2, '0')}_${
                        dt.getFullYear().toString().padStart(4, '0')}_${
                        dt.getHours().toString().padStart(2, '0')}_${
                        dt.getMinutes().toString().padStart(2, '0')}_${
                        dt.getSeconds().toString().padStart(2, '0')}`
                return `${pregunta_sanitizada}_${full_date}`;
            }

            dibujarVotacion();
            dibujarQuorun();

            Echo.channel('home').listen('MessageSent', (e) => {
                dtvotacion.ajax.reload(null, false);
                dibujarVotacion();
            })

            Echo.channel('votos').listen('VotoMarcado', (e) => {
                dtvotacion.ajax.reload(null, false);
                dibujarVotacion();
            })

            Echo.channel('asistencia').listen('AsistenciaRegistrada', (e)=>{
                dibujarQuorun()
            })
        });
    </script>
@endsection
