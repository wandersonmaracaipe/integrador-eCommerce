<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PrestaShopWebservice;
use PrestaShopWebserviceException;

class SubGrupo extends Model
{
    protected $table = 'subGrupoProd';
    protected $primaryKey = 'sgpId';
    protected $guarded = [];

    # Recupera todos os subgrupos de produtos do sistema maxdata
    public function getSubGruposProdutoMaxdata()
    {
        $subGruposProduto = self::all();

        return $subGruposProduto;
    }

    # Recupera apenas um subgrupo de produto do sistema maxdata
    public function getSubGrupo($sgpId)
    {
        $subGruposProduto = self::where('sgpId', (int)$sgpId)->first();

        return $subGruposProduto;

    }

    # Pesquisa o grupo de subproduto do maxdata no prestashop
    public function pesquisaSubGrupoPrestashop($sgpNome)
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), false);

            $xml = $webService->get([
                'resource' => 'categories',
                'display' => 'full',
                'filter[name]' => $sgpNome,
            ]);

            return $xml->categories->children();

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Error: <br />' . $ex->getMessage();
        }

    }

    # Cadastra ou atualiza o subgrupo de produto do maxdata no prestashop
    public function addUpdateSubGrupoProdutoPrestashop($sgpId, $idParent = NULL)
    {
        # Consulta o grupo por ID no sistema maxdata e armazena na variavel $grupo
        $subgrupo = self::getSubGrupo($sgpId);

        # Consulta o grupo pela descrição no prestashop
        $retornoPrestashop = self::pesquisaSubGrupoPrestashop($subgrupo->sgpNome);

        # Verificar o retorno da presquisa, se zero, cadastra, caso contrario, atualizamos apenas a descrição
        if ($retornoPrestashop->count() == 0) {

            # Enviamos o cadastro do grupo e armazenamos novamente na variavel grupo
            $subgrupo = self::addSubGrupoPrestashop($subgrupo, $idParent);

        } else {
            dd($retornoPrestashop);
        }

        return $subgrupo;
    }

    # Enviando o cadastro do subgrupo de produto maxdata para o prestashop
    public function addSubGrupoPrestashop($dadosSubGrupo, $idParent = NULL)
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), true);

            $xml = $webService->get(array('url' => env('PRESTASHOP_URL') . '/api/categories?schema=blank'));

            $category = $xml->children()->children();
            $category->name->language[0][0] = $dadosSubGrupo->sgpNome;
            $category->description->language[0][0] = $dadosSubGrupo->sgpNome;
            $category->link_rewrite->language[0][0] = $dadosSubGrupo->sgpNome;

            $category->active = $dadosSubGrupo->sgpDesativa;

            if ($idParent == NULL): $category->id_parent = 2;
            else: $category->id_parent = $idParent; endif;

            $category->is_root_category = 0;
            $opt = array('resource' => 'categories');

            $opt['postXml'] = $xml->asXML();
            $xml = $webService->add($opt);

            return $xml->category->children();

        } catch (PrestaShopWebserviceException $ex) {
            echo "Error:<br>" . $ex->getMessage();
            exit(1);
        }
    }

}
