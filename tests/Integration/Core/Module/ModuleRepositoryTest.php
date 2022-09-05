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

declare(strict_types=1);

namespace Tests\Integration\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Translation\Translator;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    protected function setUp(): void
    {
        $moduleDataProvider = $this->createMock(ModuleDataProvider::class);
        $moduleDataProvider->method('findByName')->willReturn([
            'installed' => 0,
            'active' => true,
        ]);

        $translator = $this->createMock(Translator::class);
        $translator->method('trans')->willReturnArgument(0);

        $this->moduleRepository = new ModuleRepository(
            $moduleDataProvider,
            $this->createMock(AdminModuleDataProvider::class),
            new DoctrineProvider(new ArrayAdapter()),
            $this->createMock(HookManager::class),
            dirname(__DIR__, 3) . '/Resources/modules/'
        );
    }

    public function testGetAtLeastOneModuleFromUniverse(): void
    {
        $this->assertGreaterThan(0, count($this->moduleRepository->getList()));
    }
}
