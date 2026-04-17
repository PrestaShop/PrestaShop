<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        throw new HelperCardDocumentationDoesNotExistException(sprintf('Documentation for helper card "%s" does not exist', $cardType));
    }
}
