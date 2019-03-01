<?php

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Pack;

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
