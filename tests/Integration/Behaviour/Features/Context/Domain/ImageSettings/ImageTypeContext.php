<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use RuntimeException;
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
            if ($imageType->getWidth() != (int) $expectedData['width']) {
                $errors[] = 'width';
            }
        }

        if (isset($expectedData['height'])) {
            if ($imageType->getHeight() != (int) $expectedData['height']) {
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
            throw new RuntimeException(sprintf('Fields %s are not identical', implode(', ', $errors)));
        }
    }

    /**
     * @When image type :imageTypeName should not exist.
     */
    public function assertImageTypeDoesNotExist(string $imageTypeName)
    {
        try {
            $this->getQueryBus()->handle(new GetImageTypeForEditing($this->getSharedStorage()->get($imageTypeName)));
            throw new RuntimeException(sprintf('Image type %s still exists', $imageTypeName));
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
