<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Product;

use PrestaShopBundle\Service\Hook\HookFinder;

/**
 * This class gets the extra content to display on the product page
 * from the modules hooked on displayProductExtraContent.
 */
class ProductExtraContentFinder extends HookFinder
{
    protected $hookName = 'displayProductExtraContent';
    protected $expectedInstanceClasses = array('PrestaShop\PrestaShop\Core\Product\ProductExtraContent');

    /**
     * Execute hook to get all addionnal product content, and check if valid
     * (not empty and only instances of class ProductExtraContent).
     *
     * @return array
     *
     * @throws \Exception
     */
    public function find()
    {
        // Check first that we have a product to send as params
        if (!array_key_exists('product', $this->params) || !$this->params['product'] instanceof \Product) {
            throw new \Exception('Required product param not found.');
        }

        return parent::find();
    }
}
