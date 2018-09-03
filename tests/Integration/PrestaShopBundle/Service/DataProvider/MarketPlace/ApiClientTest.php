<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Service\DataProvider\MarketPlace;

use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Phake;

/**
 * @group addons
 */
class ApiClientTest extends KernelTestCase
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $kernel->getContainer()->set('prestashop.adapter.legacy.context', $this->mockLegacyContext());

        $this->apiClient = $kernel->getContainer()->get('prestashop.addons.client_api');
        $this->apiClient->setClient($this->mockClient());
    }

    protected function tearDown()
    {
        $this->apiClient = null;
    }

    public function testGetNativeModules()
    {
        $this->assertCount(0, $this->apiClient->getNativesModules());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockResponse()
    {
        $responseMock = $this->getMockBuilder('\GuzzleHttp\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getBody'])
            ->getMock();

        $responseMock->method('getBody')
            ->willReturn(json_encode((object)['modules' => []]));

        return $responseMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockClient()
    {
        $responseMock = $this->mockResponse();

        $clientMock = $this->getMockBuilder('\GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $clientMock->method('get')
            ->with($this->anything())
            ->willReturn($responseMock);

        return $clientMock;
    }

    /**
     * @return \PrestaShop\PrestaShop\Adapter\LegacyContext
     */
    protected function mockLegacyContext()
    {
        $context = Phake::mock('Context');
        $context->language = Phake::mock('Language');

        $legacyContext = Phake::mock('\PrestaShop\PrestaShop\Adapter\LegacyContext');
        Phake::when($legacyContext)->getContext()->thenReturn($context);

        return $legacyContext;
    }
}
