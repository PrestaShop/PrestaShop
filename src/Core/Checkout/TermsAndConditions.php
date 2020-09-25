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

namespace PrestaShop\PrestaShop\Core\Checkout;

/**
 * TermsAndConditions object is used to render "terms and conditions" text sections with links in it
 *
 * @todo: refactor this class to make it stateless
 */
class TermsAndConditions
{
    /**
     * CSS identifier used to build the <a> tag ID
     *
     * @var string
     */
    private $identifier;

    /**
     * List of URLs to use, following a numerical index
     *
     * @var string[]
     */
    private $links;

    /**
     * @var string
     */
    private $rawText;

    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * This function expects more than 1 argument: additionnal arguments
     * are used as links
     *
     * Exemple : $terms->setText('hello [world] [new]', 'http://www.world.com', 'http://new.com');
     *
     * @param string $rawText
     *
     * @return $this
     */
    public function setText($rawText)
    {
        $links = func_get_args();
        array_shift($links);

        $this->links = $links;
        $this->rawText = $rawText;

        return $this;
    }

    /**
     * Parses given raw text, replacing all [something] statements with <a> tags,
     * using URLs from $this->links
     *
     * @return string formatted text, which now contains HTML <a> tags
     */
    public function format()
    {
        $index = 0;

        $formattedText = preg_replace_callback('/\[(.*?)\]/', function (array $match) use (&$index) {
            $textToReplace = $match[1];

            $thereIsAMatchingLink = isset($this->links[$index]);
            if ($thereIsAMatchingLink === false) {
                return $textToReplace;
            }

            $replacement = sprintf(
                '<a href="%s" id="%s">%s</a>',
                $this->links[$index],
                $this->createLinkId($index),
                $textToReplace
            );

            ++$index;

            return $replacement;
        }, $this->rawText);

        return $formattedText;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param int $index
     *
     * @return string
     */
    protected function createLinkId($index)
    {
        return 'cta-' . $this->getIdentifier() . '-' . $index;
    }
}
