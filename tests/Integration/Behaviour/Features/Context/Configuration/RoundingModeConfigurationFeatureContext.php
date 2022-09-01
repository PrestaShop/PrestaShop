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

class RoundingModeConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^specific shop configuration for "rounding mode" is set to round (up|down|half up|half down|half even|half even|half odd)$/
     */
    public function setRoundingMode($value)
    {
        switch ($value) {
            case 'up':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_UP);
                break;
            case 'down':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_DOWN);
                break;
            case 'half up':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_UP);
                break;
            case 'half down':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_DOWN);
                break;
            case 'half even':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_EVEN);
                break;
            case 'half odd':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_ODD);
                break;
            default:
                throw new \Exception('Unknown config value for specific shop configuration for "rounding mode": ' . $value);
        }
    }
}
