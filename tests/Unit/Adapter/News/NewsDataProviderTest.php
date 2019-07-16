<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Adapter\News;

use ContextCore;
use PHPUnit\Framework\TestCase;
use PrestaShop\CircuitBreaker\Contract\CircuitBreakerInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\News\NewsDataProvider;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Adapter\Validate;

class NewsDataProviderTest extends TestCase
{
    public function testConstructor()
    {
        $generator = new NewsDataProvider(
            $this->createCircuitBreakerMock(),
            $this->createCountryDataProviderMock(),
            $this->createToolsMock(),
            $this->createConfigurationMock(),
            $this->createValidateMock(),
            ContextCore::MODE_STD
        );
        $this->assertNotNull($generator);
    }

    public function testDataFallback()
    {
        $expectedLocale = 'fr';

        $generator = new NewsDataProvider(
            $this->createCircuitBreakerMock($expectedLocale, ''),
            $this->createCountryDataProviderMock(),
            $this->createToolsMock(),
            $this->createConfigurationMock(),
            $this->createValidateMock(),
            ContextCore::MODE_STD
        );
        $expectedJson = $generator->getData($expectedLocale);
        $this->assertInternalType('array', $expectedJson);
        $this->assertArrayHasKey('has_errors', $expectedJson);
        $this->assertTrue($expectedJson['has_errors']);
        $this->assertArrayHasKey('rss', $expectedJson);
        $this->assertInternalType('array', $expectedJson['rss']);
        $this->assertEmpty($expectedJson['rss']);
    }

    public function testDataRssInvalid()
    {
        $expectedLocale = 'fr';

        $generator = new NewsDataProvider(
            $this->createCircuitBreakerMock($expectedLocale, 'INVALID DATA'),
            $this->createCountryDataProviderMock(),
            $this->createToolsMock(),
            $this->createConfigurationMock(),
            $this->createValidateMock(),
            ContextCore::MODE_STD
        );
        $expectedJson = $generator->getData($expectedLocale);
        $this->assertInternalType('array', $expectedJson);
        $this->assertArrayHasKey('has_errors', $expectedJson);
        $this->assertTrue($expectedJson['has_errors']);
        $this->assertArrayHasKey('rss', $expectedJson);
        $this->assertInternalType('array', $expectedJson['rss']);
        $this->assertEmpty($expectedJson['rss']);
    }

    public function testData()
    {
        $expectedLocale = 'fr';

        $generator = new NewsDataProvider(
            $this->createCircuitBreakerMock($expectedLocale, file_get_contents(__DIR__ . '/../../Resources/rss/blog-fr.xml')),
            $this->createCountryDataProviderMock(),
            $this->createToolsMock(),
            $this->createConfigurationMock(),
            $this->createValidateMock(true),
            ContextCore::MODE_STD
        );
        $expectedJson = $generator->getData($expectedLocale);

        $this->assertInternalType('array', $expectedJson);
        $this->assertArrayHasKey('has_errors', $expectedJson);
        $this->assertFalse($expectedJson['has_errors']);
        $this->assertArrayHasKey('rss', $expectedJson);
        $this->assertInternalType('array', $expectedJson['rss']);
        $this->assertCount(NewsDataProvider::NUM_ARTICLES, $expectedJson['rss']);
    }

    /**
     * @param string|null $locale
     * @param string|null $returnData
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|CircuitBreakerInterface
     */
    private function createCircuitBreakerMock($locale = null, $returnData = null)
    {
        $circuitBreakerMock = $this->getMockBuilder(CircuitBreakerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
            ;

        if (!is_null($locale) && !is_null($returnData)) {
            $circuitBreakerMock
                ->expects($this->once())
                ->method('call')
                ->with(
                    $this->stringContains($locale)
                )
                ->willReturn($returnData)
            ;
        }

        return $circuitBreakerMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Configuration
     */
    private function createConfigurationMock()
    {
        return $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock()
            ;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CountryDataProvider
     */
    private function createCountryDataProviderMock()
    {
        return $this->getMockBuilder(CountryDataProvider::class)
            ->disableOriginalConstructor()
            ->getMock()
            ;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Tools
     */
    private function createToolsMock()
    {
        return $this->getMockBuilder(Tools::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param bool|null $isCleanHtml
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Validate
     */
    private function createValidateMock($isCleanHtml = null)
    {
        $validateMock = $this->getMockBuilder(Validate::class)
            ->disableOriginalConstructor()
            ->getMock();

        if (!is_null($isCleanHtml)) {
            $validateMock
                ->method('isCleanHtml')
                ->willReturn($isCleanHtml)
            ;
        }

        return $validateMock;
    }
}
