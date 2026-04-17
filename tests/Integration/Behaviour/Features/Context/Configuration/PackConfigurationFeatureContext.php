<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Exception;
use Pack;

class PackConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^specific shop configuration for "pack stock type" is set to decrement (packs only|products only|both packs and products)$/
     */
    public function specificShopConfigurationPackStockTypeOfIsSetTo($value)
    {
        switch ($value) {
            case 'packs only':
                $this->setConfiguration('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_ONLY);
                break;
            case 'products only':
                $this->setConfiguration('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PRODUCTS_ONLY);
                break;
            case 'both packs and products':
                $this->setConfiguration('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_BOTH);
                break;
            default:
                throw new Exception('Unknown config value for specific shop configuration for "pack stock type": ' . $value);
        }
    }
}
