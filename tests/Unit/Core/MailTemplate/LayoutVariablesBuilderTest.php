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

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageDefaultFontsCatalog;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutVariablesBuilder;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutVariablesBuilderInterface;

class LayoutVariablesBuilderTest extends TestCase
{
    public function testConstructor()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        /** @var LanguageDefaultFontsCatalog $fontCatalog */
        $fontCatalog = $this->getMockBuilder(LanguageDefaultFontsCatalog::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builder = new LayoutVariablesBuilder($dispatcherMock, $fontCatalog);
        $this->assertNotNull($builder);

        $builder = new LayoutVariablesBuilder($dispatcherMock, $fontCatalog, ['locale' => 'en']);
        $this->assertNotNull($builder);
    }

    public function testBuildParameters()
    {
        $layoutInfos = [
            'getName' => 'user_account',
            'getModuleName' => null,
        ];
        $layoutMock = $this->buildLayoutMock($layoutInfos);
        $languageMock = $this->buildLanguageMock();

        $expectedVariables = [
            'templateName' => 'user_account',
            'templateModuleName' => '',
            'languageIsRTL' => false,
            'languageDefaultFont' => '',
            'locale' => 'en-EN',
        ];

        $builder = new LayoutVariablesBuilder($this->createHookDispatcherMock($expectedVariables, $layoutMock), $this->buildFontCatalog());
        $builtVariables = $builder->buildVariables($layoutMock, $languageMock);

        $this->assertEquals($expectedVariables, $builtVariables);
    }

    public function testBuildParametersWithDefault()
    {
        $layoutInfos = [
            'getName' => 'user_account',
            'getModuleName' => null,
        ];
        $layoutMock = $this->buildLayoutMock($layoutInfos);
        $languageMock = $this->buildLanguageMock();

        $expectedVariables = [
            'url' => 'http://test.com',
            'templateName' => 'user_account',
            'templateModuleName' => '',
            'languageIsRTL' => false,
            'languageDefaultFont' => '',
            'locale' => 'en-EN',
        ];
        $builder = new LayoutVariablesBuilder(
            $this->createHookDispatcherMock($expectedVariables, $layoutMock),
            $this->buildFontCatalog(),
            [
                'url' => 'http://test.com',
                'languageDefaultFont' => 'overriddenFont',
            ]
        );
        $builtVariables = $builder->buildVariables($layoutMock, $languageMock);

        $this->assertEquals($expectedVariables, $builtVariables);
    }

    public function testBuildParametersWithRTL()
    {
        $layoutInfos = [
            'getName' => 'user_account',
            'getModuleName' => 'ps_reminder',
        ];
        $layoutMock = $this->buildLayoutMock($layoutInfos);
        $languageMock = $this->buildLanguageMock('ar', true);

        $expectedVariables = [
            'templateName' => 'user_account',
            'templateModuleName' => 'ps_reminder',
            'languageIsRTL' => true,
            'languageDefaultFont' => 'Tahoma,',
            'locale' => 'ar-AR',
        ];
        $builder = new LayoutVariablesBuilder(
            $this->createHookDispatcherMock($expectedVariables, $layoutMock),
            $this->buildFontCatalog()
        );
        $builtVariables = $builder->buildVariables($layoutMock, $languageMock);

        $this->assertEquals($expectedVariables, $builtVariables);
    }

    /**
     * @param array $expectedVariables
     * @param LayoutInterface $mailLayout
     *
     * @return MockObject|HookDispatcherInterface
     */
    private function createHookDispatcherMock(array $expectedVariables, LayoutInterface $mailLayout)
    {
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dispatcherMock
            ->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo(LayoutVariablesBuilderInterface::BUILD_MAIL_LAYOUT_VARIABLES_HOOK),
                $this->callback(function (array $hookParameters) use ($expectedVariables, $mailLayout) {
                    $this->assertEquals($expectedVariables, $hookParameters['mailLayoutVariables']);
                    $this->assertInstanceOf(LayoutInterface::class, $hookParameters['mailLayout']);
                    $this->assertEquals($mailLayout, $hookParameters['mailLayout']);

                    return true;
                })
            )
        ;

        return $dispatcherMock;
    }

    /**
     * @param string $isoCode
     * @param bool $isRTL
     *
     * @return MockObject|LanguageInterface
     */
    private function buildLanguageMock($isoCode = 'en', $isRTL = false)
    {
        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $languageMock
            ->expects($this->atLeastOnce())
            ->method('getIsoCode')
            ->willReturn($isoCode)
        ;
        $languageMock
            ->expects($this->once())
            ->method('getLocale')
            ->willReturn(sprintf('%s-%s', $isoCode, strtoupper($isoCode)))
        ;
        $languageMock
            ->expects($this->once())
            ->method('isRTL')
            ->willReturn($isRTL)
        ;

        return $languageMock;
    }

    /**
     * @param array $expectedMethods
     *
     * @return MockObject|LayoutInterface
     */
    private function buildLayoutMock(array $expectedMethods)
    {
        $layoutMock = $this->getMockBuilder(LayoutInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        foreach ($expectedMethods as $methodName => $returnValue) {
            $layoutMock
                ->expects($this->once())
                ->method($methodName)
                ->willReturn($returnValue)
            ;
        }

        return $layoutMock;
    }

    /**
     * @return MockObject|LanguageDefaultFontsCatalog
     */
    private function buildFontCatalog()
    {
        $fontCatalog = $this->getMockBuilder(LanguageDefaultFontsCatalog::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $fontCatalog
            ->expects($this->once())
            ->method('getDefaultFontByLanguage')
            ->with(
                $this->isInstanceOf(LanguageInterface::class)
            )
            ->willReturnCallback(function (LanguageInterface $language) {
                if (in_array($language->getIsoCode(), ['ar', 'fa'])) {
                    return 'Tahoma';
                }

                return '';
            })
        ;

        return $fontCatalog;
    }
}
