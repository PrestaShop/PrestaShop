<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Core\Domain\Configuration\Command\SwitchDebugModeCommand;

class DebugModeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I enable debug mode
     */
    public function enableDebugMode()
    {
        $this->getCommandBus()->handle(new SwitchDebugModeCommand(true));
    }

    /**
     * @When I disable debug mode
     */
    public function disableDebugMode()
    {
        $this->getCommandBus()->handle(new SwitchDebugModeCommand(false));
    }

    /**
     * @Then debug mode should be enabled in the configuration
     */
    public function debugModeShouldBeEnabledInTheConfiguration()
    {
        /** @var DebugMode $configuration */
        $configuration = $this->getContainer()->get('prestashop.adapter.debug_mode');
        $isDebugModeEnabled = $configuration->isDebugModeEnabled();
        PHPUnit_Framework_Assert::assertTrue($isDebugModeEnabled, 'Debug mode is not enabled');
    }

    /**
     * @Then debug mode should be disabled in the configuration
     */
    public function debugModeShouldBeDisabledInTheConfiguration()
    {
        /** @var DebugMode $configuration */
        $configuration = $this->getContainer()->get('prestashop.adapter.debug_mode');
        $isDebugModeEnabled = $configuration->isDebugModeEnabled();
        PHPUnit_Framework_Assert::assertFalse($isDebugModeEnabled, 'Debug mode is enabled');
    }
}
