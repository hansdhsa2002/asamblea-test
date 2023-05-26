
@extends('layouts.app')
@section('css')
	<link href="/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form class="form-row" id="form-registrar">
                        <div class="form-group col-md-12">
                            <label for="name" >{{ __('Nombres y Apellidos') }}</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}"  autofocus>
                        </div>


                        <div class="form-group col-md-4">
                            <label for="cedula" >{{ __('Cedula') }}</label>
                            <input id="cedula" type="text" class="form-control" name="cedula" value="{{ old('cedula') }}">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="email" >{{ __('Correo') }}</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" >

                        </div>
                        <div class="form-group col-md-4">
                            <label for="rol_user" >{{ __('Rol') }}</label>
                            <select id="rol_user" type="email" class="form-control" name="rol_user" >
                                @foreach($roles as $rol)
                                    <option value="{{$rol->name}}">{{$rol->name}}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="form-group col-md-12">
                            <label for="email" >{{ __('Empresas que representa') }}</label>
                            <select name="empresa_nit[]" class="multiple-select2 form-control" multiple="multiple">
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="password" >{{ __('Contraseña') }}</label>
                            <input id="password" type="password" class="form-control" name="password" >
                        </div>

                        <div class="form-group col-md-6">
                            <label for="password-confirm" >{{ __('Confirmar Contraseña') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" >
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Registrar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script src="/assets/plugins/select2/dist/js/select2.min.js"></script>
    <script !src="">
        $(function () {
            $(".multiple-select2").select2({
                placeholder: "Seleccione las Empresas",
                tags: true,
                tokenSeparators: [','],
                ajax: {
                    dataType: 'json',
                    url: '{{ url("s2_empresas") }}',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        }
                    },
                    processResults: function (data, page) {
                        return {
                            results: data
                        };
                    },
                }
            });
            $("#form-registrar").submit(function (e) {
                e.preventDefault()
                let data = $(this).serialize();
                $.post('{{url('registrar')}}', data, function (res) {
                    alert('Usuario registrado exitosamente!')
                    $("#form-registrar")[0].reset();
                    $(".multiple-select2").empty();
                }).fail(function (res) {
                    console.log(res)
                    let html = '';
                    alert(res.responseText);
                });
            })
        })
    </script>
@endsection
