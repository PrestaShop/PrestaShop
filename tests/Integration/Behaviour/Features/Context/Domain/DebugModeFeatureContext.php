<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit_Framework_AssertionFailedError;
use PrestaShop\PrestaShop\Adapter\Debug\DebugMode;
use PrestaShop\PrestaShop\Core\Domain\Configuration\Command\SwitchDebugModeCommand;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class DebugModeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I enable debug mode
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iEnableDebugMode()
    {
        $this->getCommandBus()->handle(new SwitchDebugModeCommand(true));
    }

    /**
     * @When I disable debug mode
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iDisableDebugMode()
    {
        $this->getCommandBus()->handle(new SwitchDebugModeCommand(false));
    }

    /**
     * @Then debug mode should be enabled in the configuration
     *
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function debugModeShouldBeEnabledInTheConfiguration()
    {
        /** @var DebugMode $configuration */
        $configuration = $this->getContainer()->get('prestashop.adapter.debug_mode');
        $isDebugModeEnabled = $configuration->isDebugModeEnabled();
        assertTrue($isDebugModeEnabled, 'Debug mode is not enabled');
    }

    /**
     * @Then debug mode should be disabled in the configuration
     *
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function debugModeShouldBeDisabledInTheConfiguration()
    {
        /** @var DebugMode $configuration */
        $configuration = $this->getContainer()->get('prestashop.adapter.debug_mode');
        $isDebugModeEnabled = $configuration->isDebugModeEnabled();
        assertFalse($isDebugModeEnabled, 'Debug mode is enabled');
    }
}
