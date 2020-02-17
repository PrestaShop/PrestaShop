<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\MailTemplate;

use DOMElement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\CSSInlineTransformation;
use Symfony\Component\DomCrawler\Crawler;

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

        $languageMock = $this->getMockBuilder(LanguageInterface::class)
             ->disableOriginalConstructor()
             ->getMock()
         ;
        $this->assertEquals($transformation, $transformation->setLanguage($languageMock));
    }

    public function testSimpleCss()
    {
        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css/titles.css');
        $simpleHtml = $this->createSimpleHtml($cssPath);
        $transformation = new CSSInlineTransformation();

        $transformedHtml = $transformation
            ->apply($simpleHtml, [], $this->buildLanguageMock())
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
            ->apply($simpleHtml, [], $this->buildLanguageMock())
        ;
        $this->assertNotNull($transformedHtml);

        $crawler = new Crawler($transformedHtml);
        $titleTags = $crawler->filter('h1');
        $this->assertCount(1, $titleTags);
        $this->assertStyle($titleTags->getNode(0), []);
    }

    public function testCssSizes()
    {
        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css') . '/sizes.css';
        $simpleHtml = $this->createSimpleHtml($cssPath);
        $transformation = new CSSInlineTransformation();

        $transformedHtml = $transformation
            ->apply($simpleHtml, [], $this->buildLanguageMock())
        ;
        $this->assertNotNull($transformedHtml);

        $crawler = new Crawler($transformedHtml);
        $spanTags = $crawler->filter('.account_details');
        $this->assertCount(1, $spanTags);
        $spanTag = $spanTags->getNode(0);
        $widthAttribute = $spanTag->attributes->getNamedItem('width');
        $this->assertNotNull($widthAttribute);
        $this->assertEquals(100, $widthAttribute->nodeValue);
    }

    public function testCssOverride()
    {
        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css') . '/override.css';
        $simpleHtml = $this->createSimpleHtml($cssPath);
        $transformation = new CSSInlineTransformation();

        $transformedHtml = $transformation
            ->apply($simpleHtml, [], $this->buildLanguageMock())
        ;
        $this->assertNotNull($transformedHtml);

        $crawler = new Crawler($transformedHtml);
        $divTags = $crawler->filter('.promo_code');
        $this->assertCount(2, $divTags);

        $firstDiv = $divTags->getNode(0);
        $bgColorAttribute = $firstDiv->attributes->getNamedItem('bgcolor');
        $this->assertNotNull($bgColorAttribute);
        $this->assertEquals('#ff0000', $bgColorAttribute->nodeValue);
        $this->assertStyle($firstDiv, ['background-color' => '#ff0000']);

        $secondDiv = $divTags->getNode(1);
        $bgColorAttribute = $secondDiv->attributes->getNamedItem('bgcolor');
        $this->assertNotNull($bgColorAttribute);
        $this->assertEquals('#00ff00', $bgColorAttribute->nodeValue);
        $this->assertStyle($secondDiv, ['background-color' => '#00ff00']);
    }

    public function testMultipleCss()
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html>
                <head>
                    <link rel="stylesheet" type="text/css" href="file://@TITLES_CSS_PATH@">
                    <link rel="stylesheet" type="text/css" href="file://@SIZES_CSS_PATH@">
                </head>
                <body>
                    <h1>Test H1</h1>
                    <span class="account_details">
                        Account details
                    </span>
                </body>
            </html>
HTML;

        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css') . '/titles.css';
        $html = str_replace('@TITLES_CSS_PATH@', $cssPath, $html);
        $cssPath = realpath(__DIR__ . '/../../Resources/assets/css') . '/sizes.css';
        $html = str_replace('@SIZES_CSS_PATH@', $cssPath, $html);

        $transformation = new CSSInlineTransformation();

        $transformedHtml = $transformation
            ->apply($html, [], $this->buildLanguageMock())
        ;
        $this->assertNotEquals($html, $transformedHtml);
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
     * @return MockObject|LanguageInterface
     */
    private function buildLanguageMock()
    {
        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $languageMock;
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
                    <span class="account_details">
                        Account details
                    </span>
                    <div class="promo_code" style="background-color: #ff0000">
                        Super Promo
                    </div>
                    <div class="promo_code">
                        Super Promo
                    </div>
                </body>
            </html>
HTML;

        return str_replace('@CSS_PATH@', $cssPath, $simpleHtml);
    }
}
