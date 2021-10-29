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

namespace Tests\Unit\Adapter\News;

use ContextCore;
use PHPUnit\Framework\MockObject\MockObject;
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
        $this->assertIsArray($expectedJson);
        $this->assertArrayHasKey('has_errors', $expectedJson);
        $this->assertFalse($expectedJson['has_errors']);
        $this->assertArrayHasKey('rss', $expectedJson);
        $this->assertIsArray($expectedJson['rss']);
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
        $this->assertIsArray($expectedJson);
        $this->assertArrayHasKey('has_errors', $expectedJson);
        $this->assertTrue($expectedJson['has_errors']);
        $this->assertArrayHasKey('rss', $expectedJson);
        $this->assertIsArray($expectedJson['rss']);
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

        $this->assertIsArray($expectedJson);
        $this->assertArrayHasKey('has_errors', $expectedJson);
        $this->assertFalse($expectedJson['has_errors']);
        $this->assertArrayHasKey('rss', $expectedJson);
        $this->assertIsArray($expectedJson['rss']);
        $this->assertCount(NewsDataProvider::NUM_ARTICLES, $expectedJson['rss']);
        foreach ($expectedJson['rss'] as $expectedJsonRssItem) {
            $this->assertArrayHasKey('date', $expectedJsonRssItem);
            $this->assertStringMatchesFormat('%i-%i-%i', $expectedJsonRssItem['date']);
            $this->assertArrayHasKey('title', $expectedJsonRssItem);
            $this->assertNotEmpty($expectedJsonRssItem['title']);
            $this->assertArrayHasKey('short_desc', $expectedJsonRssItem);
            $this->assertLessThanOrEqual(150, mb_strlen($expectedJsonRssItem['short_desc']));
            $this->assertArrayHasKey('link', $expectedJsonRssItem);
            $this->assertNotFalse(filter_var($expectedJsonRssItem['link'], FILTER_VALIDATE_URL));
            $this->assertStringContainsString('utm_content=download', $expectedJsonRssItem['link']);
        }
    }

    /**
     * @param string|null $locale
     * @param string|null $returnData
     *
     * @return MockObject|CircuitBreakerInterface
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
     * @return MockObject|Configuration
     */
    private function createConfigurationMock()
    {
        return $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock()
            ;
    }

    /**
     * @return MockObject|CountryDataProvider
     */
    private function createCountryDataProviderMock()
    {
        return $this->getMockBuilder(CountryDataProvider::class)
            ->disableOriginalConstructor()
            ->getMock()
            ;
    }

    /**
     * @return MockObject|Tools
     */
    private function createToolsMock()
    {
        $toolsMock = $this->getMockBuilder(Tools::class)
            ->disableOriginalConstructor()
            ->getMock();

        $toolsMock
            ->method('displayDate')
            ->willReturn('2019-07-17');

        return $toolsMock;
    }

    /**
     * @param bool|null $isCleanHtml
     *
     * @return MockObject|Validate
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
