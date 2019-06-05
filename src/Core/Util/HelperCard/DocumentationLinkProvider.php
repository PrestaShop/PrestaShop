<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Util\HelperCard;

/**
 * Class HelperCardDocumentationLinkProvider provides documentation links for helper cards.
 */
final class DocumentationLinkProvider implements DocumentationLinkProviderInterface
{
    /**
     * @var string
     */
    private $contextLangIsoCode;

    /**
     * @var array
     */
    private $documentationLinks;

    /**
     * @param string $contextLangIsoCode
     * @param array $documentationLinks
     */
    public function __construct(
        $contextLangIsoCode,
        array $documentationLinks
    ) {
        $this->contextLangIsoCode = $contextLangIsoCode;
        $this->documentationLinks = $documentationLinks;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink($cardType)
    {
        if (isset($this->documentationLinks[$cardType])) {
            $cardLinks = $this->documentationLinks[$cardType];

            if (isset($cardLinks[$this->contextLangIsoCode])) {
                return $cardLinks[$this->contextLangIsoCode];
            }

            if (isset($cardLinks['_fallback'])) {
                return $cardLinks['_fallback'];
            }
        }

        throw new HelperCardDocumentationDoesNotExistException(
            sprintf('Documentation for helper card "%s" does not exist', $cardType)
        );
    }
}
