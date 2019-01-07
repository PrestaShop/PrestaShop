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

namespace Tests\Unit\PrestaShopBundle\Service\Mail;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Mail\MailTemplateInterface;
use PrestaShopBundle\Service\Mail\MailTemplateParametersBuilder;
use Language;

class MailTemplateParametersBuilderTest extends TestCase
{
    public function testConstructor()
    {
        $builder = new MailTemplateParametersBuilder();
        $this->assertNotNull($builder);

        $builder = new MailTemplateParametersBuilder(['locale' => 'en']);
        $this->assertNotNull($builder);
    }

    public function testBuildParameters()
    {
        $templateInfos = [
            'getType' => MailTemplateInterface::HTML_TYPE,
            'getTheme' => 'classic',
            'getName' => 'user_account',
            'getModule' => null,
        ];
        $templateMock = $this->buildTemplateMock($templateInfos);
        $languageMock = $this->buildLanguageMock();

        $builder = new MailTemplateParametersBuilder();
        $parameters = $builder->buildParameters($templateMock, $languageMock);

        $this->assertEquals([
            'templateType' => MailTemplateInterface::HTML_TYPE,
            'templateName' => 'user_account',
            'templateTheme' => 'classic',
            'templateModule' => null,
            'languageIsRTL' => false,
            'languageDefaultFont' => '',
            'locale' => 'en-EN',
        ], $parameters);
    }

    public function testBuildParametersWithDefault()
    {
        $templateInfos = [
            'getType' => MailTemplateInterface::HTML_TYPE,
            'getTheme' => 'classic',
            'getName' => 'user_account',
            'getModule' => null,
        ];
        $templateMock = $this->buildTemplateMock($templateInfos);
        $languageMock = $this->buildLanguageMock();

        $builder = new MailTemplateParametersBuilder([
            'url' => 'http://test.com',
            'languageDefaultFont' => 'overriddenFont'
        ]);
        $parameters = $builder->buildParameters($templateMock, $languageMock);

        $this->assertEquals([
            'url' => 'http://test.com',
            'templateType' => MailTemplateInterface::HTML_TYPE,
            'templateName' => 'user_account',
            'templateTheme' => 'classic',
            'templateModule' => null,
            'languageIsRTL' => false,
            'languageDefaultFont' => '',
            'locale' => 'en-EN',
        ], $parameters);
    }

    public function testBuildParametersWithRTL()
    {
        $templateInfos = [
            'getType' => MailTemplateInterface::HTML_TYPE,
            'getTheme' => 'classic',
            'getName' => 'user_account',
            'getModule' => 'ps_reminder',
        ];
        $templateMock = $this->buildTemplateMock($templateInfos);
        $languageMock = $this->buildLanguageMock('ar', true);

        $builder = new MailTemplateParametersBuilder();
        $parameters = $builder->buildParameters($templateMock, $languageMock);

        $this->assertEquals([
            'templateType' => MailTemplateInterface::HTML_TYPE,
            'templateName' => 'user_account',
            'templateTheme' => 'classic',
            'templateModule' => 'ps_reminder',
            'languageIsRTL' => true,
            'languageDefaultFont' => 'Tahoma,',
            'locale' => 'ar-AR',
        ], $parameters);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateInterface
     */
    private function buildTemplateMock(array $expectedMethods)
    {
        $templateMock = $this->getMockBuilder(MailTemplateInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        foreach ($expectedMethods as $methodName => $returnValue) {
            $templateMock
                ->expects($this->once())
                ->method($methodName)
                ->willReturn($returnValue)
            ;
        }

        return $templateMock;
    }
}
