<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
