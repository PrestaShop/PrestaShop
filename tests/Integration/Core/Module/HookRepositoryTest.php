<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Core\Module;

use Db;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Core\Module\HookRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;

class HookRepositoryTest extends KernelTestCase
{
    use ContextMockerTrait;

    /**
     * @var HookRepository
     */
    private $hookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hookRepository = new HookRepository(
            new HookInformationProvider(),
            self::getContext()->shop,
            Db::getInstance()
        );
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
