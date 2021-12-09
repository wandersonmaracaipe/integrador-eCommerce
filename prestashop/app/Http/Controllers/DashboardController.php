<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoEmpresa;

class DashboardController extends Controller
{
    private $produto;
    private $produto_empresa;

    public function __construct(Produto $produto, ProdutoEmpresa $produto_empresa)
    {
        $this->produto = $produto;
        $this->produto_empresa = $produto_empresa;
    }

    public function index()
    {

        return view('welcome');

    }

    public function countProduto()
    {
        $countProdutos['contador'] = $this->produto->produtosPrestashopIds()->count();

        $countProdutos['success'] = true;

        echo json_encode($countProdutos);
    }

    public function countVenda()
    {
        $countVendas['contador'] = $this->produto->vendasPrestashopIds()->count();

        $countVendas['success'] = true;

        echo json_encode($countVendas);
    }
}
