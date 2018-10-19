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

namespace PrestaShop\PrestaShop\Core\Util\HelperCard;

use PrestaShop\PrestaShop\Core\HelperDoc\HelperDocLinkProviderInterface;

/**
 * Class HelperCardDocumentationLinkProvider provides documentation links for helper cards.
 */
final class HelperCardDocumentationLinkProvider implements HelperDocLinkProviderInterface
{
    /**
     * @var string
     */
    private $contextLangIsoCode;

    /**
     * @var array
     */
    private $availableDocumentationLinks;

    /**
     * @var string
     */
    private $fallbackDocumentationLink;

    /**
     * @param string $contextLangIsoCode
     * @param array $availableDocumentationLinks
     * @param string $fallbackDocumentationLink
     */
    public function __construct(
        $contextLangIsoCode,
        array $availableDocumentationLinks,
        $fallbackDocumentationLink
    ) {
        $this->contextLangIsoCode = $contextLangIsoCode;
        $this->availableDocumentationLinks = $availableDocumentationLinks;
        $this->fallbackDocumentationLink = $fallbackDocumentationLink;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        if (isset($this->availableDocumentationLinks[$this->contextLangIsoCode])) {
            return $this->availableDocumentationLinks[$this->contextLangIsoCode];
        }

        return $this->fallbackDocumentationLink;
    }
}
