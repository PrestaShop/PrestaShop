<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Tests\Integration\Utility\ContextMocker;

class ContextFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var ContextMocker|null
     */
    protected $contextMocker;

    /**
     * This hook can be used to properly mock context
     *
     * @BeforeScenario
     */
    public function mockContext()
    {
        /** @var LegacyContext $legacyContext */
        $legacyContext = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.context');
        /*
         * We need to call this before initializing the ContextMocker because this method forcefully init
         * the shop context thus overriding the expected value
         */
        $legacyContext->getContext();

        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();
    }

    /**
     * This hook can be used to properly reset previous context
     *
     * @AfterScenario
     */
    public function resetContext()
    {
        if (empty($this->contextMocker)) {
            throw new Exception('Context was not mocked');
        }
        $this->contextMocker->resetContext();
    }
}
