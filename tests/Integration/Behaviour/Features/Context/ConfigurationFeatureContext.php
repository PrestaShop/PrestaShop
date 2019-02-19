<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Tester\Exception\PendingException;
use Configuration;
use Pack;
use Product;
use StockAvailable;

class ConfigurationFeatureContext implements BehatContext
{
    protected $previousConfiguration = [];

    /**
     * @Given Shop configuration of :index is set to :value
     */
    public function shopConfigurationOfIsSetTo($index, $value)
    {
        $this->previousConfiguration[$index] = Configuration::get($index);
        Configuration::set($index, $value);
    }

    /**
     * This hook can be used to reset changed configuration
     *
     * @AfterScenario
     */
    public function afterScenario_cleanConfiguration()
    {
        // delete products
        foreach ($this->previousConfiguration as $index => $value) {
            Configuration::set($index, $value);
        }
        $this->previousConfiguration = [];
    }

}
