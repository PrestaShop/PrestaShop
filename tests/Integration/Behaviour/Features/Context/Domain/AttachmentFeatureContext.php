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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Attachment;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptySearchException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\GetAttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\SearchAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use RuntimeException;
use Tests\Resources\DummyFileUploader;

class AttachmentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new attachment :reference with following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function addAttachment(string $reference, TableNode $tableNode): void
    {
        $data = $this->localizeByRows($tableNode);
        $addCommand = new AddAttachmentCommand(
            $data['name'],
            $data['description']
        );
        $destination = $this->uploadDummyFile($data['file_name']);
        $fileSize = filesize($destination);
        $addCommand->setFileInformation(
            pathinfo($destination, PATHINFO_BASENAME),
            $fileSize,
            mime_content_type($destination),
            $data['file_name']
        );

        /** @var AttachmentId $attachmentId */
        $attachmentId = $this->getCommandBus()->handle($addCommand);
        $this->getSharedStorage()->set($reference, $attachmentId->getValue());
    }

    /**
     * @Then attachment :reference should have following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function assertAttachmentProperties(string $reference, TableNode $tableNode): void
    {
        $attachment = $this->getAttachment($reference);
        $data = $this->localizeByRows($tableNode);

        Assert::assertEquals($data['name'], $attachment->getLocalizedNames());
        Assert::assertEquals($data['description'], $attachment->getLocalizedDescriptions());
        Assert::assertEquals($data['file_name'], $attachment->getFileName());
        Assert::assertEquals($data['mime'], $attachment->getMimeType());
        Assert::assertEquals((int) $data['size'], $attachment->getFileSize());
    }

    /**
     * @When I search for attachment matching :searchPhrase I get following results:
     *
     * @param string $searchPhrase
     */
    public function searchAttachment(string $searchPhrase, TableNode $tableNode): void
    {
        /** @var AttachmentInformation[] $foundAttachments */
        $foundAttachments = $this->getCommandBus()->handle(new SearchAttachment($searchPhrase));
        $expectedAttachments = $this->localizeByColumns($tableNode);

        Assert::assertEquals(count($expectedAttachments), count($foundAttachments));
        foreach ($expectedAttachments as $expectedAttachment) {
            $expectedAttachmentId = (int) $this->getSharedStorage()->get($expectedAttachment['attachment_id']);
            $matchingAttachment = null;
            foreach ($foundAttachments as $foundAttachment) {
                if ($foundAttachment->getAttachmentId() === $expectedAttachmentId) {
                    $matchingAttachment = $foundAttachment;
                    break;
                }
            }
            if (null === $matchingAttachment) {
                throw new RuntimeException(sprintf('Could not find expected attachment %s', $expectedAttachment['attachment_id']));
            }

            $attachmentNames = $matchingAttachment->getLocalizedNames();
            foreach ($expectedAttachment['name'] as $langId => $name) {
                Assert::assertTrue(isset($attachmentNames[$langId]));
                Assert::assertEquals($name, $attachmentNames[$langId]);
            }
            $attachmentDescriptions = $matchingAttachment->getLocalizedDescriptions();
            foreach ($expectedAttachment['description'] as $langId => $description) {
                Assert::assertTrue(isset($attachmentDescriptions[$langId]));
                Assert::assertEquals($description, $attachmentDescriptions[$langId]);
            }

            Assert::assertEquals($expectedAttachment['mime'], $matchingAttachment->getMimeType());
            Assert::assertEquals($expectedAttachment['file_name'], $matchingAttachment->getFileName());
            Assert::assertEquals((int) $expectedAttachment['size'], $matchingAttachment->getFileSize());
        }
    }

    /**
     * @When I delete attachment :attachmentReference
     *
     * @param string $attachmentReference
     */
    public function deleteAttachment(string $attachmentReference): void
    {
        $this->getCommandBus()->handle(
            new DeleteAttachmentCommand((int) $this->getSharedStorage()->get($attachmentReference))
        );
    }

    /**
     * @When I search for attachment matching :searchPhrase I get no results
     *
     * @param string $searchPhrase
     */
    public function searchAttachmentFails(string $searchPhrase): void
    {
        $caughtException = null;
        try {
            $this->getCommandBus()->handle(new SearchAttachment($searchPhrase));
        } catch (EmptySearchException $e) {
            $caughtException = $e;
        }
        Assert::assertNotNull($caughtException, 'Expected to get no results for this search');
    }

    /**
     * @param string $reference
     *
     * @return AttachmentInformation
     */
    private function getAttachment(string $reference): AttachmentInformation
    {
        $id = $this->getSharedStorage()->get($reference);

        return $this->getCommandBus()->handle(new GetAttachmentInformation((int) $id));
    }

    /**
     * @param string $fileName
     *
     * @return string uploaded file destination path including the file name
     */
    private function uploadDummyFile(string $fileName): string
    {
        $file = DummyFileUploader::upload($fileName);

        $destination = _PS_DOWNLOAD_DIR_ . $fileName;
        copy($file, $destination);

        return $destination;
    }
}
