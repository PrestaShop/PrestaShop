<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\RemoveAllAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\Command\SetAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class ProductAttachmentsCommandsBuilder implements ProductCommandsBuilderInterface
{
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['details']['attachments']['attached_files'])) {
            return [];
        }

        $attachedFiles = $formData['details']['attachments']['attached_files'];

        if (empty($attachedFiles)) {
            return [new RemoveAllAssociatedProductAttachmentsCommand($productId->getValue())];
        }

        $attachmentIds = [];

        foreach ($attachedFiles as $attachedFile) {
            $attachmentId = (int) $attachedFile['attachment_id'];
            // Just avoid duplicate IDs from the form
            if (!in_array($attachmentId, $attachmentIds)) {
                $attachmentIds[] = $attachmentId;
            }
        }

        return [new SetAssociatedProductAttachmentsCommand($productId->getValue(), $attachmentIds)];
    }
}
