<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PrestaShopWebservice;
use PrestaShopWebserviceException;

class Grupo extends Model
{

    protected $table = 'grupoProd';
    protected $primaryKey = 'gdpId';
    protected $guarded = [];

    # Recupera todos os grupos de produtos do sistema maxdata
    public function getGruposProdutoMaxdata()
    {
        $gruposProduto = self::all();

        return $gruposProduto;
    }

    # Recupera apenas um grupo de produto do sistema maxdata
    public function getGrupo($gdpId)
    {
        $grupoProduto = self::where('gdpId', (int) $gdpId)->first();

        return $grupoProduto;
    }

    # Pesquisa o grupo de produto do maxdata no prestashop
    public function pesquisaGrupoPrestashop($gdpNome)
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), false);

            $xml = $webService->get([
                'resource' => 'categories',
                'display' => 'full',
                'filter[name]' => $gdpNome,
            ]);

            return $xml->categories->children();

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Error: <br />' . $ex->getMessage();
        }

    }

    # Cadastra ou atualiza o grupo de produto do maxdata no prestashop
    public function addUpdateGrupoProdutoPrestashop($gdpId)
    {
        # Consulta o grupo por ID no sistema maxdata e armazena na variavel $grupo
        $grupo = self::getGrupo($gdpId);

        # Consulta o grupo pela descrição no prestashop
        $retornoPrestashop = self::pesquisaGrupoPrestashop($grupo->gdpNome);

        # Verificar o retorno da presquisa, se zero, cadastra, caso contrario, atualizamos apenas a descrição
        if ($retornoPrestashop->count() == 0) {
            # Enviamos o cadastro do grupo e armazenamos novamente na variavel grupo
            $grupo = self::addGrupoPrestashop($grupo);
        } else {
            # retornar ID do grupo existente
            return $retornoPrestashop->children();
        }

        return $grupo;
    }

    # Enviando o cadastro do grupo de produto maxdata para o prestashop
    public function addGrupoPrestashop($dadosGrupo, $idParent = NULL)
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), true);

            $xml = $webService->get(array('url' => env('PRESTASHOP_URL') . '/api/categories?schema=blank'));

            $category = $xml->children()->children();
            $category->name->language[0][0] = $dadosGrupo->gdpNome;
            $category->description->language[0][0] = $dadosGrupo->gdpNome;
            $category->link_rewrite->language[0][0] = $dadosGrupo->gdpNome;

            $category->active = $dadosGrupo->gdpDesativa;

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
