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

namespace PrestaShop\PrestaShop\Adapter\Product;

use Attachment;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelPersister;
use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentProvider;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;

/**
 * Performs update of provided Product properties
 */
class ProductUpdater extends AbstractObjectModelPersister
{
    /**
     * @var ProductValidator
     */
    private $productValidator;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var AttachmentProvider
     */
    private $attachmentProvider;

    /**
     * @param ProductValidator $productValidator
     * @param ProductProvider $productProvider
     * @param AttachmentProvider $attachmentProvider
     */
    public function __construct(
        ProductValidator $productValidator,
        ProductProvider $productProvider,
        AttachmentProvider $attachmentProvider
    ) {
        $this->productValidator = $productValidator;
        $this->productProvider = $productProvider;
        $this->attachmentProvider = $attachmentProvider;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function update(Product $product, array $propertiesToUpdate, int $errorCode): void
    {
        $this->fillProperties($product, $propertiesToUpdate);
        $this->productValidator->validate($product);
        $this->updateObjectModel($product, CannotUpdateProductException::class, $errorCode);
    }

    /**
     * @param Product $product
     */
    public function refreshProductCustomizabilityProperties(Product $product): void
    {
        if ($product->hasActivatedRequiredCustomizableFields()) {
            $customizable = ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION;
        } elseif (!empty($product->getNonDeletedCustomizationFieldIds())) {
            $customizable = ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION;
        } else {
            $customizable = ProductCustomizabilitySettings::NOT_CUSTOMIZABLE;
        }

        $this->update(
            $product,
            [
                'customizable' => $customizable,
                'text_fields' => $product->countCustomizationFields(CustomizationFieldType::TYPE_TEXT),
                'uploadable_files' => $product->countCustomizationFields(CustomizationFieldType::TYPE_FILE),
            ], CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS
        );
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
        $this->productProvider->assertProductExists($productId);
        $this->attachmentProvider->assertAttachmentExists($attachmentId);

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
     * @throws ProductException
     */
    public function setAttachments(ProductId $productId, array $attachmentIds): void
    {
        $this->productProvider->assertProductExists($productId);
        $productIdValue = $productId->getValue();

        try {
            foreach ($attachmentIds as $attachmentId) {
                $this->attachmentProvider->assertAttachmentExists($attachmentId);
                $attachmentIdValues[] = $attachmentId->getValue();
            }

            if (!Attachment::attachToProduct($productIdValue, $attachmentIds)) {
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

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     */
    private function fillProperties(Product $product, array $propertiesToUpdate): void
    {
        $this->fillProperty($product, 'customizable', $propertiesToUpdate);
        $this->fillProperty($product, 'text_fields', $propertiesToUpdate);
        $this->fillProperty($product, 'uploadable_files', $propertiesToUpdate);
        //@todo; more properties when refactoring other handlers to use updater/validator
    }
}
