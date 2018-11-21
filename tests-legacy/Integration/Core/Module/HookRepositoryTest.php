<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace Tests\Integration\Core\Module;

use Context;
use Db;
use PrestaShop\PrestaShop\Core\Module\HookRepository;
use Tests\TestCase\IntegrationTestCase;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use Tests\Unit\ContextMocker;

class HookRepositoryTest extends IntegrationTestCase
{
    private $hookRepository;

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    protected function setUp()
    {
        parent::setUp();
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();
        $this->hookRepository = new HookRepository(
            new HookInformationProvider,
            Context::getContext()->shop,
            Db::getInstance(),
            _DB_PREFIX_
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->contextMocker->resetContext();
    }

    public function test_persist_and_retrieve()
    {
        $modules = [
            'ps_emailsubscription',
            'ps_featuredproducts'
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
            'displayTestHookName' => ['ps_emailsubscription', 'ps_featuredproducts'],
            'notADisplayTestHookName' => ['ps_languageselector', 'ps_currencyselector']
        ]);

        $actual = $this->hookRepository->getDisplayHooksWithModules();

        $this->assertEquals(
            ['ps_emailsubscription', 'ps_featuredproducts'],
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
                [
                    'ps_emailsubscription' => [
                        'except_pages' => ['category', 'product']
                    ]
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
