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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use Attachment;
use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

/**
 * Provides method to update Product-Attachment association
 */
class ProductAttachmentUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var AttachmentRepository
     */
    private $attachmentRepository;

    /**
     * @param ProductRepository $productRepository
     * @param AttachmentRepository $attachmentRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        AttachmentRepository $attachmentRepository
    ) {
        $this->productRepository = $productRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * @param ProductId $productId
     * @param AttachmentId $attachmentId
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function associateProductAttachment(ProductId $productId, AttachmentId $attachmentId): void
    {
        $this->productRepository->assertProductExists($productId);
        $this->attachmentRepository->assertAttachmentExists($attachmentId);

        $productIdValue = $productId->getValue();
        $attachmentIdValue = $attachmentId->getValue();

        try {
            if (!Attachment::associateProductAttachment($productIdValue, $attachmentIdValue)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to associate attachment #%d with product #%d', $attachmentIdValue, $productIdValue),
                    CannotUpdateProductException::FAILED_UPDATE_ATTACHMENTS
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to associate attachment #%d with product #%d', $attachmentIdValue, $productIdValue),
                0,
                $e
            );
        }
    }

    /**
     * Removes previous association and sets new one with provided attachments
     *
     * @param ProductId $productId
     * @param AttachmentId[] $attachmentIds
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    public function setAttachments(ProductId $productId, array $attachmentIds): void
    {
        $this->productRepository->assertProductExists($productId);
        $productIdValue = $productId->getValue();
        $attachmentIdValues = [];

        try {
            foreach ($attachmentIds as $attachmentId) {
                $this->attachmentRepository->assertAttachmentExists($attachmentId);
                $attachmentIdValues[] = $attachmentId->getValue();
            }

            if (!Attachment::attachToProduct($productIdValue, $attachmentIdValues)) {
                throw new CannotUpdateProductException(
                    sprintf('Failed to set product #%d attachments', $productIdValue),
                    CannotUpdateProductException::FAILED_UPDATE_ATTACHMENTS
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to set product #%d attachments',
                    $productIdValue
                ),
                0,
                $e
            );
        }
    }
}
