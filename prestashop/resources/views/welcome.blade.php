@extends('layouts.base')
@section('conteudo')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard e-Commerce </h1>
        <span id="time"></span>

        <form name="formSincronizar">
            <button type="submit" onclick="sincronizar()" id="btnSincronizar"
                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> Sincronizar
            </button>
        </form>

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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $countProdutos }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $countVendas }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@section('script')
    <script src="http://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>

    <script type="text/javascript">

        $('form[name="formSincronizar"]').submit(function (event) {
            event.preventDefault();

            var element = document.getElementById('btnSincronizar');
            document.getElementById("btnSincronizar").disabled = true;
            element.innerHTML= '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sincronizando';

            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({ headers: {'X-CSRF-TOKEN': _token} });

            $.ajax({
                url: "{{ route('sincronizar') }}",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (response) {
                    if(response.success === true){
                        var element = document.getElementById('btnSincronizar');
                        element.innerHTML= ' Sincronizar';
                        document.getElementById("btnSincronizar").disabled = false;
                        document.location.reload(true);
                    }
                }
            });

        });
    </script>
@stop
@endsection
