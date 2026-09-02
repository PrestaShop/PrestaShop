<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use DOMAttr;
use DOMElement;
use Pelago\Emogrifier;
use Pelago\Emogrifier\HtmlProcessor\CssToAttributeConverter;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use Symfony\Component\DomCrawler\Crawler;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Class CSSInlineTransformation applies a transformation on html templates, it downloads
 * each css files integrated in the template, and then applies them on the html inline-style.
 * This is used for some mail readers which don't load css styles but can interpret them when
 * set inline.
 */
class CSSInlineTransformation extends AbstractTransformation
{
    public function __construct()
    {
        parent::__construct(MailTemplateInterface::HTML_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function apply($templateContent, array $templateVariables)
    {
        if (MailTemplateInterface::HTML_TYPE != $this->type) {
            return $templateContent;
        }

        /**
         * For unknown reason Emogrifier modifies href attribute with variables written
         * like this {shop_url} so we temporarily change them to @shop_url@
         */
        $templateContent = preg_replace('/\{(\w+)\}/', '@\1@', $templateContent);

        $cssContent = $this->getCssContent($templateContent);

        $cssToInlineStyles = new CssToInlineStyles();
        $templateContent = $cssToInlineStyles->convert($templateContent, $cssContent);

        $converter = CssToAttributeConverter::fromHtml($templateContent);
        $templateContent = $converter->convertCssToVisualAttributes()->render();

        return preg_replace('/@(\w+)@/', '{\1}', $templateContent);
    }

    /**
     * @param string $templateContent
     *
     * @return string
     */
    private function getCssContent($templateContent)
    {
        $crawler = new Crawler($templateContent);
        $cssTags = $crawler->filter('link[type="text/css"]');
        $cssUrls = [];
        /** @var DOMElement $cssTag */
        foreach ($cssTags as $cssTag) {
            /** @var DOMAttr $hrefAttr */
            if ($hrefAttr = $cssTag->attributes->getNamedItem('href')) {
                $cssUrls[] = $hrefAttr->nodeValue;
            }
        }
        $cssContents = '';
        foreach ($cssUrls as $cssUrl) {
            $cssContent = @file_get_contents($cssUrl);
            if (!empty($cssContent)) {
                $cssContents .= $cssContent;
            }
        }

        return $cssContents;
    }
}
