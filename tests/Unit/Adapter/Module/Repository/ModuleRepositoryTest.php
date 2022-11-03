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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Module\Repository;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->moduleRepository = new ModuleRepository(
            _PS_ROOT_DIR_ . '/tests/Unit/Resources/composerLock/',
            _PS_ROOT_DIR_ . '/tests/Resources/modules/'
        );
    }

    public function testNativeModulesMandatoryModules(): void
    {
        $modules = $this->moduleRepository->getNativeModules();
        foreach (ModuleRepository::ADDITIONAL_ALLOWED_MODULES as $mandatoryModule) {
            self::assertContains($mandatoryModule, $modules);
        }
    }

    /**
     * @dataProvider dataProviderNativeModules
     *
     * @param string $moduleName
     * @param bool $isNative
     */
    public function testNativeModulesCheckModules(string $moduleName, bool $isNative): void
    {
        if ($isNative) {
            self::assertContains($moduleName, $this->moduleRepository->getNativeModules());
        } else {
            self::assertNotContains($moduleName, $this->moduleRepository->getNativeModules());
        }
    }

    /**
     * @dataProvider dataProviderOnlyNativeModules
     *
     * @param string $moduleName
     */
    public function testNonNativeModulesCheckModules(string $moduleName): void
    {
        self::assertNotContains($moduleName, $this->moduleRepository->getNonNativeModules());
    }

    public function dataProviderNativeModules(): iterable
    {
        // Native modules
        yield ['blockwishlist', true];
        yield ['ps_banner', true];
        yield ['ps_wirepayment', true];
        // Non native modules
        yield ['ps_checkout', false];
        yield ['azerty', false];
        yield ['', false];
    }

    public function dataProviderOnlyNativeModules(): iterable
    {
        yield ['blockwishlist'];
        yield ['ps_banner'];
        yield ['ps_wirepayment'];
    }
}
