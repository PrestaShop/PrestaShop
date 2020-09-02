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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Attachment;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\AttachmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\SetAssociatedProductAttachmentsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopException;

/**
 * Handles @see SetAssociatedProductAttachmentsCommand using legacy object model
 */
final class SetAssociatedProductAttachmentsHandler extends AbstractProductHandler implements SetAssociatedProductAttachmentsHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(SetAssociatedProductAttachmentsCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());
        $attachmentIdValues = [];

        foreach ($command->getAttachmentIds() as $attachmentId) {
            $this->assertAttachmentExists($attachmentId->getValue());
            $attachmentIdValues[] = $attachmentId->getValue();
        }

        $this->associateProductAttachments((int) $product->id, $attachmentIdValues);
    }

    /**
     * @param int $attachmentIdValue
     *
     * @throws AttachmentNotFoundException
     */
    private function assertAttachmentExists(int $attachmentIdValue): void
    {
        if (!Attachment::existsInDatabase($attachmentIdValue, 'attachment')) {
            throw new AttachmentNotFoundException(sprintf(
                'Attachment with id %d does not exist',
                $attachmentIdValue
            ));
        }
    }

    /**
     * @param int $productId
     * @param array $attachmentIdValues
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function associateProductAttachments(int $productId, array $attachmentIdValues): void
    {
        try {
            if (!Attachment::attachToProduct($productId, $attachmentIdValues)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to set product #%d attachments', $productId),
                    CannotUpdateProductException::FAILED_UPDATE_ATTACHMENTS
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf(
                    'Error occurred when trying to set product #%d attachments',
                    $productId
                ),
                0,
                $e
            );
        }
    }
}
