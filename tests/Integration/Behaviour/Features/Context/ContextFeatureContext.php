<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use LegacyTests\Unit\ContextMocker;

class ContextFeatureContext implements BehatContext
{

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    /**
     * This hook can be used to properly mock context
     *
     * @BeforeScenario
     */
    public function mockContext()
    {
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
            throw new \Exception('Context was not mocked');
        }
        $this->contextMocker->resetContext();
    }
}
