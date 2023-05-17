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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\CartRule;

use Tests\Resources\DatabaseDump;

class CommonCartRuleFeatureContext extends AbstractCartRuleFeatureContext
{
    /**
     * @BeforeScenario @restore-cart-rules-before-scenario
     *
     * @return void
     */
    public static function restoreCartRules(): void
    {
        DatabaseDump::restoreTables([
            'cart_rule',
            'cart_rule_shop',
            'cart_rule_lang',
            'cart_cart_rule',
            'cart_rule_carrier',
            'cart_rule_combination',
            'cart_rule_country',
            'cart_rule_group',
            'cart_rule_product_rule',
            'cart_rule_product_rule_group',
            'cart_rule_product_rule_value',
        ]);
    }
}
