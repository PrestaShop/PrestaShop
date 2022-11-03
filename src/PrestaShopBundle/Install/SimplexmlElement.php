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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Install;

use DOMDocument;

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
    #[\ReturnTypeWillChange]
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
