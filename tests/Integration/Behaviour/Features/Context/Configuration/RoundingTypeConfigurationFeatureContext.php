<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Exception;
use Order;

class RoundingTypeConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^specific shop configuration for "rounding type" is set to round (each article|each line|cart total)$/
     */
    public function setRoundingMode($value)
    {
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
                throw new Exception('Unknown config value for specific shop configuration for "rounding type": ' . $value);
        }
    }
}
