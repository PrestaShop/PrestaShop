<?php

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Tools;

class CommonConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{

    /**
     * @Given /^Shop configuration of (.+) is set to (.+)$/
     */
    public function shopConfigurationOfIsSetTo($index, $value)
    {
        if ($index == 'PS_PRICE_ROUND_MODE') {
            Tools::$round_mode = null;
        }
        $this->setConfiguration($index, $value);
    }
}
