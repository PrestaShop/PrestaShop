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

namespace Tests\Integration\PrestaShopBundle\Translation\Provider\Factory;

use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShopBundle\Translation\Provider\CoreProvider;
use PrestaShopBundle\Translation\Provider\Factory\ProviderFactory;
use PrestaShopBundle\Translation\Provider\ModulesProvider;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use PrestaShopBundle\Translation\Provider\Type\BackType;
use PrestaShopBundle\Translation\Provider\Type\CoreDomainType;
use PrestaShopBundle\Translation\Provider\Type\CoreFrontType;
use PrestaShopBundle\Translation\Provider\Type\MailsBodyType;
use PrestaShopBundle\Translation\Provider\Type\MailsType;
use PrestaShopBundle\Translation\Provider\Type\ModulesType;
use PrestaShopBundle\Translation\Provider\Type\OthersType;
use PrestaShopBundle\Translation\Provider\Type\ThemesType;
use PrestaShopBundle\Translation\Provider\Type\TypeInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProviderFactoryTest extends KernelTestCase
{
    /**
     * @var ProviderFactory
     */
    private $providerFactory;

    public function setUp()
    {
        self::bootKernel();
        $this->providerFactory = self::$kernel->getContainer()->get('prestashop.translation.provider_factory');
    }

    public function testThrowsExceptionIfTypeIsWrong()
    {
        $this->expectException(NotImplementedException::class);
        $provider = $this->providerFactory->build($this->createMock(TypeInterface::class));
    }

    public function testBuildBackProvider()
    {
        $provider = $this->providerFactory->build(new BackType());

        $this->assertInstanceOf(CoreProvider::class, $provider);
    }

    public function testBuildCoreFrontProvider()
    {
        $provider = $this->providerFactory->build(new CoreFrontType());

        $this->assertInstanceOf(CoreProvider::class, $provider);
    }

    public function testBuildMailsBodyProvider()
    {
        $provider = $this->providerFactory->build(new MailsBodyType());

        $this->assertInstanceOf(CoreProvider::class, $provider);
    }

    public function testBuildMailsProvider()
    {
        $provider = $this->providerFactory->build(new MailsType());

        $this->assertInstanceOf(CoreProvider::class, $provider);
    }

    public function testBuildModulesProvider()
    {
        $provider = $this->providerFactory->build(new ModulesType('modulename'));

        $this->assertInstanceOf(ModulesProvider::class, $provider);
    }

    public function testBuildOthersProvider()
    {
        $provider = $this->providerFactory->build(new OthersType());

        $this->assertInstanceOf(CoreProvider::class, $provider);
    }

    public function testBuildCoreDomainProvider()
    {
        $provider = $this->providerFactory->build(new CoreDomainType('domainname'));

        $this->assertInstanceOf(CoreProvider::class, $provider);
    }

    public function testBuildThemeProvider()
    {
        $provider = $this->providerFactory->build(new ThemesType('fakeThemeForTranslations'));

        $this->assertInstanceOf(ThemeProvider::class, $provider);
    }
}
