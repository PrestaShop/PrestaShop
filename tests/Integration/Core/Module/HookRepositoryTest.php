<?php

namespace PrestaShop\PrestaShop\tests\Integration\Core\Module;

use Context;
use Db;
use PrestaShop\PrestaShop\Core\Module\HookRepository;
use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

class HookRepositoryTest extends IntegrationTestCase
{
    private $hookRepository;

    public function setup()
    {
        $this->hookRepository = new HookRepository(
            Context::getContext()->shop,
            Db::getInstance(),
            _DB_PREFIX_
        );
    }

    public function test_persist_and_retrieve()
    {
        $modules = [
            'blocknewsletter',
            'blockcart'
        ];

        $this->hookRepository->persistHookConfiguration([
            'displayTestHookName' => $modules
        ]);

        $this->assertEquals(
            $modules,
            $this->hookRepository->getHooksWithModules()['displayTestHookName']
        );
    }
}
