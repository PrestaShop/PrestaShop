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

namespace Tests\Unit\Adapter\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailTemplateTwigRenderer;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateParametersBuilderInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\MailTemplateTransformationInterface;
use Symfony\Component\Templating\EngineInterface;
use Language;

class MailTemplateTwigRendererTest extends TestCase
{
    public function testConstructor()
    {
        $engineMock = $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builderMock = $this->getMockBuilder(MailTemplateParametersBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateTwigRenderer($engineMock, $builderMock);
        $this->assertNotNull($generator);
    }

    public function testRenderHtml()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => 'path/to/test_template.html.twig',
        ];
        $expectedTemplate = 'mail_template';
        $expectedParameters = ['locale' => null, 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $template = $this->createMailTemplateMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::HTML_TYPE], $expectedParameters, $expectedTemplate),
            $this->createParametersBuilderMock($expectedParameters, $expectedLanguage)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderHtml($template, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderHtmlWithFallback()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => '',
            MailTemplateInterface::TXT_TYPE => 'path/to/test_template.txt.twig',
        ];
        $expectedTemplate = 'mail_template';
        $expectedParameters = ['locale' => null, 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $template = $this->createMailTemplateMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::TXT_TYPE], $expectedParameters, $expectedTemplate),
            $this->createParametersBuilderMock($expectedParameters, $expectedLanguage)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderHtml($template, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderTxt()
    {
        $templatePaths = [
            MailTemplateInterface::TXT_TYPE => 'path/to/test_template.txt.twig',
        ];
        $expectedTemplate = 'mail_template';
        $expectedParameters = ['locale' => null, 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $template = $this->createMailTemplateMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::TXT_TYPE], $expectedParameters, $expectedTemplate),
            $this->createParametersBuilderMock($expectedParameters, $expectedLanguage)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderTxt($template, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderTxtFallback()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => 'path/to/test_template.html.twig',
            MailTemplateInterface::TXT_TYPE => '',
        ];
        $expectedTemplate = 'mail_template';
        $expectedParameters = ['locale' => null, 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $template = $this->createMailTemplateMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::HTML_TYPE], $expectedParameters, $expectedTemplate),
            $this->createParametersBuilderMock($expectedParameters, $expectedLanguage)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderTxt($template, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderWithTransformations()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => 'path/to/test_template.html.twig',
        ];
        $generatedTemplate = 'mail_template';
        $transformedTemplate = 'mail_template_transformed_fr';
        $expectedParameters = ['locale' => 'fr', 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $template = $this->createMailTemplateMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::HTML_TYPE], $expectedParameters, $generatedTemplate),
            $this->createParametersBuilderMock($expectedParameters, $expectedLanguage)
        );
        $this->assertNotNull($generator);

        $generator->addTransformation($this->createTransformationMock($generatedTemplate, $expectedParameters, MailTemplateInterface::HTML_TYPE));
        $generatedTemplate = $generator->renderHtml($template, $expectedLanguage);
        $this->assertEquals($transformedTemplate, $generatedTemplate);
    }

    /**
     * @param string $initialTemplate
     * @param array $expectedParameters
     * @param string $templateType
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateTransformationInterface
     */
    private function createTransformationMock($initialTemplate, $expectedParameters, $templateType)
    {
        $transformationMock = $this->getMockBuilder(MailTemplateTransformationInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $transformationMock
            ->expects($this->once())
            ->method('apply')
            ->with(
                $this->equalTo($initialTemplate),
                $this->equalTo($expectedParameters)
            )
            ->will($this->returnCallback(function($templateContent, array $templateVariables) {
                return $templateContent.'_transformed_'.$templateVariables['locale'];
            }))
        ;

        $transformationMock
            ->expects($this->once())
            ->method('getType')
            ->willReturn($templateType)
        ;

        $transformationMock
            ->expects($this->once())
            ->method('setTemplate')
            ->willReturn($transformationMock)
        ;

        $transformationMock
            ->expects($this->once())
            ->method('setLanguage')
            ->willReturn($transformationMock)
        ;

        return $transformationMock;
    }

    /**
     * @param string $expectedPath
     * @param array  $expectedParameters
     * @param string $generatedTemplate
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|EngineInterface
     */
    private function createEngineMock($expectedPath, array $expectedParameters, $generatedTemplate)
    {
        $engineMock = $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $engineMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($expectedPath),
                $this->equalTo($expectedParameters)
            )
            ->willReturn($generatedTemplate)
        ;

        return $engineMock;
    }

    /**
     * @param array $parameters
     * @param Language $expectedLanguage
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateParametersBuilderInterface
     */
    private function createParametersBuilderMock(array $parameters, Language $expectedLanguage)
    {
        $builderMock = $this->getMockBuilder(MailTemplateParametersBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builderMock
            ->expects($this->once())
            ->method('buildParameters')
            ->with(
                $this->isInstanceOf(MailTemplateInterface::class),
                $this->equalTo($expectedLanguage)
            )
            ->willReturn($parameters)
        ;

        return $builderMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Language
     */
    private function createLanguageMock()
    {
        $languageMock = $this->getMockBuilder(Language::class)
            ->disableOriginalConstructor()
            ->getMock();
        ;

        return $languageMock;
    }

    /**
     * @param array $expectedPaths
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateInterface
     */
    private function createMailTemplateMock(array $expectedPaths)
    {
        $templateMock = $this->getMockBuilder(MailTemplateInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (isset($expectedPaths[MailTemplateInterface::HTML_TYPE])) {
            $templateMock
                ->expects($this->atLeastOnce())
                ->method('getHtmlPath')
                ->willReturn($expectedPaths[MailTemplateInterface::HTML_TYPE])
            ;
        }

        if (isset($expectedPaths[MailTemplateInterface::TXT_TYPE])) {
            $templateMock
                ->expects($this->atLeastOnce())
                ->method('getTxtPath')
                ->willReturn($expectedPaths[MailTemplateInterface::TXT_TYPE])
            ;
        }

        return $templateMock;
    }

}
