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
use Order;

class RoundingTypeConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^Specific shop configuration of "rounding type" is set to (ROUND_ITEM|ROUND_LINE|ROUND_TOTAL)$/
     */
    public function setRoundingMode($value)
    {
        $this->previousConfiguration['PS_ROUND_TYPE'] = Configuration::get('PS_ROUND_TYPE');
        switch ($value) {
            case 'ROUND_ITEM':
                $this->setConfiguration('PS_ROUND_TYPE', Order::ROUND_ITEM);
                break;
            case 'ROUND_LINE':
                $this->setConfiguration('PS_ROUND_TYPE', Order::ROUND_LINE);
                break;
            case 'ROUND_TOTAL':
                $this->setConfiguration('PS_ROUND_TYPE', Order::ROUND_TOTAL);
                break;
            default:
                throw new \Exception('Unknown config value for specific shop configuration of "rounding type": ' . $value);
                break;
        }
    }
}
