<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Install;

use DOMDocument;
use ReturnTypeWillChange;

class SimplexmlElement extends \SimpleXMLElement
{
    /**
     * Can add SimpleXMLElement values in XML tree.
     *
     * @param string $name
     * @param string|SimplexmlElement|null $value
     * @param string|null $namespace
     *
     * @return \SimpleXMLElement|void
     */
    public function addChild($name, $value = null, $namespace = null)
    {
        if ($value instanceof static) {
            $content = trim((string) $value);
            if (strlen($content) > 0) {
                $new_element = parent::addChild($name, str_replace('&', '&amp;', $content), $namespace);
            } else {
                $new_element = parent::addChild($name);
                foreach ($value->attributes() as $k => $v) {
                    $new_element->addAttribute($k, $v);
                }
            }

            foreach ($value->children() as $child) {
                $new_element->addChild($child->getName(), $child);
            }
        } else {
            return parent::addChild($name, str_replace('&', '&amp;', $value), $namespace);
        }
    }

    /**
     * Generate nice and sweet XML.
     *
     * @see SimpleXMLElement::asXML()
     */
    #[ReturnTypeWillChange]
    public function asXML($filename = null)
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML(parent::asXML());

        if ($filename) {
            return (bool) file_put_contents($filename, $dom->saveXML());
        }

        return $dom->saveXML();
    }
}
