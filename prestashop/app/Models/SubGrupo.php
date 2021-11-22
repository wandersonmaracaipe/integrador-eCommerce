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
    public function pesquisaSubGrupoPrestashop($gdpNome)
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

    # Cadastra ou atualiza o subgrupo de produto do maxdata no prestashop
    public function addUpdateSubGrupoProdutoPrestashop($gdpId)
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
            dd('else grupo');
        }

        return $grupo;
    }

}
