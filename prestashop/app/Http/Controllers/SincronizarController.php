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
        $grupoPrestashop = null;
        $subgrupoPrestashop = null;


        # Select na tabela produtos para buscar todos os produtos marcados para usar ecommerce
        $produtos = $this->produto->getProdutosMaxdata();

        # Percorrendo o array de produtos
        foreach ($produtos as $produto) {
            # Verifica se valor do produto é maior que zero antes de sincronizar o mesmo.
            if ($produto->proVenda > 0 && !empty($produto->proGrupo)) {
                # Pesquisa o produto no prestashop
                $pesquisaProduto = $this->produto->pesquisaProdutoPrestashop($produto);

                # Se count() igual a zero, cadastra o produto
                if ($pesquisaProduto->count() == 0) {

                    # Se produto estiver vinculado em um grupo, enviamos o cadastro
                    if (!empty($produto->proGrupo)) {
                        # Envia o cadastro do grupo de produto para o prestashop para referenciar ao produto
                        $grupoPrestashop = $this->grupo->addUpdateGrupoProdutoPrestashop($produto->proGrupo);
                    }

                    # Se produto estiver vinculado em um subgrupo de produto, enviamos o cadastro
                    if ($produto->proSubGrupo != null) {
                        # Envia o cadastro do subgrup de produto para o prestashop e referencia o subgrupo ao grupo (idParent)
                        $subgrupoPrestashop = $this->subgrupo->addUpdateSubGrupoProdutoPrestashop($produto->proSubGrupo, $grupoPrestashop->id);
                    }

                    # Envia o cadastro do produto para o prestashop
                    $xmlProdPrestashop = $this->produto->sincronizarProdudo($produto, $grupoPrestashop, $subgrupoPrestashop);

                    # Atualiza o estoque atual do produto no prestashop
                    $this->produto->atualizaEstoque($xmlProdPrestashop, $produto);

                } else { # Caso contrario, atualiza o estoque do produto.

                    # Atualiza Dados do produto no prestashop
                    $this->produto->atualizaDadosProduto($pesquisaProduto, $produto);

                    # Atualiza o estoque atual do produto no prestashop
                    $this->produto->atualizaEstoque($pesquisaProduto, $produto);

                }
            }

        }

        # dump and die
        # dd($produtos);

        //$produto['success'] = true;
        $retorno['success'] = true;

        echo json_encode($retorno);

        return;

    }
}
