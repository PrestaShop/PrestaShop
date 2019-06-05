<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Order;

class RoundingTypeConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^specific shop configuration for "rounding type" is set to round (each article|each line|cart total)$/
     */
    public function setRoundingMode($value)
    {
        $this->previousConfiguration['PS_ROUND_TYPE'] = Configuration::get('PS_ROUND_TYPE');
        switch ($value) {
            case 'each article':
                $this->setConfiguration('PS_ROUND_TYPE', Order::ROUND_ITEM);
                break;
            case 'each line':
                $this->setConfiguration('PS_ROUND_TYPE', Order::ROUND_LINE);
                break;
            case 'cart total':
                $this->setConfiguration('PS_ROUND_TYPE', Order::ROUND_TOTAL);
                break;
            default:
                throw new \Exception('Unknown config value for specific shop configuration for "rounding type": ' . $value);
                break;
        }
    }
}
