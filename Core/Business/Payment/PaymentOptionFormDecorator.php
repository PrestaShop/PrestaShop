<?php

namespace PrestaShop\PrestaShop\Core\Business\Payment;

use DOMDocument;
use DOMNode;

class PaymentOptionFormDecorator
{
    public function addHiddenSubmitButton($formHTML, $optionId)
    {
        $doc = new DOMDocument;

        $doc->loadHTML($formHTML);

        $forms = $doc->getElementsByTagName('form');
        if ($forms->length !== 1) {
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

        $forms[0]->appendChild($hiddenSubmitButton);

        $body = $doc->getElementsByTagName('body')[0];
        $html = '';

        foreach ($body->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }

        return $html;
    }
}
