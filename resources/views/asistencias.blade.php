@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-5" >
                <div class="card">
                    <div class="card-body">
                        <table class="table table-sm table-hover table-bordered table-striped" id="dt-asistencia" width="100%">
                            <thead>
                                <th>Cedula</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Empresas</th>
                                <th></th>
                                <th></th>
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
            const dtasistencia= $("#dt-asistencia").DataTable({
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
                        filename: function () { return `reporte_asistencia_${Date.now()}`}
                    },
                    {
                        extend: 'excelHtml5',
                        text:   'Exportar Excel',
                        filename: function () { return `reporte_asistencia_${Date.now()}}`}
                    }
                ],
                ajax: '{{url('dt_asistencia')}}',
                columns:[
                    {data: 'cedula'},
                    {data: 'name'},
                    {data: 'email'},
                    {
                        data: null,
                        render: (data, type, row)=>{
                            let html = ''
                            let len = row.empresas.length;
                            row.empresas.forEach(function(empresa, index){
                                html += `<b>${empresa.nit_sin_digito}</b> - ${empresa.razon_social} ${index < len-1 ?' <br> ':''}`
                            })
                            return html
                        }
                    },
                    {
                        data: null,
                        render: (data, type, row)=>{
                            return `<button class="btn btn-sm btn-asistencia btn-${row.asistio ? 'success':'danger'}" value="${row.id}">${row.asistio?'Presente':'Ausente'}</button>`;
                        }
                    },
                    {
                        data: null,
                        render: (data, type, row)=>{
                            return `<button class="btn btn-sm btn-password btn-warning" value="${row.id}">Resetear</button>`;
                        }
                    }
                ]
            });

            $("#dt-asistencia").on('click','.btn-asistencia',function (e) {
                let user_id = this.value;
                $.post('{{url('marcar_asistencia')}}', {user_id: user_id}, function () {
                    dtasistencia.ajax.reload(false, null);
                })
            })
            $("#dt-asistencia").on('click','.btn-password',function (e) {
                let user_id = this.value;
                $.post('{{url('reset_password')}}', {user_id: user_id}, function (res) {
                    dtasistencia.ajax.reload(false, null);
                    alert(`Contraseña asignada: ${res}`)
                })
            })
        });
    </script>
@endsection
