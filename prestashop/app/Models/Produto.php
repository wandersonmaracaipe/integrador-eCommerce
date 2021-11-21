<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PrestaShopWebservice;
use PrestaShopWebserviceException;


class Produto extends Model
{
    # Table view por questões de segurança
    protected $table = 'v_produto';
    protected $primaryKey = 'proId';
    protected $guarded = [];

    # Recupera todos os produtos do sistema maxdata
    public function getProdutosMaxdata()
    {
        $produtos = self::where('proUsaEcommerce', 1)
            ->where('empId', 2)
            ->get();

        return $produtos;
    }

    # Verifica se produto já esta cadastro no prestashop
    public function pesquisaProdutoPrestashop($produto)
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), false);

            # Referencia/ SKU do produto
            if (!empty($produto->proCodigoSKU)) {
                $reference = $produto->proCodigoSKU;
            } else {
                $reference = $produto->proId;
            }

            $xml = $webService->get([
                'resource' => 'products',
                'display' => 'full',
                'filter[reference]' => $reference,
            ]);

            return $xml->products->children();

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Error: <br />' . $ex->getMessage();
        }
    }

    # Sincronizando produdo maxdata para o prestashop
    public function sincronizarProdudo($produto)
    {
        try { # Conectando ao prestashop
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), true);
            $xml = $webService->get(array('url' => env('PRESTASHOP_URL') . '/api/products?schema=blank'));

            # Montando XML de cadastro do produto
            $product = $xml->children()->children();

            # Descrição do produto
            $product->name->language[0][0] = $produto->proDescricao;
            $product->name->language[0][0]['id'] = 1;
            $product->name->language[0][0]['xlink:href'] = env('PRESTASHOP_URL') . '/api/languages/' . 1;

            # Descrição completa do produto
            $product->description->language[0][0] = $produto->proAplicacao;
            $product->description->language[0][0]['id'] = 1;
            $product->description->language[0][0]['xlink:href'] = env('PRESTASHOP_URL') . '/api/languages/' . 1;

            # Descrição curta do produto
            $product->description_short->language[0][0] = $produto->proDescPdv;
            $product->description_short->language[0][0]['id'] = 1;
            $product->description_short->language[0][0]['xlink:href'] = env('PRESTASHOP_URL') . '/api/languages/' . 1;

            # Preço de venda do produto
            $product->price = $produto->proVenda;
            $product->wholesale_price = $produto->proVenda;

            # Referencia/ SKU do produto
            if (!empty($produto->proCodigoSKU)) {
                $product->reference = $produto->proCodigoSKU;
            } else {
                $product->reference = $produto->proId;
            }

            # Outros dados
            $product->active = '1';
            $product->state = '1';
            $product->advanced_stock_management = '1';
            $product->on_sale = 0;
            $product->show_price = 1;
            $product->available_for_order = 1;
            $product->unit_price_ratio = 10;
            $product->depends_on_stock = 0;
            $product->width = $produto->proLargura;
            $product->height = $produto->proAltura;
            $product->depth = $produto->proComprimento;
            $product->weight = $produto->proPeso;

            $category_id = 2; // Categoria Inicio = 2
            $product->associations->categories->addChild('category')->addChild('id', $category_id);
            $product->id_category_default = $category_id;

            $opt = array('resource' => 'products');
            $opt['postXml'] = $xml->asXML();
            $xml = $webService->add($opt);

            return $xml;

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }
    }

    # Atualiza dos dados do produto
    public function atualizaDadosProduto($xmlProduto, $produto) {
        try { # Conectando ao prestashop
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), true);

            $xml = $webService->get([
                'resource' => 'products',
                'id' => (int) $xmlProduto->product->id,
            ]);

            # Montando XML de cadastro do produto
            $product = $xml->product->children();

            # Remove tags do XML
            unset($product->manufacturer_name);
            unset($product->position_in_category);
            unset($product->quantity);

            # Descrição do produto
            $product->name->language[0][0] = $produto->proDescricao;

            # Descrição completa do produto
            $product->description->language[0][0] = $produto->proAplicacao;

            # Descrição curta do produto
            $product->description_short->language[0][0] = $produto->proDescPdv;

            # Preço de venda do produto
            $product->price = $produto->proVenda;
            $product->wholesale_price = $produto->proVenda;
            $product->width = $produto->proLargura;
            $product->height = $produto->proAltura;
            $product->depth = $produto->proComprimento;
            $product->weight = $produto->proPeso;

            $updatedXml = $webService->edit([
                'resource' => 'products',
                'id' => (int) $product->id,
                'putXml' => $xml->asXML(),
            ]);

            return $updatedXml->product->children();

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }
    }

    # Atualiza estoque do produto
    public function atualizaEstoque($xmlProduto, $produto)
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), false);

            $stockAvailableXml = $webService->get([
                'resource' => 'stock_availables',
                'id' => $xmlProduto->product->associations->stock_availables->stock_available->id
            ]);

            $dataStockAvailable = $stockAvailableXml->stock_available->children();

            # intval($produto->proEstoqueAtual) : Converte float para inteiro (Estoque Atual Maxdata tipo fload)
            $dataStockAvailable->quantity = intval($produto->proEstoqueAtual);

            $updatedXml = $webService->edit([
                'resource' => 'stock_availables',
                'id' => $xmlProduto->product->associations->stock_availables->stock_available->id,
                'putXml' => $stockAvailableXml->asXML()
            ]);

            return $updatedXml;

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Error: <br />' . $ex->getMessage();
        }

    }

    # Obtem a quantidade de produtos sincronizados com o prestashop
    public function produtosPrestashopIds()
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), false);

            $xml = $webService->get([
                'resource' => 'products',
            ]);

            return $xml->products->children();

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Error: <br />' . $ex->getMessage();
        }
    }

    # Obtem a quantidade de produtos sincronizados com o prestashop
    public function vendasPrestashopIds()
    {
        try {
            $webService = new PrestaShopWebservice(env('PRESTASHOP_URL'), env('PRESTASHOP_KEY'), false);

            $xml = $webService->get([
                'resource' => 'orders',
            ]);

            return $xml->orders->children();

        } catch (PrestaShopWebserviceException $ex) {
            echo 'Error: <br />' . $ex->getMessage();
        }
    }
}
