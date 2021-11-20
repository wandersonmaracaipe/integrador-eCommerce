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

        $countProdutos = $this->produto->produtosPrestashopIds()->count();
        $countVendas = $this->produto->vendasPrestashopIds()->count();

        return view('welcome', compact('countProdutos', $countProdutos, 'countVendas', $countVendas));

    }
}
