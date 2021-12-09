@extends('layouts.base')
@section('conteudo')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard e-Commerce </h1>
        <span id="time"></span>

            <button type="submit" id="btnSincronizar"
                    onclick="sincronizaEcommerce()"
                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> Sincronizar</button>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countProduto"> ... </div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countVenda"> ... </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@section('script')


    <script type="text/javascript">

        function sincronizaEcommerce(){

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


                        setTimeout(sincronizaEcommerce, 120000);

                    } else {

                        element.innerHTML= ' Sincronizar';

                        document.getElementById("btnSincronizar").disabled = false;

                        alert('ERRO: Ocorreu um erro ao sincronizar')
                    }

                }
            });

        }

        function countProdutosEcommerce(){

            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({ headers: {'X-CSRF-TOKEN': _token} });

            $.ajax({
                url: "{{ route('countProduto') }}",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (response) {

                    if(response.success === true){

                        var element = document.getElementById('countProduto');

                        element.innerHTML = response.contador;

                        setTimeout(countProdutosEcommerce, 30000);

                    }

                }
            });
        }

        function countVendasEcommerce(){

            var _token = $('meta[name="_token"]').attr('content');

            $.ajaxSetup({ headers: {'X-CSRF-TOKEN': _token} });

            $.ajax({
                url: "{{ route('countVenda') }}",
                type: "POST",
                data: {},
                dataType: "JSON",
                success: function (response) {

                    if(response.success === true){

                        var element = document.getElementById('countVenda');

                        element.innerHTML = response.contador;

                        setTimeout(countVendasEcommerce, 30000);

                    }

                }
            });
        }


        $(document).ready(sincronizaEcommerce);
        $(document).ready(countProdutosEcommerce);
        $(document).ready(countVendasEcommerce);

    </script>
@stop
@endsection
