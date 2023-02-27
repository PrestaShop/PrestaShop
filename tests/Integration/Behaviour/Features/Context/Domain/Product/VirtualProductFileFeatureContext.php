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
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\DeleteVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;
use RuntimeException;
use Tests\Resources\DummyFileUploader;

class VirtualProductFileFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then product :productReference should not have a file
     *
     * @param string $productReference
     */
    public function assertProductHasNoFile(string $productReference): void
    {
        $virtualProductFile = $this->getProductForEditing($productReference)->getVirtualProductFile();

        Assert::assertEquals(null, $virtualProductFile, 'Expected no virtual product file');
    }

    /**
     * @When I add virtual product file :fileReference to product :productReference with following details:
     *
     * @param string $fileReference
     * @param string $productReference
     * @param TableNode $dataTable
     */
    public function addFile(string $fileReference, string $productReference, TableNode $dataTable): void
    {
        $dataRows = $dataTable->getRowsHash();
        $filePath = DummyFileUploader::upload($dataRows['file name']);
        $command = new AddVirtualProductFileCommand(
            $this->getSharedStorage()->get($productReference),
            $filePath,
            $dataRows['display name'],
            isset($dataRows['access days']) ? (int) $dataRows['access days'] : null,
            isset($dataRows['download times limit']) ? (int) $dataRows['download times limit'] : null,
            isset($dataRows['expiration date']) ? new DateTime($dataRows['expiration date']) : null
        );

        try {
            $virtualProductId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($fileReference, $virtualProductId->getValue());

            // save Downloads filePath in shared storage for further assertions
            $filename = $this->getProductForEditing($productReference)->getVirtualProductFile()->getFileName();
            $this->getSharedStorage()->set(
                $this->buildSystemFileReference($productReference, $fileReference),
                _PS_DOWNLOAD_DIR_ . $filename
            );
        } catch (VirtualProductFileException|InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update file ":fileReference" with following details:
     *
     * @param string $fileReference
     * @param TableNode $tableNode
     */
    public function updateFile(string $fileReference, TableNode $tableNode): void
    {
        $command = $this->buildUpdateVirtualProductFileCommand(
            $this->getSharedStorage()->get($fileReference),
            $tableNode->getRowsHash(),
            null
        );

        try {
            $this->getCommandBus()->handle($command);
        } catch (VirtualProductFileConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I replace product ":productReference" file ":fileReference" with a new file ":newFileReference" named ":newFileName" and following details:
     *
     * @param string $productReference
     * @param string $fileReference
     * @param string $newFileReference
     * @param string $newFileName
     * @param TableNode $tableNode
     */
    public function replaceFileWithDetails(
        string $productReference,
        string $fileReference,
        string $newFileReference,
        string $newFileName,
        TableNode $tableNode
    ): void {
        $virtualProductFileId = $this->getSharedStorage()->get($fileReference);
        $command = $this->buildUpdateVirtualProductFileCommand(
            $virtualProductFileId,
            $tableNode->getRowsHash(),
            $newFileName
        );

        $this->getCommandBus()->handle($command);
        $uploadedFilename = $this->getProductForEditing($productReference)->getVirtualProductFile()->getFileName();

        $this->getSharedStorage()->set($newFileReference, $virtualProductFileId);
        $this->getSharedStorage()->set(
            $this->buildSystemFileReference($productReference, $newFileReference),
            _PS_DOWNLOAD_DIR_ . $uploadedFilename
        );
    }

    /**
     * @When I delete virtual product file ":fileReference"
     *
     * @param string $fileReference
     */
    public function deleteFile(string $fileReference): void
    {
        $this->getCommandBus()->handle(new DeleteVirtualProductFileCommand(
            $this->getSharedStorage()->get($fileReference)
        ));
    }

    /**
     * @Then file ":fileReference" for product ":productReference" should not exist in system
     *
     * @param string $productReference
     * @param string $fileReference
     */
    public function assertFileDoesNotExistInSystem(string $productReference, string $fileReference): void
    {
        $this->assertSystemFileExistence($productReference, $fileReference, false);
    }

    /**
     * @Then file :fileReference for product :productReference should have same file as :dummyFileName
     *
     * @param string $productReference
     * @param string $fileReference
     * @param string $dummyFileName
     */
    public function assertFileIsSameAsDummyFile(string $productReference, string $fileReference, string $dummyFileName): void
    {
        $reference = $this->buildSystemFileReference($productReference, $fileReference);
        if (!$this->getSharedStorage()->exists($reference)) {
            throw new RuntimeException('No file reference stored in shared storage');
        }

        $virtualDownloadFilePath = $this->getSharedStorage()->get($reference);

        // This was previously saved during image upload
        $dummyFilePath = DummyFileUploader::getDummyFilePath($dummyFileName);
        $dummyMD5 = md5_file($dummyFilePath);

        if ($dummyMD5 !== md5_file($virtualDownloadFilePath)) {
            throw new RuntimeException(sprintf(
                'Expected files dummy %s and file %s to be identical',
                $dummyFileName,
                $fileReference
            ));
        }
    }

    /**
     * @Given file :fileReference for product :productReference exists in system
     * @Given file :fileReference for product :productReference should exist in system
     *
     * @param string $productReference
     * @param string $fileReference
     */
    public function assertFileExistsInSystem(string $productReference, string $fileReference): void
    {
        $this->assertSystemFileExistence($productReference, $fileReference, true);
    }

    /**
     * @Then I should get error that product file :propertyName is invalid
     *
     * @param string $propertyName
     */
    public function assertLastConstraintError(string $propertyName): void
    {
        $errorCodeByPropertyMap = [
            'display name' => VirtualProductFileConstraintException::INVALID_DISPLAY_NAME,
            'access days' => VirtualProductFileConstraintException::INVALID_ACCESS_DAYS,
            'download times limit' => VirtualProductFileConstraintException::INVALID_DOWNLOAD_TIMES_LIMIT,
        ];

        if (!isset($errorCodeByPropertyMap[$propertyName])) {
            throw new RuntimeException(sprintf('Error code is not set for property "%s"', $propertyName));
        }

        $this->assertLastErrorIs(
            VirtualProductFileConstraintException::class,
            $errorCodeByPropertyMap[$propertyName]
        );
    }

    /**
     * @Then I should get error that product already has a file
     */
    public function assertProductAlreadyHasAFileError(): void
    {
        $this->assertLastErrorIs(
            VirtualProductFileConstraintException::class,
            VirtualProductFileConstraintException::ALREADY_HAS_A_FILE
        );
    }

    /**
     * @Then product :productReference should have a virtual product file :fileReference with following details:
     *
     * @param string $productReference
     * @param string $fileReference
     * @param TableNode $dataTable
     */
    public function assertFileAndReference(string $productReference, string $fileReference, TableNode $dataTable): void
    {
        $actualFile = $this->getProductForEditing($productReference)->getVirtualProductFile();
        if (!$actualFile) {
            throw new RuntimeException('Expected virtual product to have a file');
        }
        Assert::assertEquals(
            $this->getSharedStorage()->get($fileReference),
            $actualFile->getId(),
            'Unexpected virtual product file (ids do not match)'
        );
        $this->assertVirtualFile($actualFile, $dataTable);
    }

    /**
     * @Then product :productReference should have a virtual product file which reference is :fileReference and has following details:
     *
     * @param string $productReference
     * @param string $fileReference
     * @param TableNode $dataTable
     */
    public function assertNewFile(string $productReference, string $fileReference, TableNode $dataTable): void
    {
        $actualFile = $this->getProductForEditing($productReference)->getVirtualProductFile();
        if (!$actualFile) {
            throw new RuntimeException('Expected virtual product to have a file');
        }
        $this->getSharedStorage()->set($fileReference, $actualFile->getId());
        $this->assertVirtualFile($actualFile, $dataTable);

        // Set path for new reference used in other assertions
        $reference = $this->buildSystemFileReference($productReference, $fileReference);
        $this->getSharedStorage()->set($reference, _PS_DOWNLOAD_DIR_ . $actualFile->getFileName());
    }

    private function assertVirtualFile(VirtualProductFileForEditing $actualFile, TableNode $dataTable): void
    {
        $dataRows = $dataTable->getRowsHash();
        Assert::assertEquals($dataRows['display name'], $actualFile->getDisplayName(), 'Unexpected display file name');
        unset($dataRows['display name']);

        Assert::assertEquals(
            (int) $dataRows['access days'],
            $actualFile->getAccessDays(),
            'Unexpected file access days'
        );
        unset($dataRows['access days']);

        Assert::assertEquals(
            (int) $dataRows['download times limit'],
            $actualFile->getDownloadTimesLimit(), 'Unexpected file download times limit'
        );
        unset($dataRows['download times limit']);

        $this->assertExpirationDate($dataRows, $actualFile);
        unset($dataRows['expiration date']);

        if (!empty($dataRows)) {
            throw new RuntimeException(sprintf('Some values were not asserted. [%s]', var_export($dataRows, true)));
        }
    }

    /**
     * @param array $dataRows
     * @param VirtualProductFileForEditing $actualFile
     */
    private function assertExpirationDate(array $dataRows, VirtualProductFileForEditing $actualFile): void
    {
        $expectedExpiration = $dataRows['expiration date'] === '' ? null : new DateTimeImmutable($dataRows['expiration date']);
        Assert::assertEquals($expectedExpiration, $actualFile->getExpirationDate(), 'Unexpected file expiration date');
    }

    /**
     * @param string $productReference
     * @param string $fileReference
     * @param bool $expectedToExist
     */
    private function assertSystemFileExistence(string $productReference, string $fileReference, bool $expectedToExist): void
    {
        $reference = $this->buildSystemFileReference($productReference, $fileReference);

        if (!$this->getSharedStorage()->exists($reference)) {
            throw new RuntimeException('No file reference stored in shared storage');
        }

        $path = $this->getSharedStorage()->get($reference);
        $exists = file_exists($path);

        if ($expectedToExist) {
            Assert::assertTrue(
                $exists,
                sprintf('File referenced as "%s" does not exist in system (path "%s")', $reference, $path)
            );
        } else {
            Assert::assertFalse(
                $exists,
                sprintf('File referenced as "%s" exists in system (path "%s")', $reference, $path)
            );
        }
    }

    /**
     * System file name is generated, so we want to save it in shared storage after upload to assert later
     *
     * @param string $productReference
     * @param string $fileReference
     *
     * @return string
     */
    private function buildSystemFileReference(string $productReference, string $fileReference): string
    {
        return sprintf('%s-%s', $productReference, $fileReference);
    }

    /**
     * @param int $virtualProductFileId
     * @param array<string, string> $data
     * @param string|null $newFileName provide new file name to replace old file with new one from dummy files
     *
     * @return UpdateVirtualProductFileCommand
     *
     * @throws \Exception
     */
    private function buildUpdateVirtualProductFileCommand(int $virtualProductFileId, array $data, ?string $newFileName): UpdateVirtualProductFileCommand
    {
        $command = new UpdateVirtualProductFileCommand($virtualProductFileId);

        if (isset($data['display name'])) {
            $command->setDisplayName($data['display name']);
            unset($data['display name']);
        }
        if (isset($data['access days'])) {
            $command->setAccessDays((int) $data['access days']);
            unset($data['access days']);
        }
        if (isset($data['download times limit'])) {
            $command->setDownloadTimesLimit((int) $data['download times limit']);
            unset($data['download times limit']);
        }
        if (isset($data['expiration date'])) {
            $command->setExpirationDate(new DateTimeImmutable($data['expiration date']));
            unset($data['expiration date']);
        }

        Assert::assertEmpty(
            $data,
            sprintf('Not all provided fields were handled during virtual product file testing. [%s]', var_export($data, true))
        );

        if (isset($newFileName)) {
            $newFilePath = DummyFileUploader::upload($newFileName);
            $command->setFilePath($newFilePath);
        }

        return $command;
    }
}
