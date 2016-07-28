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
            'ps_emailsubscription',
            'ps_shoppingcart'
        ];

        $this->hookRepository->persistHooksConfiguration([
            'displayTestHookName' => $modules
        ]);

        $this->assertEquals(
            $modules,
            $this->hookRepository->getHooksWithModules()['displayTestHookName']
        );
    }

    public function test_only_display_hooks_are_retrieved()
    {
        $this->hookRepository->persistHooksConfiguration([
            'displayTestHookName' => ['ps_emailsubscription', 'ps_shoppingcart'],
            'notADisplayTestHookName' => ['ps_languageselector', 'ps_currencyselector']
        ]);

        $actual = $this->hookRepository->getDisplayHooksWithModules();

        $this->assertEquals(
            ['ps_emailsubscription', 'ps_shoppingcart'],
            $actual['displayTestHookName']
        );

        $this->assertFalse(
            array_key_exists('notADisplayTestHookName', $actual)
        );
    }

    public function test_exceptions_taken_into_account()
    {
        $this->hookRepository->persistHooksConfiguration([
            'displayTestHookNameWithExceptions' => [
                'ps_emailsubscription' => [
                    'except_pages' => ['category', 'product']
                ]
            ]
        ]);

        $this->assertEquals(
            [
                'ps_emailsubscription' => [
                    'except_pages' => ['category', 'product']
                ]
            ],
            $this->hookRepository->getHooksWithModules()['displayTestHookNameWithExceptions']
        );
    }
}
