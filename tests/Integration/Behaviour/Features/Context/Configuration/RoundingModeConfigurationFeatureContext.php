<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Exception;

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
                throw new Exception('Unknown config value for specific shop configuration for "rounding mode": ' . $value);
        }
    }
}
