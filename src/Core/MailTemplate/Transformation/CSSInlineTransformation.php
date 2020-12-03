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
