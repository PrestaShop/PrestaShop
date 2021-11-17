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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AssociateProductAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductAttachmentsCommand;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Transform\StringToArrayTransformContext;

class UpdateAttachmentFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I associate attachment :attachmentReference with product :productReference
     *
     * @param string $attachmentReference
     * @param string $productReference
     */
    public function associateProductAttachment(string $attachmentReference, string $productReference): void
    {
        $this->getCommandBus()->handle(new AssociateProductAttachmentCommand(
            $this->getSharedStorage()->get($productReference),
            $this->getSharedStorage()->get($attachmentReference)
        ));
    }

    /**
     * @Then product :productReference should have following attachments associated: :attachmentReferences
     *
     * attachmentReferences transformation handled by @see StringToArrayTransformContext
     *
     * @param string $productReference
     * @param string[] $attachmentReferences
     */
    public function assertProductAttachments(string $productReference, array $attachmentReferences): void
    {
        $attachmentIds = $this->getProductForEditing($productReference)->getAssociatedAttachmentIds();

        Assert::assertEquals(
            count($attachmentIds),
            count($attachmentReferences),
            'Unexpected associated product attachments count'
        );

        foreach ($attachmentReferences as $key => $expectedReference) {
            if ($attachmentIds[$key] === $this->getSharedStorage()->get($expectedReference)) {
                continue;
            }

            throw new RuntimeException(sprintf('Unexpected associated product attachments'));
        }
    }

    /**
     * @Then product :productReference should have no attachments associated
     *
     * @param string $productReference
     */
    public function assertProductHasNoAttachmentsAssociated(string $productReference)
    {
        Assert::assertEmpty(
            $this->getProductForEditing($productReference)->getAssociatedAttachmentIds(),
            'Product "%s" expected to have no attachments associated'
        );
    }

    /**
     * @When I associate product :productReference with following attachments: :attachmentReferences
     *
     * attachmentReferences transformation handled by @see StringToArrayTransformContext
     *
     * @param string $productReference
     * @param string[] $attachmentReferences
     */
    public function setAssociatedProductAttachments(string $productReference, array $attachmentReferences): void
    {
        $attachmentIds = [];

        foreach ($attachmentReferences as $attachmentReference) {
            $attachmentIds[] = $this->getSharedStorage()->get($attachmentReference);
        }

        $this->getCommandBus()->handle(new SetAssociatedProductAttachmentsCommand(
            $this->getSharedStorage()->get($productReference),
            $attachmentIds
        ));
    }

    /**
     * @When I remove product :productReference attachments association
     *
     * @param string $productReference
     */
    public function removeProductAttachmentsAssociation(string $productReference)
    {
        $this->getCommandBus()->handle(new RemoveAllAssociatedProductAttachmentsCommand(
            $this->getSharedStorage()->get($productReference)
        ));
    }
}
