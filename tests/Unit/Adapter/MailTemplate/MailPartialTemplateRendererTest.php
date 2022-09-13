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

namespace Tests\Unit\Adapter\MailTemplate;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\MailTemplate\MailPartialTemplateRenderer;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use Smarty;

class MailPartialTemplateRendererTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $requiredConstants = [
            '_PS_THEME_DIR_' => _PS_ROOT_DIR_ . '/themes/classic/',
            '_PS_MAIL_DIR_' => _PS_CORE_DIR_ . '/mails/',
        ];
        foreach ($requiredConstants as $constant => $value) {
            if (!defined($constant)) {
                define($constant, $value);
            }
        }
    }

    public function testUnknownTemplate()
    {
        $smartyMock = $this->buildSmartyMock();

        $renderer = new MailPartialTemplateRenderer($smartyMock);
        $this->assertEquals('', $renderer->render('unknown_template.tpl', $this->buildLanguageMock(), []));
        $this->assertEquals('', $renderer->render('unknown_template.tpl', $this->buildLanguageMock()));
    }

    public function testOrderConfTemplate()
    {
        $smartyMock = $this->buildSmartyMock('order_conf_template');

        $renderer = new MailPartialTemplateRenderer($smartyMock);
        $this->assertEquals('order_conf_template', $renderer->render('order_conf_product_list.tpl', $this->buildLanguageMock(), []));
        $this->assertEquals('order_conf_template', $renderer->render('order_conf_product_list.tpl', $this->buildLanguageMock()));
    }

    /**
     * @return MockObject|Smarty
     */
    private function buildSmartyMock($template = '')
    {
        $smartyMock = $this->getMockBuilder(Smarty::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (empty($template)) {
            $smartyMock
                ->expects($this->never())
                ->method('fetch')
            ;
        } else {
            $smartyMock
                ->expects($this->exactly(2))
                ->method('fetch')
                ->willReturn($template)
            ;
        }

        return $smartyMock;
    }

    /**
     * @return MockObject|LanguageInterface
     */
    private function buildLanguageMock()
    {
        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $languageMock
            ->expects($this->exactly(2))
            ->method('getIsoCode')
            ->willReturn('en')
        ;

        return $languageMock;
    }
}
