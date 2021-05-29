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

use Behat\Gherkin\Node\TableNode;
use ImageType;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\AddImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\BulkDeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\DeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Command\EditImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageType\Query\GetImageTypeForEditing;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ImageTypeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new image type :imageTypeReference with following properties:
     *
     * @param string $imageTypeReference
     * @param TableNode $table
     */
    public function createImageType(string $imageTypeReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            $imageTypeId = $this->getCommandBus()->handle(
                new AddImageTypeCommand(
                    $data['name'],
                    PrimitiveUtils::castStringIntegerIntoInteger($data['width']),
                    PrimitiveUtils::castStringIntegerIntoInteger($data['height']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['products_enabled']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['categories_enabled']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['manufacturers_enabled']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['suppliers_enabled']),
                    PrimitiveUtils::castStringBooleanIntoBoolean($data['stores_enabled'])
                )
            );
            $this->getSharedStorage()->set($imageTypeReference, new ImageType($imageTypeId->getValue()));
        } catch (ImageTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit image type :imageTypeReference with following properties:
     *
     * @param string $imageTypeReference
     * @param TableNode $table
     */
    public function editImageType(string $imageTypeReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        /** @var ImageType $imageType */
        $imageType = $this->getSharedStorage()->get($imageTypeReference);

        $imageTypeId = (int) $imageType->id;
        $command = new EditImageTypeCommand($imageTypeId);

        if (isset($data['name'])) {
            $command->setName($data['name']);
        }

        if (isset($data['width'])) {
            $command->setWidth(PrimitiveUtils::castStringIntegerIntoInteger($data['width']));
        }

        if (isset($data['height'])) {
            $command->setHeight(PrimitiveUtils::castStringIntegerIntoInteger($data['height']));
        }

        if (isset($data['products_enabled'])) {
            $command->setProductsEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['products_enabled']));
        }

        if (isset($data['categories_enabled'])) {
            $command->setCategoriesEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['categories_enabled']));
        }

        if (isset($data['manufacturers_enabled'])) {
            $command->setManufacturersEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['manufacturers_enabled']));
        }

        if (isset($data['suppliers_enabled'])) {
            $command->setSuppliersEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['suppliers_enabled']));
        }

        if (isset($data['stores_enabled'])) {
            $command->setStoresEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['stores_enabled']));
        }

        $this->getCommandBus()->handle($command);

        $this->getSharedStorage()->set($imageTypeReference, new ImageType($imageTypeId));
    }

    /**
     * @When I delete image type :imageTypeReference
     *
     * @param string $imageTypeReference
     */
    public function deleteImageType(string $imageTypeReference): void
    {
        /** @var ImageType $imageType */
        $imageType = $this->getSharedStorage()->get($imageTypeReference);

        $this->getCommandBus()->handle(new DeleteImageTypeCommand((int) $imageType->id));
    }

    /**
     * @When I delete image types: :imageTypeReferences using bulk action
     *
     * @param string $imageTypeReferences
     */
    public function bulkDeleteImageTypes(string $imageTypeReferences): void
    {
        $imageTypeIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($imageTypeReferences) as $imageTypeReference) {
            $imageTypeIds[] = (int) $this->getSharedStorage()->get($imageTypeReference)->id;
        }

        $this->getCommandBus()->handle(new BulkDeleteImageTypeCommand($imageTypeIds));
    }

    /**
     * @Then image type :imageTypeReference name should be :name
     *
     * @param string $imageTypeReference
     * @param string $name
     */
    public function assertImageTypeName(string $imageTypeReference, string $name): void
    {
        $imageType = $this->getSharedStorage()->get($imageTypeReference);

        if ($imageType->name !== $name) {
            throw new RuntimeException(
                sprintf(
                    'Image type "%s" has "%s" name, but "%s" was expected.',
                    $imageTypeReference,
                    $imageType->name,
                    $name
                )
            );
        }
    }

    /**
     * @Then /^image type "(.*)" (width|height)? should be "(.*)" pixels$/
     *
     * @param string $imageTypeReference
     * @param string $lengthType
     * @param string $expectedLength
     */
    public function assertImageTypeLength(
        string $imageTypeReference,
        string $lengthType,
        string $expectedLength
    ): void {
        /** @var ImageType $imageType */
        $imageType = $this->getSharedStorage()->get($imageTypeReference);

        if ((int) $imageType->$lengthType !== ($length = PrimitiveUtils::castStringIntegerIntoInteger($expectedLength))) {
            throw new RuntimeException(
                sprintf(
                    'Image type "%s" has %s value %d pixels, but %d was expected.',
                    $imageTypeReference,
                    $lengthType,
                    $length,
                    $expectedLength
                )
            );
        }
    }

    /**
     * @Then /^image type "(.*)" (products|categories|manufacturers|suppliers|stores)? status should be (enabled|disabled)?$/
     *
     * @param string $imageTypeReference
     * @param string $statusType
     * @param string $expectedStatus
     */
    public function assertImageTypeStatusType(
        string $imageTypeReference,
        string $statusType,
        string $expectedStatus
    ): void {
        /** @var ImageType $imageType */
        $imageType = $this->getSharedStorage()->get($imageTypeReference);

        $isEnabled = $expectedStatus === 'enabled';
        $actualStatus = (bool) $imageType->$statusType;

        if ($actualStatus !== $isEnabled) {
            throw new RuntimeException(
                sprintf(
                    'Image type "%s" %s field is %s, but it was expected to be %s.',
                    $imageTypeReference,
                    $statusType,
                    $actualStatus ? 'enabled' : 'disabled',
                    $expectedStatus
                )
            );
        }
    }

    /**
     * @Then image type :imageTypeReference should be deleted
     *
     * @param string $imageTypeReference
     */
    public function assertImageTypeIsDeleted(string $imageTypeReference): void
    {
        /** @var ImageType $imageType */
        $imageType = $this->getSharedStorage()->get($imageTypeReference);

        try {
            $query = new GetImageTypeForEditing((int) $imageType->id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(
                sprintf(
                    'Image type "%s" exists, but it was expected to be deleted',
                    $imageTypeReference
                )
            );
        } catch (ImageTypeNotFoundException $e) {
            $this->getSharedStorage()->clear($imageTypeReference);
        }
    }

    /**
     * @Then image types :imageTypeReferences should be deleted
     *
     * @param string $imageTypeReferences
     */
    public function assertImageTypesAreDeleted(string $imageTypeReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($imageTypeReferences) as $imageTypeReference) {
            $this->assertImageTypeIsDeleted($imageTypeReference);
        }
    }
}
