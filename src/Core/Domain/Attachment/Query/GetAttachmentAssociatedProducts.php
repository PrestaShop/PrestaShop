<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Query;

use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Gets the products an attachment is associated with
 */
class GetAttachmentAssociatedProducts
{
    private AttachmentId $attachmentId;

    private LanguageId $languageId;

    /**
     * @param int $attachmentId
     * @param int $languageId language of the product names
     * @param ShopConstraint $shopConstraint shop whose name and status are returned, each product's default shop
     *                                       when the constraint is not a single shop
     */
    public function __construct(
        int $attachmentId,
        int $languageId,
        private readonly ShopConstraint $shopConstraint,
    ) {
        $this->attachmentId = new AttachmentId($attachmentId);
        $this->languageId = new LanguageId($languageId);
    }

    public function getAttachmentId(): AttachmentId
    {
        return $this->attachmentId;
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
