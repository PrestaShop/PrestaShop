<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Cart\Calculation\SpecificPriceRules;

use SpecificPriceRule;
use Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;

abstract class AbstractSpecificPriceRuleTest extends AbstractCartCalculationTest
{

    const SPECIFIC_PRICE_RULES_FIXTURES = [
        1 => ['reductionType' => 'percentage', 'reduction' => 23, 'fromQuantity' => 1],
        2 => ['reductionType' => 'percentage', 'reduction' => 15, 'fromQuantity' => 2],
        3 => ['reductionType' => 'amount', 'reduction' => 23, 'fromQuantity' => 1],
        4 => ['reductionType' => 'amount', 'reduction' => 15, 'fromQuantity' => 2],
    ];

    /**
     * @var SpecificPriceRule[]
     */
    protected $specificPriceRules = [];

    public function tearDown()
    {
        foreach ($this->specificPriceRules as $rule) {
            $rule->resetApplication();
            $rule->delete();
        }
        $this->specificPriceRules=[];
        parent::tearDown();
    }

    protected function insertSpecificPriceRule($priceRuleId)
    {
        $fixtures = static::SPECIFIC_PRICE_RULES_FIXTURES;
        if (!isset($fixtures[$priceRuleId])) {
            throw new \Exception('Unknown specific cart rule with id #' . $priceRuleId);
        }
        $specificCartRuleFixture = $fixtures[$priceRuleId];
        $rule                    = new SpecificPriceRule;
        $rule->id_shop           = \Context::getContext()->shop->id;
        $rule->id_currency       = 0; // 0 = all
        $rule->id_country        = 0; // 0 = all
        $rule->id_group          = 0; // 0 = all
        if (isset($specificCartRuleFixture['price'])) {
            $rule->price = $specificCartRuleFixture['price'];
        } else {
            // -1 to keep original product price
            $rule->price = -1;
        }
        $rule->reduction_tax  = 1;
        $rule->name           = 'price rule name';
        $rule->reduction_type = $specificCartRuleFixture['reductionType'];
        $rule->reduction      = $specificCartRuleFixture['reduction'];
        $rule->from_quantity  = $specificCartRuleFixture['fromQuantity'];
        $rule->add();
        $this->specificPriceRules[] = $rule;

        $rule->apply();
    }
}
