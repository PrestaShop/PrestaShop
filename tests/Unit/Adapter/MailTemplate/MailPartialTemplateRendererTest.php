<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
