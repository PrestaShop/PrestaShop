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

class RoundingModeConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^Specific shop configuration of "rounding mode" is set to (PS_ROUND_UP|PS_ROUND_DOWN|PS_ROUND_HALF_UP|PS_ROUND_HALF_DOWN|PS_ROUND_HALF_EVEN|PS_ROUND_HALF_ODD)$/
     */
    public function setRoundingMode($value)
    {
        $this->previousConfiguration['PS_PRICE_ROUND_MODE'] = Configuration::get('PS_PRICE_ROUND_MODE');
        switch ($value) {
            case 'PS_ROUND_UP':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_UP);
                break;
            case 'PS_ROUND_DOWN':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_DOWN);
                break;
            case 'PS_ROUND_HALF_UP':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_UP);
                break;
            case 'PS_ROUND_HALF_DOWN':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_DOWN);
                break;
            case 'PS_ROUND_HALF_EVEN':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_EVEN);
                break;
            case 'PS_ROUND_HALF_ODD':
                $this->setConfiguration('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_ODD);
                break;
            default:
                throw new \Exception('Unknown config value for specific shop configuration of "rounding mode": ' . $value);
                break;
        }
    }
}
