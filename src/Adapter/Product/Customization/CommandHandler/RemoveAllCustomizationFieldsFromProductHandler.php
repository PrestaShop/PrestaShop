<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Customization\Update\CustomizationFieldDeleter;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Update\ProductCustomizationFieldUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\RemoveAllCustomizationFieldsFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\RemoveAllCustomizationFieldsFromProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;

/**
 * Handles @see RemoveAllCustomizationFieldsFromProductCommand using legacy object model
 */
#[AsCommandHandler]
final class RemoveAllCustomizationFieldsFromProductHandler implements RemoveAllCustomizationFieldsFromProductHandlerInterface
{
    /**
     * @var CustomizationFieldDeleter
     */
    private $customizationFieldDeleter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductCustomizationFieldUpdater
     */
    private $productCustomizationFieldUpdater;

    /**
     * @param CustomizationFieldDeleter $customizationFieldDeleter
     * @param ProductRepository $productRepository
     * @param ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
     */
    public function __construct(
        CustomizationFieldDeleter $customizationFieldDeleter,
        ProductRepository $productRepository,
        ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
    ) {
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->productRepository = $productRepository;
        $this->productCustomizationFieldUpdater = $productCustomizationFieldUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllCustomizationFieldsFromProductCommand $command): void
    {
        $product = $this->productRepository->getProductByDefaultShop($command->getProductId());

        $customizationFieldIds = array_map(function (array $field): CustomizationFieldId {
            return new CustomizationFieldId((int) $field['id_customization_field']);
        }, $product->getCustomizationFieldIds());

        $this->customizationFieldDeleter->bulkDelete($customizationFieldIds);
        $this->productCustomizationFieldUpdater->refreshProductCustomizability($command->getProductId());
    }
}
