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

namespace Tests\Integration\Behaviour\Features\Context\Modifier;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Tests\Integration\Behaviour\Features\Context\ProductFeatureContext;
use Behat\Behat\Context\Context as BehatContext;

/**
 * This Modifier enables the use of tag '@test-with-modifier-virtual-products'.
 * Scenarios marked with this tag will be run with virtual products instead of standard products.
 */
class ProductFeatureContextModifier implements BehatContext
{
    const PROFILE_VIRTUAL_PRODUCTS = 'virtual_products';

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $parameters = $scope->getEnvironment()->getSuite()->getSetting('parameters');

        if (false === array_key_exists('profile', $parameters)) {
            return;
        }

        $profile = $parameters['profile'];

        if ($profile !== self::PROFILE_VIRTUAL_PRODUCTS) {
            return;
        }

        $productFeatureContext = $scope->getEnvironment()->getContext(ProductFeatureContext::class);
        $productFeatureContext->activateCreateVirtualProductsModifier();
    }
}
