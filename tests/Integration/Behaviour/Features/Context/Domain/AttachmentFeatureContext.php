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
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptySearchException;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Query\SearchAttachment;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;
use PrestaShopException;
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
        $fileName = $data['file_name'];

        $destination = $this->uploadDummyFile($fileName);

        $attachment = new Attachment();
        $attachment->description = $data['description'];
        $attachment->name = $data['name'];
        $attachment->file_name = $fileName;
        $attachment->mime = mime_content_type($destination);
        $attachment->file = pathinfo($destination, PATHINFO_BASENAME);

        $attachment->add();

        $this->getSharedStorage()->set($reference, (int) $attachment->id);
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

        Assert::assertEquals($data['description'], $attachment->description);
        Assert::assertEquals($data['name'], $attachment->name);
        Assert::assertEquals($data['file_name'], $attachment->file_name);
        Assert::assertEquals($data['mime'], $attachment->mime);
        Assert::assertEquals($data['size'], $attachment->file_size);
    }

    /**
     * @When I search for attachment matching :searchPhrase with language :languageReference I get following results:
     *
     * @param string $searchPhrase
     * @param string $languageReference
     */
    public function searchAttachment(string $searchPhrase, string $languageReference, TableNode $tableNode): void
    {
        /** @var Language $language */
        $language = $this->getSharedStorage()->get($languageReference);

        /** @var AttachmentInformation[] $foundAttachments */
        $foundAttachments = $this->getCommandBus()->handle(new SearchAttachment($searchPhrase, (int) $language->id));
        $expectedAttachments = $tableNode->getColumnsHash();

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

            Assert::assertEquals($expectedAttachment['name'], $matchingAttachment->getName());
            Assert::assertEquals($expectedAttachment['mime'], $matchingAttachment->getType());
            Assert::assertEquals($expectedAttachment['file_name'], $matchingAttachment->getFileName());
        }
    }

    /**
     * @When I search for attachment matching :searchPhrase with language :languageReference I get no results
     *
     * @param string $searchPhrase
     * @param string $languageReference
     */
    public function searchAttachmentFails(string $searchPhrase, string $languageReference): void
    {
        /** @var Language $language */
        $language = $this->getSharedStorage()->get($languageReference);

        $caughtException = null;
        try {
            $this->getCommandBus()->handle(new SearchAttachment($searchPhrase, (int) $language->id));
        } catch (EmptySearchException $e) {
            $caughtException = $e;
        }
        Assert::assertNotNull($caughtException, 'Expected to get no results for this search');
    }

    /**
     * @param string $reference
     *
     * @return Attachment
     *
     * @throws PrestaShopException
     */
    private function getAttachment(string $reference): Attachment
    {
        $id = $this->getSharedStorage()->get($reference);
        $attachment = new Attachment($id);

        if (!$attachment->id) {
            throw new RuntimeException(sprintf('Failed to load attachment with id %d', $id));
        }

        return $attachment;
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
