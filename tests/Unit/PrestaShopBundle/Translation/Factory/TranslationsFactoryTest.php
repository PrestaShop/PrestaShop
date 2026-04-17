<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
