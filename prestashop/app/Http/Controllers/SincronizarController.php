<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Produto;
use App\Models\ProdutoEmpresa;
use App\Models\SubGrupo;

class SincronizarController extends Controller
{
    private $produto;
    private $produto_empresa;
    private $grupo;
    private $subgrupo;

    public function __construct(Produto $produto, ProdutoEmpresa $produto_empresa, Grupo $grupo, SubGrupo $subgrupo)
    {
        $this->produto = $produto;
        $this->produto_empresa = $produto_empresa;
        $this->grupo = $grupo;
        $this->subgrupo = $subgrupo;
    }

    public function sincroniza()
    {

        # Select na tabela produtos para buscar todos os produtos marcados para usar ecommerce
        $produtos = $this->produto->getProdutosMaxdata();

        # Percorrendo o array de produtos
        foreach ($produtos as $produto) {

            # Pesquisa o produto no prestashop
            $pesquisaProduto = $this->produto->pesquisaProdutoPrestashop($produto);

            # Se count() igual a zero, cadastra o produto
            if($pesquisaProduto->count() == 0){

                # Envia o cadastro do produto para o prestashop
                $xmlProdPrestashop = $this->produto->sincronizarProdudo($produto);

                # Atualiza o estoque atual do produto no prestashop
                $this->produto->atualizaEstoque($xmlProdPrestashop, $produto);

            } else{

                # Atualiza Dados do produto no prestashop
                $this->produto->atualizaDadosProduto($pesquisaProduto, $produto);

                # Atualiza o estoque atual do produto no prestashop
                $this->produto->atualizaEstoque($pesquisaProduto, $produto);

            }

        }

        # dump and die
        # dd($produtos);

       return redirect()->route('dashboard');

    }
}
