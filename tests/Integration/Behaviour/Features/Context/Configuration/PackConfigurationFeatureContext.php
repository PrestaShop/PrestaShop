<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Pack;

class PackConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^specific shop configuration for "pack stock type" is set to decrement (packs only|products only|both packs and products)$/
     */
    public function specificShopConfigurationPackStockTypeOfIsSetTo($value)
    {
        $this->previousConfiguration['PS_PACK_STOCK_TYPE'] = Configuration::get('PS_PACK_STOCK_TYPE');
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
                throw new \Exception('Unknown config value for specific shop configuration for "pack stock type": ' . $value);
                break;
        }
    }
}
