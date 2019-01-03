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
use PrestaShopBundle\Service\Mail\MailTemplate;
use PrestaShopBundle\Service\Mail\MailTemplateRenderer;
use PrestaShopBundle\Service\Mail\MailTemplateInterface;
use Symfony\Component\Templating\EngineInterface;

class MailTemplateRendererTest extends TestCase
{
    public function testConstructor()
    {
        $engineMock = $this->getMockBuilder(EngineInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateRenderer($engineMock);
        $this->assertNotNull($generator);
    }

    public function testRender()
    {
        $expectedTemplate = 'mail_template';
        $expectedPath = 'path/to/test_template.twig';
        $expectedParameters = ['_locale' => null];

        $generator = new MailTemplateRenderer($this->createEngineMock($expectedPath, $expectedTemplate, $expectedParameters));
        $this->assertNotNull($generator);

        $template = new MailTemplate(
            'unrelevant',
            MailTemplateInterface::CORE_TEMPLATES,
            'unrelevant',
            $expectedPath,
            null
        );
        $generatedTemplate = $generator->render($template);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderDefaultLocale()
    {
        $expectedTemplate = 'mail_template';
        $expectedPath = 'path/to/test_template.twig';
        $expectedParameters = [
            '_locale' => 'en',
        ];

        $generator = new MailTemplateRenderer($this->createEngineMock($expectedPath, $expectedTemplate, $expectedParameters));
        $this->assertNotNull($generator);

        $template = new MailTemplate(
            'unrelevant',
            MailTemplateInterface::CORE_TEMPLATES,
            'unrelevant',
            $expectedPath,
            null
        );
        $generator->setDefaultLocale('en');
        $generatedTemplate = $generator->render($template);
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    public function testRenderLocale()
    {
        $expectedTemplate = 'mail_template';
        $expectedPath = 'path/to/test_template.twig';
        $expectedParameters = [
            '_locale' => 'fr',
        ];

        $generator = new MailTemplateRenderer($this->createEngineMock($expectedPath, $expectedTemplate, $expectedParameters));
        $this->assertNotNull($generator);

        $template = new MailTemplate(
            'unrelevant',
            MailTemplateInterface::CORE_TEMPLATES,
            'unrelevant',
            $expectedPath,
            null
        );
        $generator->setDefaultLocale('en');
        $generatedTemplate = $generator->render($template, 'fr');
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }

    /**
     * @param string $expectedPath
     * @param string $expectedTemplate
     * @param array  $expectedParameters
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|EngineInterface
     */
    private function createEngineMock($expectedPath, $expectedTemplate, array $expectedParameters)
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
            ->willReturn($expectedTemplate)
        ;

        return $engineMock;
    }
}
