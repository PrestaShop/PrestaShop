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

namespace Tests\Unit\Core\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Builder\TranslationCatalogueBuilder;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShop\PrestaShop\Core\Translation\Provider\BackofficeCatalogueLayersProvider;
use PrestaShop\PrestaShop\Core\Translation\Provider\CatalogueProviderFactory;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;

class CatalogueProviderFactoryTest extends TestCase
{
    /**
     * @var CatalogueProviderFactory
     */
    private $factory;

    protected function setUp()
    {
        $databaseTranslationLoader = $this->createMock(DatabaseTranslationLoader::class);

        $this->factory = new CatalogueProviderFactory($databaseTranslationLoader, 'resourceDirectory');
    }

    public function testGetProviderFailsWhenWrongTypeIsGiven()
    {
        $this->expectException(UnexpectedTranslationTypeException::class);
        $this->factory->getProvider('wrongType');
    }

    public function testGetProvider()
    {
        $providersByType = [
            TranslationCatalogueBuilder::TYPE_BACK => BackofficeCatalogueLayersProvider::class,
        ];

        foreach ($providersByType as $providerType => $providerClass) {
            $provider = $this->factory->getProvider($providerType);
            $this->assertInstanceOf($providerClass, $provider);
        }
    }
}
