@extends('layouts.base')
@section('conteudo')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard e-Commerce </h1>
        <span id="time"></span>
        <a href="{{ route('sincronizar') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> Sincronizar</a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total produtos -->
        <div class="col-xl-3 col-md-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Produtos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Vendas -->
        <div class="col-xl-3 col-md-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Vendas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
