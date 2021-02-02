<?php

/**
 * Description of SimpleXMLElement
 *
 * @author Matej Kminek <matej.kminek@attendees.eu>, 10. 11. 2020
 */
class SuperXMLElement extends \SimpleXMLElement
{

    public function addChildCData(string $name, ?string $value = null): SuperXMLElement
    {
        $new = $this->addChild($name);
        if ($value !== null) {
            $base = dom_import_simplexml($new);
            $docOwner = $base->ownerDocument;
            $base->appendChild($docOwner->createCDATASection($value));
        }
        return $new;
    }
}