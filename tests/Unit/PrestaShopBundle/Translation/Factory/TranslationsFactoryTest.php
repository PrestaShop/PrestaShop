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

namespace Tests\Unit\PrestaShopBundle\Translation\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Factory\TranslationsFactory;
use PrestaShopBundle\Translation\Provider\AbstractProvider;
use Symfony\Component\Translation\MessageCatalogue;

class TranslationsFactoryTest extends TestCase
{
    /**
     * @var TranslationsFactory
     */
    private $factory;
    /**
     * @var AbstractProvider|MockObject
     */
    private $providerMock;

    protected function setUp(): void
    {
        $this->providerMock = $this->getMockBuilder(AbstractProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->providerMock->method('getIdentifier')
            ->willReturn('mock');

        $this->providerMock->method('setLocale')
            ->will($this->returnSelf());

        $this->providerMock->method('getMessageCatalogue')
            ->willReturn(new MessageCatalogue('en-US'));

        $this->providerMock->method('getDefaultCatalogue')
            ->willReturn(new MessageCatalogue('en-US'));

        $this->providerMock->method('getDatabaseCatalogue')
            ->willReturn(new MessageCatalogue('en-US'));

        $this->factory = new TranslationsFactory();
    }

    public function testCreateCatalogueWithoutProviderFails(): void
    {
        $this->expectException('PrestaShopBundle\Translation\Factory\ProviderNotFoundException');
        $this->factory->createCatalogue($this->providerMock->getIdentifier());
    }

    public function testCreateCatalogueWithProvider(): void
    {
        $this->factory->addProvider($this->providerMock);

        $expected = $this->factory->createCatalogue($this->providerMock->getIdentifier());

        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogue', $expected);
    }

    public function testCreateTranslationsArrayWithoutProviderFails(): void
    {
        $this->expectException('PrestaShopBundle\Translation\Factory\ProviderNotFoundException');
        $this->factory->createTranslationsArray($this->providerMock->getIdentifier());
    }

    public function testCreateTranslationsArrayWithProvider(): void
    {
        $this->providerMock->method('getXliffCatalogue')
            ->willReturn(new MessageCatalogue('en-US'));

        $this->factory->addProvider($this->providerMock);

        $expected = $this->factory->createTranslationsArray($this->providerMock->getIdentifier());
        $this->assertIsArray($expected);
    }
}
