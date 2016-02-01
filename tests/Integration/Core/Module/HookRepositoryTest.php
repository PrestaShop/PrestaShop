<?php

namespace PrestaShop\PrestaShop\tests\Integration\Core\Module;

use Context;
use Db;
use PrestaShop\PrestaShop\Core\Module\HookRepository;
use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;

class HookRepositoryTest extends IntegrationTestCase
{
    private $hookRepository;

    public function setup()
    {
        $this->hookRepository = new HookRepository(
            new HookInformationProvider,
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

    public function test_only_display_hooks_are_retrieved()
    {
        $this->hookRepository->persistHookConfiguration([
            'displayTestHookName' => ['blocknewsletter', 'blockcart'],
            'notADisplayTestHookName' => ['blocklanguage', 'blockcurrencies']
        ]);

        $actual = $this->hookRepository->getDisplayHooksWithModules();

        $this->assertEquals(
            ['blocknewsletter', 'blockcart'],
            $actual['displayTestHookName']
        );

        $this->assertFalse(
            array_key_exists('notADisplayTestHookName', $actual)
        );
    }
}
