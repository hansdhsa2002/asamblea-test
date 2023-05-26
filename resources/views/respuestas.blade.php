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
                    <h1 class="text-center">¿Esta deacuerdo con la nocion?</h1>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-success btn-block" value="SI">SI</button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-danger btn-block" value="NO">NO</button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script !src="">
        $(function () {
        })
    </script>
@endsection
