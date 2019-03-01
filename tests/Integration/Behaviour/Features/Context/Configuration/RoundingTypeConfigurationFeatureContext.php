<?php

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Configuration;
use Order;
use Pack;

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
