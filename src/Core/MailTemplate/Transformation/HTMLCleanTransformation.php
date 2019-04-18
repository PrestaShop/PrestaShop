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

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use Symfony\Component\DomCrawler\Crawler;
use DOMElement;

class HTMLCleanTransformation extends AbstractTransformation
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
        $templateContent = $this->removeTxtOnly($templateContent);
        $templateContent = $this->removeContainer($templateContent, 'html-only');
        $templateContent = $this->removeContainer($templateContent, 'mj-raw');

        //DOMElement escapes {variables} in src of href attributes
        $templateContent = preg_replace('/href="%7B(.*?)%7D"/', 'href="{\1}"', $templateContent);
        $templateContent = preg_replace('/src="%7B(.*?)%7D"/', 'src="{\1}"', $templateContent);

        return $templateContent;
    }

    /**
     * @param string $templateContent
     * @param string $containerType
     *
     * @return string
     */
    private function removeContainer($templateContent, $containerType)
    {
        $crawler = new Crawler($templateContent);

        $crawler->filter($containerType)->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                /** @var DOMElement $childNode */
                foreach ($node->childNodes as $childNode) {
                    $node->parentNode->insertBefore($childNode->cloneNode(), $node);
                }
                $node->parentNode->removeChild($node);
            }
        });

        $filteredContent = '';
        foreach ($crawler as $domElement) {
            $filteredContent .= $domElement->ownerDocument->saveHTML($domElement);
        }

        return $filteredContent;
    }

    /**
     * @param string $templateContent
     *
     * @return string
     */
    private function removeTxtOnly($templateContent)
    {
        $crawler = new Crawler($templateContent);

        $crawler->filter('[data-text-only=1]')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $filteredContent = '';
        foreach ($crawler as $domElement) {
            $filteredContent .= $domElement->ownerDocument->saveHTML($domElement);
        }

        return $filteredContent;
    }
}
