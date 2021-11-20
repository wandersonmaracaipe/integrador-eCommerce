<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoEmpresa;

class SincronizarController extends Controller
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
        # Select na tabela produtos para buscar todos os produtos marcados para usar ecommerce
        $produtos = $this->produto->getProdutosMaxdata();

        # Percorrendo o array de produtos
        foreach ($produtos as $produto) {

            # Pesquisa o produto no prestashop
            $pesquisaProduto = $this->produto->pesquisaProdutoPrestashop($produto->proId);

            # Se count() igual a zero, cadastra o produto
            if($pesquisaProduto->count() == 0){
                $this->produto->sincronizarProdudo($produto);
            }


            echo '<pre>';
            echo 'ID: <b>' . $produto->proId . '</b><br/>';
            echo 'DESCRIÇÃO: <b>' . $produto->proDescricao . '</b><br/>';
            echo 'DESCRIÇÃO PDV: <b>' . $produto->proDescPdv . '</b><br/>';
            echo 'VL. VENDA: <b>' . $produto->proVenda . '</b><br/>';
            echo 'EMPRESA: <b>' . $produto->empId . '</b><br/>';
            echo '</pre>';
        }

        # dump and die
        # dd($produtos);

    }
}
