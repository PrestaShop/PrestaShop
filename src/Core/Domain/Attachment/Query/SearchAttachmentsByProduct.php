<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Query;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptySearchInputException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Finds the attachments of the products whose ID is the searched phrase, or whose reference or name contains it
 */
class SearchAttachmentsByProduct
{
    public const MAX_RESULTS = 100;

    private string $phrase;

    private LanguageId $languageId;

    /**
     * @param string $phrase product ID, or part of the product reference or name
     * @param int $languageId language of the product and attachment names
     * @param ShopConstraint $shopConstraint shop whose product name and status are returned, each product's default
     *                                       shop when the constraint is not a single shop
     *
     * @throws EmptySearchInputException
     */
    public function __construct(
        string $phrase,
        int $languageId,
        private readonly ShopConstraint $shopConstraint,
    ) {
        $phrase = trim($phrase);
        if ('' === $phrase) {
            throw new EmptySearchInputException('Search phrase cannot be empty');
        }

        $this->phrase = $phrase;
        $this->languageId = new LanguageId($languageId);
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
