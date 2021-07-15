<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Core\Module;

use Context;
use Db;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Core\Module\HookRepository;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HookRepositoryTest extends KernelTestCase
{
    /**
     * @var HookRepository
     */
    private $hookRepository;

    /**
     * @var Context
     */
    protected $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = Context::getContext();
        Context::setInstanceForTesting($this->getMockContext());

        $this->hookRepository = new HookRepository(
            new HookInformationProvider(),
            Context::getContext()->shop,
            Db::getInstance(),
            _DB_PREFIX_
        );
    }

    protected function getMockContext(): Context
    {
        $mockContext = $this->getMockBuilder(Context::class)->getMock();
        $mockContext->shop = $this->getMockBuilder(Shop::class)->getMock();

        return $mockContext;
    }

    public function testPersistAndRetrieve(): void
    {
        $modules = [
            'ps_emailsubscription',
            'ps_featuredproducts',
        ];

        $this->hookRepository->persistHooksConfiguration([
            'displayTestHookName' => $modules,
        ]);

        $this->assertEquals(
            $modules,
            $this->hookRepository->getHooksWithModules()['displayTestHookName']
        );
    }

    public function testOnlyDisplayHooksAreRetrieved(): void
    {
        $this->hookRepository->persistHooksConfiguration([
            'displayTestHookName' => ['ps_emailsubscription', 'ps_featuredproducts'],
            'notADisplayTestHookName' => ['ps_languageselector', 'ps_currencyselector'],
        ]);

        $actual = $this->hookRepository->getDisplayHooksWithModules();

        $this->assertEquals(
            ['ps_emailsubscription', 'ps_featuredproducts'],
            $actual['displayTestHookName']
        );

        $this->assertArrayNotHasKey(
            'notADisplayTestHookName',
            $actual
        );
    }

    public function testExceptionsTakenIntoAccount(): void
    {
        $this->hookRepository->persistHooksConfiguration([
            'displayTestHookNameWithExceptions' => [
                [
                    'ps_emailsubscription' => [
                        'except_pages' => ['category', 'product'],
                    ],
                ],
            ],
        ]);

        $this->assertEquals(
            [
                'ps_emailsubscription' => [
                    'except_pages' => ['category', 'product'],
                ],
            ],
            $this->hookRepository->getHooksWithModules()['displayTestHookNameWithExceptions']
        );
    }
}
