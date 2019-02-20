<?php

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

class CommonConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{

    /**
     * @Given Shop configuration of :index is set to :value
     */
    public function shopConfigurationOfIsSetTo($index, $value)
    {
        $this->setConfiguration($index, $value);
    }
}
