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

namespace PrestaShopBundle\Controller\Admin;

use Dispatcher;
use DOMDocument;
use DOMXPath;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyController extends PrestaShopAdminController
{
    public function legacyPageAction(Request $request): Response
    {
        $dispatcherContent = $this->getDispatcherContent();
        if ($request->get('ajax')) {
            return new Response($dispatcherContent);
        }

        $htmlContent = $this->getHtmlContent($dispatcherContent);

        return $this->render('@PrestaShop/Admin/Layout/legacy_layout.html.twig', [
            'legacyContent' => $htmlContent,
        ]);
    }

    private function getDispatcherContent(): string
    {
        ob_start();
        Dispatcher::getInstance()->dispatch();
        $outPutHtml = ob_get_contents();
        ob_end_clean();

        return $outPutHtml;
    }

    private function getHtmlContent(string $dispatcherContent): string
    {
        $dom = new DOMDocument();
        $dom->loadHTML($dispatcherContent);

        $xpath = new DOMXPath($dom);
        $elementNodeList = $xpath->query('//*[@id="content"]');
        if ($elementNodeList->count() === 0) {
            throw new CoreException('Generated legacy html has no content div found');
        }

        $content = '';
        $elementNode = $elementNodeList->item(0);
        foreach ($elementNode->childNodes->getIterator() as $childNode) {
            $content .= $dom->saveHTML($childNode);
        }

        return $content;
    }
}
