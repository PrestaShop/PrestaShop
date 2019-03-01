<?php

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Behat\Behat\Context\Context as BehatContext;
use Configuration;

abstract class AbstractConfigurationFeatureContext implements BehatContext
{
    protected $previousConfiguration = [];

    protected function setConfiguration($index, $value)
    {
        $this->previousConfiguration[$index] = Configuration::get($index);
        Configuration::set($index, $value);
    }

    /**
     * This hook can be used to reset changed configuration
     *
     * @AfterScenario
     */
    public function cleanConfiguration()
    {
        // delete products
        foreach ($this->previousConfiguration as $index => $value) {
            Configuration::set($index, $value);
        }
        $this->previousConfiguration = [];
    }

}
