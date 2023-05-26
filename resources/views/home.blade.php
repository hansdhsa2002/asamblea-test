@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div id="pregunta" class="text-center"><h2>ESPERE MIENTRAS SE ASIGNA LA PREGUNTA...</h2></div>

                </div>
            </div>

            <div class="card mt-5">
                <div class="card-body">
                    <h5>Historial de votación</h5>
                    <table id="tablehistorial" class="table table-striped table-sm table-bordered table-hover" width="100%">
                        <thead>
                        <th>Pregunta</th>
                        <th>Fecha y hora</th>
                        <th>Voto</th>
                        </thead>
                        <tbody>
                            <td colspan="3" class="text-center">Sin registros aun.</td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@section('js')
    <script !src="">
        $(function () {
            const dibujarPregunta = () =>{
                let html = '';
                $.get('pregunta_encurso', function (res) {
                    //console.log(res);
                    html = `<h2 class="text-center">${res.pregunta}</h2>
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <button class="btn btn-success btn-block btn-voto" value="SI">A FAVOR</button>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <button class="btn btn-danger btn-block btn-voto" value="NO">EN CONTRA</button>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <button class="btn btn-secondary btn-block btn-voto" value="BLANCO">EN BLANCO</button>
                                </div>
                            </div>`
                    $("#pregunta").html(html);
                }).fail(function (res) {
                   // $("#pregunta").html(`<h2 class="text-center">${res.responseText}</h2><button class="btn btn-success btn-proxima">Próximo ítem > </button>`)
                   $("#pregunta").html(`<h2 class="text-center">Usted ya ha efectuado su voto de manera exitosa, por favor espere que se active el próximo ítem de votación.</h2>`)
                });
            }
            const dibujarHistorialVotacion = () => {
                let table = '';
                $.get('historial_votacion', function (res) {
                    if (res.length > 0){

                        $(res).each(function (index, value) {
                            let valor_mostrar = '';
                            if(value.valor == 'SI'){
                                valor_mostrar = 'A FAVOR'
                            }else if(value.valor == 'NO'){
                                valor_mostrar = 'EN CONTRA'
                            }else if(value.valor == 'BLANCO'){
                                valor_mostrar = 'EN BLANCO'
                            }
                            table += `<tr><td>${value.pregunta}</td><td>${value.created_at}</td><td>${valor_mostrar}</td></tr>`
                        })
                    $("#tablehistorial > tbody").html(table);
                    }
                });

            }
            dibujarPregunta()
            dibujarHistorialVotacion()
            try {
                Echo.channel('home').listen('MessageSent', (e)=>{
                    dibujarPregunta()
                })
            } catch (e) {
                console.log("No se pudo conectar al websocket")
            }



            $("#pregunta").on('click','.btn-voto', function () {
                let valor = $(this).val();
                $.post('registrar_respuesta', {valor}, function (resp) {
                    alert("Voto registrado exitosamente.")
                    dibujarHistorialVotacion()
                    dibujarPregunta()
                }).fail( function(res) {
                    alert(res.responseText);
                })
            });
            $("#pregunta").on('click','.btn-proxima', function () {
                dibujarPregunta();
            })
        })
    </script>
@endsection
