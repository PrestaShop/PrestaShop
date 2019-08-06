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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Associates products with attachments.
 */
class UpdateProductAttachmentsAssociationCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var array
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
     * @return array
     */
    public function getAttachmentIds(): array
    {
        return $this->attachmentIds;
    }

    private function setAttachmentIds(array $attachmentIds): void
    {
        foreach ($attachmentIds as $attachmentId) {
            //todo: change me when AttachmentId VO is available
            $this->attachmentIds[] = new class($attachmentId) {
                private $attachmentId;

                public function __construct(int $attachmentId)
                {
                    $this->attachmentId;
                }

                public function getValue()
                {
                    return $this->attachmentId;
                }
            };
        }
    }
}
