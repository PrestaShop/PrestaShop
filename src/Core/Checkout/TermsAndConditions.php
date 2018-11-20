<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Checkout;

class TermsAndConditions
{
    private $identifier;
    private $links;
    private $rawText;

    public function setText($rawText)
    {
        $links = func_get_args();
        array_shift($links);

        $this->links = $links;
        $this->rawText = $rawText;
        return $this;
    }

    /**
     * Inserts links into the text, replacing all [something] with links to "something", taking
     * URLs from $this->links
     * @return an string of HTML
     */
    public function format()
    {
        $index = 0;
        return preg_replace_callback('/\[(.*?)\]/', function (array $match) use (&$index) {
            if (!isset($this->links[$index])) {
                return $match[1];
            }

            $replacement = '<a href="' . $this->links[$index] . '" id="' . $this->createLinkId($index) . '">' . $match[1] . '</a>';
            ++$index;
            return $replacement;
        }, $this->rawText);
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    protected function createLinkId($index)
    {
        return 'cta-' . $this->getIdentifier() . '-' . $index;
    }
}
