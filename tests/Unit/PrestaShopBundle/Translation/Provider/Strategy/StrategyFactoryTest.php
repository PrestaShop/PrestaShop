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

namespace Tests\Unit\PrestaShopBundle\Translation\Provider\Strategy;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use PrestaShopBundle\Translation\Provider\Type\BackStrategy;
use PrestaShopBundle\Translation\Provider\Type\ExternalLegacyModuleStrategy;
use PrestaShopBundle\Translation\Provider\Type\FrontStrategy;
use PrestaShopBundle\Translation\Provider\Type\MailsBodyStrategy;
use PrestaShopBundle\Translation\Provider\Type\MailsStrategy;
use PrestaShopBundle\Translation\Provider\Type\ModulesStrategy;
use PrestaShopBundle\Translation\Provider\Type\OthersStrategy;
use PrestaShopBundle\Translation\Provider\Type\SearchStrategy;
use PrestaShopBundle\Translation\Provider\Type\StrategyFactory;
use PrestaShopBundle\Translation\Provider\Type\ThemesStrategy;
use PrestaShopBundle\Translation\Provider\ThemeProvider;

class StrategyFactoryTest extends TestCase
{
    /**
     * @var StrategyFactory
     */
    private $strategyFactory;

    public function setUp()
    {
        /** @var MockObject|DatabaseTranslationLoader $databaseLoader */
        $databaseLoader = $this->createMock(DatabaseTranslationLoader::class);
        /** @var @var MockObject|ThemeProvider $themeProvider */
        $themeProvider = $this->createMock(ThemeProvider::class);
        /** @var @var MockObject|SearchProvider $searchProvider */
        $searchProvider = $this->createMock(SearchProvider::class);
        /** @var @var MockObject|ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider */
        $externalModuleLegacySystemProvider = $this->createMock(ExternalModuleLegacySystemProvider::class);

        $this->strategyFactory = new StrategyFactory(
            $databaseLoader,
            '',
            $themeProvider,
            $searchProvider,
            $externalModuleLegacySystemProvider
        );
    }

    public function testBuildExternalLegacyModuleStrategy()
    {
        $strategy = $this->strategyFactory->buildExternalLegacyModuleStrategy('fr-FR', 'module-name');

        $this->assertInstanceOf(ExternalLegacyModuleStrategy::class, $strategy);
    }

    public function testBuildThemesStrategy()
    {
        $strategy = $this->strategyFactory->buildThemesStrategy('fr-FR', 'theme-name');

        $this->assertInstanceOf(ThemesStrategy::class, $strategy);
    }

    public function testBuildSearchStrategy()
    {
        $strategy = $this->strategyFactory->buildSearchStrategy(
            'fr-FR',
            'domain-name',
            'theme-name',
            'module-name'
        );

        $this->assertInstanceOf(SearchStrategy::class, $strategy);
    }

    public function testBuildFrontStrategy()
    {
        $strategy = $this->strategyFactory->buildFrontStrategy('fr-FR');

        $this->assertInstanceOf(FrontStrategy::class, $strategy);
    }

    public function testBuildModulesStrategy()
    {
        $strategy = $this->strategyFactory->buildModulesStrategy('fr-FR');

        $this->assertInstanceOf(ModulesStrategy::class, $strategy);
    }

    public function testBuildMailsStrategy()
    {
        $strategy = $this->strategyFactory->buildMailsStrategy('fr-FR');

        $this->assertInstanceOf(MailsStrategy::class, $strategy);
    }

    public function testBuildMailsBodyStrategy()
    {
        $strategy = $this->strategyFactory->buildMailsBodyStrategy('fr-FR');

        $this->assertInstanceOf(MailsBodyStrategy::class, $strategy);
    }

    public function testBuildBackStrategy()
    {
        $strategy = $this->strategyFactory->buildBackStrategy('fr-FR');

        $this->assertInstanceOf(BackStrategy::class, $strategy);
    }

    public function testBuildOthersStrategy()
    {
        $strategy = $this->strategyFactory->buildOthersStrategy('fr-FR');

        $this->assertInstanceOf(OthersStrategy::class, $strategy);
    }
}
