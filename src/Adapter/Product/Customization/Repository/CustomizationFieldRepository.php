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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\Repository;

use CustomizationField;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Validate\CustomizationFieldValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotAddCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotUpdateCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Methods to access data storage for CustomizationField
 */
class CustomizationFieldRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var CustomizationFieldValidator
     */
    private $customizationFieldValidator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param CustomizationFieldValidator $customizationFieldValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        CustomizationFieldValidator $customizationFieldValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->customizationFieldValidator = $customizationFieldValidator;
    }

    /**
     * This getter without specified shop is useful when the product is only fetched for deletion.
     * In which case the shopId doesn't matter we don't care about multishop content since the entity is about to be deleted.
     *
     * @param CustomizationFieldId $fieldId
     *
     * @return CustomizationField
     *
     * @throws CoreException
     */
    public function get(CustomizationFieldId $fieldId): CustomizationField
    {
        /** @var CustomizationField $customizationField */
        $customizationField = $this->getObjectModel(
            $fieldId->getValue(),
            CustomizationField::class,
            CustomizationFieldNotFoundException::class
        );

        return $customizationField;
    }

    /**
     * @param CustomizationFieldId $fieldId
     * @param ShopId $shopId
     *
     * @return CustomizationField
     *
     * @throws CoreException
     */
    public function getForShop(CustomizationFieldId $fieldId, ShopId $shopId): CustomizationField
    {
        /** @var CustomizationField $customizationField */
        $customizationField = $this->getObjectModelForShop(
            $fieldId->getValue(),
            CustomizationField::class,
            CustomizationFieldNotFoundException::class,
            $shopId
        );

        return $customizationField;
    }

    /**
     * @param CustomizationField $customizationField
     * @param ShopId[] $shopIds
     * @param int $errorCode
     *
     * @return CustomizationFieldId
     *
     * @throws CoreException
     */
    public function add(CustomizationField $customizationField, array $shopIds, int $errorCode = 0): CustomizationFieldId
    {
        $this->customizationFieldValidator->validate($customizationField);
        $this->addObjectModelToShops($customizationField, $shopIds, CannotAddCustomizationFieldException::class, $errorCode);

        return new CustomizationFieldId((int) $customizationField->id);
    }

    /**
     * @param CustomizationField $customizationField
     * @param ShopId[] $shopIds
     *
     * @throws CannotUpdateCustomizationFieldException
     */
    public function update(CustomizationField $customizationField, array $shopIds): void
    {
        $this->customizationFieldValidator->validate($customizationField);
        $this->updateObjectModelForShops($customizationField, $shopIds, CannotUpdateCustomizationFieldException::class);
    }

    /**
     * @param CustomizationField $customizationField
     */
    public function delete(CustomizationField $customizationField): void
    {
        $this->deleteObjectModel($customizationField, CannotDeleteCustomizationFieldException::class);
    }

    /**
     * @param CustomizationField $customizationField
     */
    public function softDelete(CustomizationField $customizationField): void
    {
        $this->softDeleteObjectModel($customizationField, CannotDeleteCustomizationFieldException::class);
    }

    /**
     * Returns the list of customization associated to a product (only their IDs), by default soft deleted entities
     * are filtered.
     *
     * @param ProductId $productId
     * @param bool $includeSoftDeleted
     *
     * @return CustomizationFieldId[]
     */
    public function getCustomizationFieldIds(ProductId $productId, bool $includeSoftDeleted = false): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->addSelect('cf.id_customization_field')
            ->from($this->dbPrefix . 'customization_field', 'cf')
            ->where('cf.id_product = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        if (!$includeSoftDeleted) {
            $qb->andWhere('cf.is_deleted = 0');
        }

        return array_map(static function (array $customizationFieldId) {
            return new CustomizationFieldId((int) $customizationFieldId['id_customization_field']);
        }, $qb->execute()->fetchAllAssociative());
    }
}
