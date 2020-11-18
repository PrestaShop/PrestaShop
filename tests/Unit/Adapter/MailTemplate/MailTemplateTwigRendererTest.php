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

namespace Tests\Unit\Adapter\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailTemplateTwigRenderer;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutVariablesBuilderInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\TransformationCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\TransformationInterface;
use Symfony\Component\Templating\EngineInterface;

class MailTemplateTwigRendererTest extends TestCase
{
    public function testConstructor()
    {
        $engineMock = $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builderMock = $this->getMockBuilder(LayoutVariablesBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateTwigRenderer($engineMock, $builderMock, $dispatcherMock);
        $this->assertNotNull($generator);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\FileNotFoundException
     */
    public function testFileNotFound()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => 'path/to/non_existent_template.html.twig',
        ];
        $expectedVariables = ['locale' => null, 'url' => 'http://test.com'];
        $expectedLanguage = $this->createLanguageMock();
        $mailLayout = $this->createMailLayoutMock($templatePaths);
        /** @var EngineInterface $engineMock */
        $engineMock = $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateTwigRenderer(
            $engineMock,
            $this->createVariablesBuilderMock($expectedVariables, $expectedLanguage),
            $dispatcherMock
        );
        $this->assertNotNull($generator);

        $generator->renderHtml($mailLayout, $expectedLanguage);
    }

    public function testRenderHtml()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => __DIR__ . '/../../Resources/mails/templates/account.html.twig',
        ];
        $expectedTemplate = 'mail_template';
        $expectedVariables = ['locale' => null, 'url' => 'http://test.com', 'templateType' => MailTemplateInterface::HTML_TYPE];
        $expectedLanguage = $this->createLanguageMock();
        $mailLayout = $this->createMailLayoutMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::HTML_TYPE], $expectedVariables, $expectedTemplate),
            $this->createVariablesBuilderMock($expectedVariables, $expectedLanguage),
            $this->createHookDispatcherMock($mailLayout, MailTemplateInterface::HTML_TYPE)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderHtml($mailLayout, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderHtmlWithFallback()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => '',
            MailTemplateInterface::TXT_TYPE => __DIR__ . '/../../Resources/mails/templates/account.html.twig',
        ];
        $expectedTemplate = 'mail_template';
        $expectedVariables = ['locale' => null, 'url' => 'http://test.com', 'templateType' => MailTemplateInterface::HTML_TYPE];
        $expectedLanguage = $this->createLanguageMock();
        $mailLayout = $this->createMailLayoutMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::TXT_TYPE], $expectedVariables, $expectedTemplate),
            $this->createVariablesBuilderMock($expectedVariables, $expectedLanguage),
            $this->createHookDispatcherMock($mailLayout, MailTemplateInterface::HTML_TYPE)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderHtml($mailLayout, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderTxt()
    {
        $templatePaths = [
            MailTemplateInterface::TXT_TYPE => __DIR__ . '/../../Resources/mails/templates/account.html.twig',
        ];
        $expectedTemplate = 'mail_template';
        $expectedVariables = ['locale' => null, 'url' => 'http://test.com', 'templateType' => MailTemplateInterface::TXT_TYPE];
        $expectedLanguage = $this->createLanguageMock();
        $mailLayout = $this->createMailLayoutMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::TXT_TYPE], $expectedVariables, $expectedTemplate),
            $this->createVariablesBuilderMock($expectedVariables, $expectedLanguage),
            $this->createHookDispatcherMock($mailLayout, MailTemplateInterface::TXT_TYPE)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderTxt($mailLayout, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderTxtFallback()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => __DIR__ . '/../../Resources/mails/templates/account.html.twig',
            MailTemplateInterface::TXT_TYPE => '',
        ];
        $expectedTemplate = 'mail_template';
        $expectedVariables = ['locale' => null, 'url' => 'http://test.com', 'templateType' => MailTemplateInterface::TXT_TYPE];
        $expectedLanguage = $this->createLanguageMock();
        $mailLayout = $this->createMailLayoutMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::HTML_TYPE], $expectedVariables, $expectedTemplate),
            $this->createVariablesBuilderMock($expectedVariables, $expectedLanguage),
            $this->createHookDispatcherMock($mailLayout, MailTemplateInterface::TXT_TYPE)
        );
        $this->assertNotNull($generator);

        $generatedTemplate = $generator->renderTxt($mailLayout, $expectedLanguage);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderWithTransformations()
    {
        $templatePaths = [
            MailTemplateInterface::HTML_TYPE => __DIR__ . '/../../Resources/mails/templates/account.html.twig',
        ];
        $generatedTemplate = 'mail_template';
        $transformedTemplate = 'mail_template_transformed_fr';
        $expectedVariables = ['locale' => 'fr', 'url' => 'http://test.com', 'templateType' => MailTemplateInterface::HTML_TYPE];
        $expectedLanguage = $this->createLanguageMock();
        $mailLayout = $this->createMailLayoutMock($templatePaths);

        $generator = new MailTemplateTwigRenderer(
            $this->createEngineMock($templatePaths[MailTemplateInterface::HTML_TYPE], $expectedVariables, $generatedTemplate),
            $this->createVariablesBuilderMock($expectedVariables, $expectedLanguage),
            $this->createHookDispatcherMock($mailLayout, MailTemplateInterface::HTML_TYPE, 1)
        );
        $this->assertNotNull($generator);

        $generator->addTransformation($this->createTransformationMock($generatedTemplate, $expectedVariables, MailTemplateInterface::HTML_TYPE));
        $generatedTemplate = $generator->renderHtml($mailLayout, $expectedLanguage);
        $this->assertEquals($transformedTemplate, $generatedTemplate);
    }

    /**
     * @param string $initialTemplate
     * @param array $expectedVariables
     * @param string $templateType
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|TransformationInterface
     */
    private function createTransformationMock($initialTemplate, $expectedVariables, $templateType)
    {
        $transformationMock = $this->getMockBuilder(TransformationInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $transformationMock
            ->expects($this->once())
            ->method('apply')
            ->with(
                $this->equalTo($initialTemplate),
                $this->equalTo($expectedVariables)
            )
            ->will($this->returnCallback(function ($templateContent, array $templateVariables) {
                return $templateContent . '_transformed_' . $templateVariables['locale'];
            }))
        ;

        $transformationMock
            ->expects($this->once())
            ->method('getType')
            ->willReturn($templateType)
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
     * @param array $expectedVariables
     * @param string $generatedTemplate
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|EngineInterface
     */
    private function createEngineMock($expectedPath, array $expectedVariables, $generatedTemplate)
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
                $this->equalTo($expectedVariables)
            )
            ->willReturn($generatedTemplate)
        ;

        return $engineMock;
    }

    /**
     * @param LayoutInterface $mailLayout
     * @param string $templateType
     * @param int $transformationsCount
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|HookDispatcherInterface
     */
    private function createHookDispatcherMock(LayoutInterface $mailLayout, $templateType, $transformationsCount = 0)
    {
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dispatcherMock
            ->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo(MailTemplateRendererInterface::GET_MAIL_LAYOUT_TRANSFORMATIONS),
                $this->callback(function (array $hookParameters) use ($mailLayout, $templateType, $transformationsCount) {
                    $this->assertEquals($mailLayout, $hookParameters['mailLayout']);
                    $this->assertEquals($templateType, $hookParameters['templateType']);
                    $this->assertInstanceOf(TransformationCollectionInterface::class, $hookParameters['layoutTransformations']);
                    $this->assertCount($transformationsCount, $hookParameters['layoutTransformations']);

                    return true;
                })
            )
        ;

        return $dispatcherMock;
    }

    /**
     * @param array $variables
     * @param LanguageInterface $expectedLanguage
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LayoutVariablesBuilderInterface
     */
    private function createVariablesBuilderMock(array $variables, LanguageInterface $expectedLanguage)
    {
        $builderMock = $this->getMockBuilder(LayoutVariablesBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builderMock
            ->expects($this->once())
            ->method('buildVariables')
            ->with(
                $this->isInstanceOf(LayoutInterface::class),
                $this->equalTo($expectedLanguage)
            )
            ->willReturn($variables)
        ;

        return $builderMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LanguageInterface
     */
    private function createLanguageMock()
    {
        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $languageMock;
    }

    /**
     * @param array $expectedPaths
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LayoutInterface
     */
    private function createMailLayoutMock(array $expectedPaths)
    {
        $mailLayoutMock = $this->getMockBuilder(LayoutInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (isset($expectedPaths[MailTemplateInterface::HTML_TYPE])) {
            $mailLayoutMock
                ->expects($this->atLeastOnce())
                ->method('getHtmlPath')
                ->willReturn($expectedPaths[MailTemplateInterface::HTML_TYPE])
            ;
        }

        if (isset($expectedPaths[MailTemplateInterface::TXT_TYPE])) {
            $mailLayoutMock
                ->expects($this->atLeastOnce())
                ->method('getTxtPath')
                ->willReturn($expectedPaths[MailTemplateInterface::TXT_TYPE])
            ;
        }

        return $mailLayoutMock;
    }
}
