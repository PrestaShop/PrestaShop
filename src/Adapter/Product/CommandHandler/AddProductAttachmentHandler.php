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
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\CommandHandler\AddAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\AddProductAttachmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShopException;

/**
 * Handles @see AddProductAttachmentCommand using legacy object model
 */
final class AddProductAttachmentHandler extends AbstractProductHandler implements AddProductAttachmentHandlerInterface
{
    /**
     * @var AddAttachmentHandlerInterface
     */
    private $addAttachmentHandler;

    /**
     * @param AddAttachmentHandlerInterface $addAttachmentHandler
     */
    public function __construct(AddAttachmentHandlerInterface $addAttachmentHandler)
    {
        $this->addAttachmentHandler = $addAttachmentHandler;
    }

    /**
     * @param AddProductAttachmentCommand $command
     *
     * @return AttachmentId
     */
    public function handle(AddProductAttachmentCommand $command): AttachmentId
    {
        $product = $this->getProduct($command->getProductId());

        $addAttachmentCommand = new AddAttachmentCommand(
            $command->getLocalizedNames(),
            $command->getLocalizedDescriptions()
        );
        $addAttachmentCommand->setFileInformation(
            $command->getFilePathName(),
            $command->getFileSize(),
            $command->getMimeType(),
            $command->getOriginalName()
        );

        $attachmentId = $this->addAttachmentHandler->handle($addAttachmentCommand);

        $this->associateAttachmentWithProduct($attachmentId->getValue(), (int) $product->id);

        return $attachmentId;
    }

    /**
     * @param int $attachmentId
     * @param int $productId
     *
     * @throws CannotUpdateProductException
     * @throws ProductException
     */
    private function associateAttachmentWithProduct(int $attachmentId, int $productId): void
    {
        try {
            $attachment = new Attachment($attachmentId);

            if (!$attachment->attachProduct($productId)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to associate attachment #%d with product #%d', $attachmentId, $productId),
                    CannotUpdateProductException::FAILED_UPDATE_ATTACHMENTS
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductException(
                sprintf('Error occurred when trying to associate attachment #%d with product #%d', $attachmentId, $productId),
                0,
                $e
            );
        }
    }
}
