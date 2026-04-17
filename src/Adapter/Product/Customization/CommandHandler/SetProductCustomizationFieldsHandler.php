<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\CommandHandler;

use CustomizationField;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Repository\CustomizationFieldRepository;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Update\ProductCustomizationFieldUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\SetProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField as CustomizationFieldDTO;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles @see SetProductCustomizationFieldsCommand using legacy object model
 */
#[AsCommandHandler]
class SetProductCustomizationFieldsHandler implements SetProductCustomizationFieldsHandlerInterface
{
    /**
     * @var CustomizationFieldRepository
     */
    private $customizationFieldRepository;

    /**
     * @var ProductCustomizationFieldUpdater
     */
    private $productCustomizationFieldUpdater;

    /**
     * @param CustomizationFieldRepository $customizationFieldRepository,
     * @param ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
     */
    public function __construct(
        CustomizationFieldRepository $customizationFieldRepository,
        ProductCustomizationFieldUpdater $productCustomizationFieldUpdater
    ) {
        $this->customizationFieldRepository = $customizationFieldRepository;
        $this->productCustomizationFieldUpdater = $productCustomizationFieldUpdater;
    }

    /**
     * {@inheritdoc}
     *
     * Creates, updates or deletes customization fields depending on differences of existing and provided fields
     */
    public function handle(SetProductCustomizationFieldsCommand $command): array
    {
        $shopConstraint = $command->getShopConstraint();
        if ($shopConstraint->getShopId()) {
            $shopId = $shopConstraint->getShopId();
        } elseif ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            $shopId = $shopConstraint->getShopIds()[0];
        } else {
            throw new InvalidShopConstraintException('Cannot handle this kind of ShopConstraint');
        }

        $productId = $command->getProductId();

        $customizationFields = [];
        foreach ($command->getCustomizationFields() as $providedCustomizationField) {
            $customizationFields[] = $this->buildEntityFromDTO($productId, $providedCustomizationField, $shopId);
        }
        $this->productCustomizationFieldUpdater->setProductCustomizationFields($productId, $customizationFields, $shopConstraint);

        return $this->customizationFieldRepository->getCustomizationFieldIds($productId);
    }

    /**
     * @param ProductId $productId
     * @param CustomizationFieldDTO $customizationFieldDTO
     * @param ShopId $shopId
     *
     * @return CustomizationField
     */
    private function buildEntityFromDTO(ProductId $productId, CustomizationFieldDTO $customizationFieldDTO, ShopId $shopId): CustomizationField
    {
        // Fetch existing customization field or create a new one
        if ($customizationFieldDTO->getCustomizationFieldId()) {
            $customizationField = new CustomizationField($customizationFieldDTO->getCustomizationFieldId(), null, $shopId->getValue());
        } else {
            $customizationField = new CustomizationField();
        }

        $customizationField->id_product = $productId->getValue();
        $customizationField->type = $customizationFieldDTO->getType();
        $customizationField->required = $customizationFieldDTO->isRequired();
        $customizationField->name = $customizationFieldDTO->getLocalizedNames();
        $customizationField->is_module = $customizationFieldDTO->isAddedByModule();

        return $customizationField;
    }
}
