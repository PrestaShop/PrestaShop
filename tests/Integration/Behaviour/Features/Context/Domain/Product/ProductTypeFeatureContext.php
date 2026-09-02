<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;

class ProductTypeFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference type to :productType
     *
     * @param string $productReference
     * @param string $productType
     */
    public function updateProductType(string $productReference, string $productType): void
    {
        $productId = (int) $this->getSharedStorage()->get($productReference);

        try {
            $this->getCommandBus()->handle(new UpdateProductTypeCommand($productId, $productType));
        } catch (InvalidProductTypeException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that this action is allowed for :productType product only
     *
     * @param string $productType
     */
    public function assertLastErrorInvalidProductType(string $productType): void
    {
        $errorCode = null;
        switch ($productType) {
            case 'standard':
                $errorCode = InvalidProductTypeException::EXPECTED_STANDARD_TYPE;
                break;
            case 'pack':
                $errorCode = InvalidProductTypeException::EXPECTED_PACK_TYPE;
                break;
            case 'virtual':
                $errorCode = InvalidProductTypeException::EXPECTED_VIRTUAL_TYPE;
                break;
            case 'combinations':
                $errorCode = InvalidProductTypeException::EXPECTED_COMBINATIONS_TYPE;
                break;
            case 'single':
                $errorCode = InvalidProductTypeException::EXPECTED_NO_COMBINATIONS_TYPE;
                break;
        }
        $this->assertLastErrorIs(InvalidProductTypeException::class, $errorCode);
    }

    /**
     * @Then I should get error that the product is already associated to a pack
     */
    public function assertLastErrorForbiddenAssociations(): void
    {
        $this->assertLastErrorIs(InvalidProductTypeException::class, InvalidProductTypeException::EXPECTED_NO_EXISTING_PACK_ASSOCIATIONS);
    }
}
