@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card my-3">
                <div class="card-body">
                    <form id="form-pregunta">
                        <div class="input-group mb-3">
                            <input type="text" name="pregunta" class="form-control" placeholder="Nueva pregunta" aria-label="Nueva pregunta" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">Agregar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-bordered table-sm" width="100%" id="dt-preguntas">
                        <thead>
                        <th>ID</th>
                        <th>Pregunta</th>
                        <th>Acciones</th>
                        <th></th>
                        </thead>
                        <tbody>

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
            let dtpreguntas = $("#dt-preguntas").DataTable({
                scrollX: true,
                ajax: '{{url('preguntas')}}',
                columns:[
                    {data: 'id'},
                    {data: 'pregunta'},
                    {
                        data: null,
                        class: 'text-center',
                        render: (data, type, row)=>{
                            return `<button class="btn btn-sm btn-estado btn-${row.estado ? 'success':'danger'}" value="${row.id}">${row.estado ? 'Activo':'Inactivo'}</button>`
                        }
                    },
                    {
                        data: null,
                        class: 'text-center',
                        render: (data, type, row)=>{
                            return `<a class="btn btn-sm btn-warning" href="reporte_votacion/${row.id}">Votación</a>`
                        }
                    }
                ]
            });
            $("#dt-preguntas").on('click','.btn-estado', function () {
                let id_pregunta = $(this).val();
                $.ajax({
                    url: '{{url('cambiar_estado')}}',
                    type: 'PUT',
                    data: {id_pregunta},
                    success: function (res) {
                        dtpreguntas.ajax.reload(false, null)
                    }
                })
            })
            $("#form-pregunta").submit(function (e) {
                e.preventDefault()
                let data = $(this).serialize();
                $.post('preguntas', data, function (res) {
                    alert("Pregunta Registrada");
                    dtpreguntas.ajax.reload(false, null)
                })
            })
        })
    </script>
@endsection
