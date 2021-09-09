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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command;

use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use RuntimeException;

/**
 * Replaces previous product attachments association with the provided one.
 */
class SetAssociatedProductAttachmentsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var AttachmentId[]
     */
    private $attachmentIds;

    /**
     * @param int $productId
     * @param int[] $attachmentIds
     */
    public function __construct(int $productId, array $attachmentIds)
    {
        $this->productId = new ProductId($productId);
        $this->setAttachmentIds($attachmentIds);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return AttachmentId[]
     */
    public function getAttachmentIds(): array
    {
        return $this->attachmentIds;
    }

    /**
     * @param int[] $attachmentIds
     */
    private function setAttachmentIds(array $attachmentIds): void
    {
        if (empty($attachmentIds)) {
            throw new RuntimeException(sprintf(
                'Empty array of product attachments provided in %s. To remove all product attachments use %s.',
                self::class,
                RemoveAllAssociatedProductAttachmentsCommand::class
            ));
        }

        foreach ($attachmentIds as $attachmentId) {
            $this->attachmentIds[] = new AttachmentId($attachmentId);
        }
    }
}
