<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Customization\Repository\CustomizationFieldRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryHandler\GetProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Defines contract to handle @var GetProductCustomizationFields query
 */
#[AsQueryHandler]
final class GetProductCustomizationFieldsHandler implements GetProductCustomizationFieldsHandlerInterface
{
    /**
     * @var CustomizationFieldRepository
     */
    private $customizationFieldRepository;

    /**
     * @param CustomizationFieldRepository $customizationFieldRepository
     */
    public function __construct(
        CustomizationFieldRepository $customizationFieldRepository
    ) {
        $this->customizationFieldRepository = $customizationFieldRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductCustomizationFields $query): array
    {
        $fieldIds = $this->customizationFieldRepository->getCustomizationFieldIds($query->getProductId());

        $customizationFields = [];
        foreach ($fieldIds as $fieldId) {
            $customizationFields[] = $this->buildCustomizationField($fieldId, $query->getShopConstraint()->getShopId());
        }

        return $customizationFields;
    }

    /**
     * @param CustomizationFieldId $fieldId
     * @param ShopId $shopId
     *
     * @return CustomizationField
     */
    private function buildCustomizationField(CustomizationFieldId $fieldId, ShopId $shopId): CustomizationField
    {
        $fieldEntity = $this->customizationFieldRepository->getForShop($fieldId, $shopId);

        return new CustomizationField(
            $fieldId->getValue(),
            (int) $fieldEntity->type,
            $fieldEntity->name,
            (bool) $fieldEntity->required,
            (bool) $fieldEntity->is_module
        );
    }
}
