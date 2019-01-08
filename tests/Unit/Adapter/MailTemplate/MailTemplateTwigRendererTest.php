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

    public function testRender()
    {
        $expectedTemplate = 'mail_template';
        $expectedPath = 'path/to/test_template.twig';
        $expectedParameters = ['locale' => null, 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $template = $this->createMailTemplateMock($expectedPath);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($expectedPath, $expectedParameters, $expectedTemplate),
            $this->createParametersBuilderMock($expectedParameters, $expectedLanguage)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->render($template, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderWithTransformations()
    {
        $generatedTemplate = 'mail_template';
        $transformedTemplate = 'mail_template_transformed_fr';
        $expectedPath = 'path/to/test_template.twig';
        $expectedParameters = ['locale' => 'fr', 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $template = $this->createMailTemplateMock($expectedPath);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($expectedPath, $expectedParameters, $generatedTemplate),
            $this->createParametersBuilderMock($expectedParameters, $expectedLanguage)
        );
        $this->assertNotNull($generator);

        $generator->addTransformation($this->createTransformationMock());
        $generatedTemplate = $generator->render($template, $expectedLanguage);
        $this->assertEquals($transformedTemplate, $generatedTemplate);
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateTransformationInterface
     */
    private function createTransformationMock()
    {
        $transformationMock = $this->getMockBuilder(MailTemplateTransformationInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $transformationMock
            ->expects($this->once())
            ->method('apply')
            ->will($this->returnCallback(function($templateContent, array $templateVariables) {
                return $templateContent.'_transformed_'.$templateVariables['locale'];
            }))
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
     * @param string $expectedPath
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateInterface
     */
    private function createMailTemplateMock($expectedPath)
    {
        $templateMock = $this->getMockBuilder(MailTemplateInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $templateMock
            ->expects($this->once())
            ->method('getPath')
            ->willReturn($expectedPath)
        ;

        return $templateMock;
    }

}
