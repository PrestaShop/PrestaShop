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

namespace Tests\Unit\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\BackofficeCatalogueProvider;
use PrestaShopBundle\Translation\Provider\CatalogueProviderFactory;
use PrestaShopBundle\Translation\TranslationCatalogueBuilder;

class CatalogueProviderFactoryTest extends TestCase
{
    /**
     * @var CatalogueProviderFactory
     */
    private $factory;

    public function setUp()
    {
        $databaseTranslationLoader = $this->createMock(DatabaseTranslationLoader::class);

        $this->factory = new CatalogueProviderFactory($databaseTranslationLoader, 'resourceDirectory');
    }

    public function testGetProviderWrongType()
    {
        $this->expectException(UnexpectedTranslationTypeException::class);
        $this->factory->getProvider('wrongType');
    }

    public function testGetProvider()
    {
        $provider = $this->factory->getProvider(TranslationCatalogueBuilder::TYPE_BACK);
        $this->assertInstanceOf(BackofficeCatalogueProvider::class, $provider);
    }
}
