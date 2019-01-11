<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\CSSInlineTransformation;
use Symfony\Component\DomCrawler\Crawler;
use DOMElement;
use Language;

class CSSInlineTransformationTest extends TestCase
{
    public function testConstructor()
    {
        $transformation = new CSSInlineTransformation();
        $this->assertNotNull($transformation);
    }

    public function testSetters()
    {
        $transformation = new CSSInlineTransformation();

        $templateMock = $this->getMockBuilder(MailTemplateInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->assertEquals($transformation, $transformation->setTemplate($templateMock));

        $languageMock = $this->getMockBuilder(Language::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->assertEquals($transformation, $transformation->setLanguage($languageMock));
    }

    public function testTxtTemplate()
    {
        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css/titles.css');
        $simpleHtml = $this->createSimpleHtml($cssPath);
        $transformation = new CSSInlineTransformation();

        $transformedHtml = $transformation
            ->setTemplate($this->buildTemplateMock(MailTemplateInterface::TXT_TYPE))
            ->apply($simpleHtml, [])
        ;
        $this->assertEquals($simpleHtml, $transformedHtml);
    }

    public function testSimpleCss()
    {
        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css/titles.css');
        $simpleHtml = $this->createSimpleHtml($cssPath);
        $transformation = new CSSInlineTransformation();

        $transformedHtml = $transformation
            ->setTemplate($this->buildTemplateMock(MailTemplateInterface::HTML_TYPE))
            ->apply($simpleHtml, [])
        ;
        $this->assertNotEquals($simpleHtml, $transformedHtml);

        $crawler = new Crawler($transformedHtml);
        $titleTags = $crawler->filter('h1');
        $this->assertCount(1, $titleTags);
        $this->assertStyle($titleTags->getNode(0), [
            'color' => '#fff',
            'font-size' => '14px',
            'font-weight' => 'bold',
        ]);
    }

    public function testNonExistentCss()
    {
        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css') . '/not_found.css';
        $simpleHtml = $this->createSimpleHtml($cssPath);
        $transformation = new CSSInlineTransformation();

        $transformedHtml = $transformation
            ->setTemplate($this->buildTemplateMock(MailTemplateInterface::HTML_TYPE))
            ->apply($simpleHtml, [])
        ;
        $this->assertNotNull($transformedHtml);

        $crawler = new Crawler($transformedHtml);
        $titleTags = $crawler->filter('h1');
        $this->assertCount(1, $titleTags);
        $this->assertStyle($titleTags->getNode(0), []);
    }

    /**
     * @param DOMElement $node
     * @param array $expectedStyle
     */
    private function assertStyle(DOMElement $node, array $expectedStyle)
    {
        $nodeStyle = $this->getNodeStyle($node);
        $this->assertEquals($expectedStyle, $nodeStyle);
    }

    /**
     * @param DOMElement $node
     *
     * @return array
     */
    private function getNodeStyle(DOMElement $node)
    {
        if (!($styleAttr = $node->attributes->getNamedItem('style'))) {
            return [];
        }
        $style = [];
        $styleAttributes = explode(';', $styleAttr->nodeValue);
        foreach ($styleAttributes as $styleAttribute) {
            if (empty($styleAttribute)) {
                continue;
            }
            $parts = explode(':', $styleAttribute);
            $style[trim($parts[0])] = trim($parts[1]);
        }

        return $style;
    }

    /**
     * @param string $templateType
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateInterface
     */
    private function buildTemplateMock($templateType)
    {
        $templateMock = $this->getMockBuilder(MailTemplateInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $templateMock
            ->expects($this->once())
            ->method('getType')
            ->willReturn($templateType)
        ;

        return $templateMock;
    }

    /**
     * @param string $cssPath
     *
     * @return string
     */
    private function createSimpleHtml($cssPath)
    {
        $simpleHtml = <<<'HTML'
            <!DOCTYPE html>
            <html>
                <head>
                    <link rel="stylesheet" type="text/css" href="file://@CSS_PATH@">
                </head>
                <body>
                    <h1>Test H1</h1>
                </body>
            </html>
HTML;

        return str_replace('@CSS_PATH@', $cssPath, $simpleHtml);
    }
}
