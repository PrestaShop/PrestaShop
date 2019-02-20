<?php

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Pack;

class PackConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{

    /**
     * @Given /^Specific shop configuration of "pack stock type" is set to (STOCK_TYPE_PACK_ONLY|STOCK_TYPE_PRODUCTS_ONLY|STOCK_TYPE_PACK_BOTH)$/
     */
    public function specificShopConfigurationPackStockTypeOfIsSetTo($value)
    {
        $this->previousConfiguration['PS_PACK_STOCK_TYPE'] = Configuration::get('PS_PACK_STOCK_TYPE');
        switch ($value) {
            case 'STOCK_TYPE_PACK_ONLY':
                $this->setConfiguration('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_ONLY);
                break;
            case 'STOCK_TYPE_PRODUCTS_ONLY':
                $this->setConfiguration('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PRODUCTS_ONLY);
                break;
            case 'STOCK_TYPE_PACK_BOTH':
                $this->setConfiguration('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_BOTH);
                break;
            default:
                throw new \Exception('Unknown config value for specific shop configuration of "pack stock type": ' . $value);
                break;
        }
    }
}
