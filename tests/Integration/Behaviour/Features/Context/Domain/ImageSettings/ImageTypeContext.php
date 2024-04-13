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

namespace Tests\Integration\Behaviour\Features\Context\Domain\ImageSettings;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\AddImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\BulkDeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\DeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageType;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ImageTypeContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create an image type :imageTypeName with following properties:
     */
    public function createImageTypeUsingCommand(string $imageTypeName, TableNode $table)
    {
        $data = $this->fixDataType($table->getRowsHash());

        $command = new AddImageTypeCommand(
            $imageTypeName,
            $data['width'],
            $data['height'],
            $data['products'],
            $data['categories'],
            $data['manufacturers'],
            $data['suppliers'],
            $data['stores']
        );

        /** @var ImageTypeId $imageTypeId */
        $imageTypeId = $this->getCommandBus()->handle($command);

        $this->getSharedStorage()->set($imageTypeName, $imageTypeId->getValue());
    }

    /**
     * @When I edit image type :imageTypeName with following properties:
     */
    public function editImageTypeUsingCommand(string $imageTypeName, TableNode $table)
    {
        $data = $this->fixDataType($table->getRowsHash());

        $command = new EditImageTypeCommand($this->getSharedStorage()->get($imageTypeName));
        $command->setName($imageTypeName);

        if (isset($data['width'])) {
            $command->setWidth($data['width']);
        }

        if (isset($data['height'])) {
            $command->setHeight($data['height']);
        }

        if (isset($data['products'])) {
            $command->setProducts($data['products']);
        }

        if (isset($data['categories'])) {
            $command->setCategories($data['categories']);
        }

        if (isset($data['manufacturers'])) {
            $command->setManufacturers($data['manufacturers']);
        }

        if (isset($data['suppliers'])) {
            $command->setSuppliers($data['suppliers']);
        }

        if (isset($data['stores'])) {
            $command->setStores($data['stores']);
        }

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I delete image type :imageTypeName.
     */
    public function deleteImageTypeUsingCommand(string $imageTypeName)
    {
        $command = new DeleteImageTypeCommand($this->getSharedStorage()->get($imageTypeName));
        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then I bulk delete image types :imageTypesName.
     */
    public function bulkDeleteImageTypeUsingCommand(string $imageTypesName)
    {
        $imageTypesName = explode(',', $imageTypesName);
        $imageTypesIds = [];
        foreach ($imageTypesName as $imageTypeName) {
            $imageTypesIds[] = $this->getSharedStorage()->get($imageTypeName);
        }
        $command = new BulkDeleteImageTypeCommand($imageTypesIds);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then image type :imageTypeName should have the following properties:
     */
    public function assertQueryImageTypeProperties(string $imageTypeName, TableNode $table)
    {
        $errors = [];
        $expectedData = $table->getRowsHash();

        /** @var EditableImageType $imageType */
        $imageType = $this->getQueryBus()->handle(new GetImageTypeForEditing($this->getSharedStorage()->get($imageTypeName)));

        if (isset($expectedData['name'])) {
            if ($imageType->getName() !== $imageTypeName) {
                $errors[] = 'name';
            }
        }

        if (isset($expectedData['width'])) {
            if ($imageType->getWidth() != $expectedData['width']) {
                $errors[] = 'width';
            }
        }

        if (isset($expectedData['height'])) {
            if ($imageType->getHeight() != $expectedData['height']) {
                $errors[] = 'height';
            }
        }

        if (isset($expectedData['products'])) {
            if ($imageType->isProducts() !== filter_var($expectedData['products'], FILTER_VALIDATE_BOOL)) {
                $errors[] = 'products';
            }
        }

        if (isset($expectedData['categories'])) {
            if ($imageType->isCategories() !== filter_var($expectedData['categories'], FILTER_VALIDATE_BOOL)) {
                $errors[] = 'categories';
            }
        }

        if (isset($expectedData['manufacturers'])) {
            if ($imageType->isManufacturers() !== filter_var($expectedData['manufacturers'], FILTER_VALIDATE_BOOL)) {
                $errors[] = 'manufacturers';
            }
        }

        if (isset($expectedData['suppliers'])) {
            if ($imageType->isSuppliers() !== filter_var($expectedData['suppliers'], FILTER_VALIDATE_BOOL)) {
                $errors[] = 'suppliers';
            }
        }

        if (isset($expectedData['stores'])) {
            if ($imageType->isStores() !== filter_var($expectedData['stores'], FILTER_VALIDATE_BOOL)) {
                $errors[] = 'stores';
            }
        }

        if (count($errors) > 0) {
            throw new \RuntimeException(sprintf('Fields %s are not identical', implode(', ', $errors)));
        }
    }

    /**
     * @When image type :imageTypeName should not exist.
     */
    public function assertImageTypeDoesNotExist(string $imageTypeName)
    {
        try {
            $this->getQueryBus()->handle(new GetImageTypeForEditing($this->getSharedStorage()->get($imageTypeName)));
            throw new \RuntimeException(sprintf('Image type %s still exists', $imageTypeName));
        } catch (ImageTypeNotFoundException $ex) {
            return;
        }
    }

    /**
     * Fix data properties.
     */
    private function fixDataType(array $data): array
    {
        // Cast to int
        foreach (['width', 'height'] as $key) {
            if (array_key_exists($key, $data) && !is_null($data[$key])) {
                $data[$key] = intval($data[$key]);
            }
        }

        // Cast to boolean
        foreach (['products', 'categories', 'manufacturers', 'suppliers', 'stores'] as $key) {
            if (array_key_exists($key, $data) && !is_null($data[$key])) {
                $data[$key] = PrimitiveUtils::castStringBooleanIntoBoolean($data[$key]);
            }
        }

        return $data;
    }
}
