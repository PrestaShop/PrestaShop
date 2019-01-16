<?php
/**
 * 2007-2018 PrestaShop.
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

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutVariablesBuilder;
use Language;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutVariablesBuilderInterface;

class MailLayoutVariablesBuilderTest extends TestCase
{
    public function testConstructor()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builder = new MailLayoutVariablesBuilder($dispatcherMock);
        $this->assertNotNull($builder);

        $builder = new MailLayoutVariablesBuilder($dispatcherMock, ['locale' => 'en']);
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

        $builder = new MailLayoutVariablesBuilder($this->createHookDispatcherMock($expectedVariables, $layoutMock));
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
        $builder = new MailLayoutVariablesBuilder(
            $this->createHookDispatcherMock($expectedVariables, $layoutMock),
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
        $builder = new MailLayoutVariablesBuilder($this->createHookDispatcherMock($expectedVariables, $layoutMock));
        $builtVariables = $builder->buildVariables($layoutMock, $languageMock);

        $this->assertEquals($expectedVariables, $builtVariables);
    }

    /**
     * @param array $expectedVariables
     * @param MailLayoutInterface $mailLayout
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|HookDispatcherInterface
     */
    private function createHookDispatcherMock(array $expectedVariables, MailLayoutInterface $mailLayout)
    {
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dispatcherMock
            ->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo(MailLayoutVariablesBuilderInterface::BUILD_LAYOUT_VARIABLES_HOOK),
                $this->callback(function (array $hookParameters) use ($expectedVariables, $mailLayout) {
                    $this->assertEquals($expectedVariables, $hookParameters['mailLayoutVariables']);
                    $this->assertInstanceOf(MailLayoutInterface::class, $hookParameters['mailLayout']);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Language
     */
    private function buildLanguageMock($isoCode = 'en', $isRTL = false)
    {
        $languageMock = $this->getMockBuilder(Language::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $languageMock->iso_code = $isoCode;
        $languageMock->locale = sprintf('%s-%s', $isoCode, strtoupper($isoCode));
        $languageMock->is_rtl = $isRTL;

        return $languageMock;
    }

    /**
     * @param array $expectedMethods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailLayoutInterface
     */
    private function buildLayoutMock(array $expectedMethods)
    {
        $layoutMock = $this->getMockBuilder(MailLayoutInterface::class)
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
}
