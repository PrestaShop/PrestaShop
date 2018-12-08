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

namespace PrestaShop\PrestaShop\Core\Payment;

use DOMDocument;

class PaymentOptionFormDecorator
{
    public function addHiddenSubmitButton($formHTML, $optionId)
    {
        $doc = new DOMDocument();
        $doc->encoding = 'UTF-8';

        $doc->loadHTML('<?xml encoding="UTF-8">'.$formHTML);

        $forms = $doc->getElementsByTagName('form');
        if (1 !== $forms->length) {
            return false;
        }

        $hiddenSubmitButton = $doc->createElement('button');

        $styleAttr = $doc->createAttribute('style');
        $styleAttr->value = 'display:none';

        $idAttr = $doc->createAttribute('id');
        $idAttr->value = 'pay-with-'.$optionId;

        $typeAttr = $doc->createAttribute('type');
        $typeAttr->value = 'submit';

        $hiddenSubmitButton->appendChild($styleAttr);
        $hiddenSubmitButton->appendChild($idAttr);
        $hiddenSubmitButton->appendChild($typeAttr);

        $forms->item(0)->appendChild($hiddenSubmitButton);

        $body = $doc->getElementsByTagName('body')->item(0);
        $html = '';

        foreach ($body->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }

        return $html;
    }
}
