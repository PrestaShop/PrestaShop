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

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AssociateProductAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductAttachmentsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductAttachmentsCommand;
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
     * @Then product :productReference should have following attachments associated:
     *
     * @param string $productReference
     * @param AttachmentInfo[] $attachmentsInfo
     *
     * @see transformAttachmentsInfo()
     */
    public function assertProductAttachments(string $productReference, array $attachmentsInfo): void
    {
        $attachments = $this->getProductForEditing($productReference)->getAssociatedAttachments();

        Assert::assertEquals(
            count($attachments),
            count($attachmentsInfo),
            'Unexpected associated product attachments count'
        );

        foreach ($attachmentsInfo as $key => $expectedAttachmentInfo) {
            Assert::assertEquals(
                $expectedAttachmentInfo,
                $attachments[$key],
                'Unexpected associated product attachments'
            );
        }
    }

    /**
     * @Transform table:attachment reference,title,file name,type
     *
     * @param TableNode $tableNode
     *
     * @return AttachmentInfo[]
     */
    public function transformAttachmentsInfo(TableNode $tableNode): array
    {
        $infos = $tableNode->getColumnsHash();

        $attachmentsInfo = [];
        foreach ($infos as $info) {
            $attachmentsInfo[] = new AttachmentInfo(
                $this->getSharedStorage()->get($info['attachment reference']),
                $this->localizeByCell($info['title']),
                $info['file name'],
                $info['type']
            );
        }

        return $attachmentsInfo;
    }

    /**
     * @Then product :productReference should have no attachments associated
     *
     * @param string $productReference
     */
    public function assertProductHasNoAttachmentsAssociated(string $productReference)
    {
        Assert::assertEmpty(
            $this->getProductForEditing($productReference)->getAssociatedAttachments(),
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
