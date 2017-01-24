<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Translation\Factory;

use PrestaShopBundle\Translation\Factory\TranslationsFactory;
use Symfony\Component\Translation\MessageCatalogue;

class TranslationsFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $factory;
    private $providerMock;

    public function setUp()
    {
        $this->providerMock = $this->getMockBuilder('PrestaShopBundle\Translation\Provider\AbstractProvider')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->providerMock->method('getIdentifier')
            ->willReturn('mock')
        ;

        $this->providerMock->method('setLocale')
            ->will($this->returnSelf())
        ;

        $this->providerMock->method('getMessageCatalogue')
            ->willReturn(new MessageCatalogue('en-US'))
        ;

        $this->providerMock->method('getDatabaseCatalogue')
            ->willReturn(new MessageCatalogue('en-US'))
        ;

        $this->factory = new TranslationsFactory();
    }

    public function testCreateCatalogueWithoutProviderFails()
    {
        $this->setExpectedException('PrestaShopBundle\Translation\Factory\ProviderNotFoundException');
        $expected = $this->factory
            ->createCatalogue($this->providerMock->getIdentifier())
        ;
    }

    public function testCreateCatalogueWithProvider()
    {
        $this->factory->addProvider($this->providerMock);

        $expected = $this->factory
            ->createCatalogue($this->providerMock->getIdentifier())
        ;

        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogue', $expected);
    }

    public function testCreateTranslationsArrayWithoutProviderFails()
    {
        $this->setExpectedException('PrestaShopBundle\Translation\Factory\ProviderNotFoundException');
        $expected = $this->factory
            ->createTranslationsArray($this->providerMock->getIdentifier())
        ;
    }

    public function testCreateTranslationsArrayWithProvider()
    {
        $this->providerMock->method('getXliffCatalogue')
            ->willReturn(new MessageCatalogue('en-US'))
        ;

        $this->factory->addProvider($this->providerMock);

        $expected = $this->factory
            ->createTranslationsArray($this->providerMock->getIdentifier())
        ;
    }
}
